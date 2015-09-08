<?php

class ProductService {

    public static function run(){

        $productList = self::getActiveVisibleProducts();

        foreach($productList as $product){
            $productData = self::getProductInfo($product->product_id);
            $productEssence = Product::withData($productData);

            $currentProduct = self::getProductByRef($productEssence->RefId);

            if($currentProduct->ProductGetByRefIdResult){
                $productEssence->Id = $currentProduct->ProductGetByRefIdResult->Id;
                $productEssence->DepartmentId = $currentProduct->ProductGetByRefIdResult->DepartmentId;
                $productEssence->CategoryId = $currentProduct->ProductGetByRefIdResult->CategoryId;
                $productEssence->BrandId = $currentProduct->ProductGetByRefIdResult->BrandId;
            }

            self::createProductOnVtex($productEssence);

            if($productEssence->isOrfan()){

                $productEssence->Id = null;

                $currentSku = self::getSkuByRefId($productEssence->RefId);

                if($currentSku->StockKeepingUnitGetByRefIdResult)
                    $productEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                $sku = self::createSKUforOrfanProduct($productEssence);

                $skuStockQty = self::getProductStock($productEssence->essence->product_id);

                self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                self::removeImagesForSku($sku);

                $skuImages = MagentoConnector::getImages($productEssence->essence->product_id);

                self::createImageforSku($sku, $skuImages);

                self::activateSku($sku->StockKeepingUnitInsertUpdateResult->Id);
            }
            else{
                $skuList = MagentoConnector::getSKUsForProduct($product->product_id);

                foreach($skuList as $skuId){

                    $skuData = null;
                    $skuEssence = null;
                    $currentSku = null;
                    $sku = null;
                    $skuImages = null;

                    $skuData = self::getProductInfo($skuId);

                    $skuEssence = Product::withSku($skuData);

                    $currentSku = self::getSkuByRefId($skuEssence->RefId);

                    if($currentSku->StockKeepingUnitGetByRefIdResult)
                        $skuEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                    $sku = self::createSKUforProduct($skuEssence, $currentProduct->ProductGetByRefIdResult->Id);

                    if($product->type=='configurable'){
                        $skuStockQty = self::getProductStock($skuData->product_id);
                    }else{
                        $skuStockQty = self::getProductStock($productEssence->essence->product_id);
                    }

                    self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                    //TODO subir especificacoes


                    self::removeImagesForSku($sku);

                    $skuImages = self::getImagesForSku($productEssence->essence->product_id);

                    self::createImageforSku($sku, $skuImages);

                    self::activateSku($sku->StockKeepingUnitInsertUpdateResult->Id);
                }
            }
        }
    }

    private static function getImagesForSku($skuId){
        try{
            return MagentoConnector::getImages($skuId);
        }catch(Exception $e){
            Logger::alert('Falha ao pegar imagens do sku.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function createImageforSku($sku,$imageList){

        foreach($imageList as $image){
            $clientUrl = StoreConfigManager::getUrl();
            $imageUrl = "http://$clientUrl/media/catalog/product$image";

            $data = array(
                'urlImage' => $imageUrl,
                'imageName' => $sku->StockKeepingUnitInsertUpdateResult->Name,
                'stockKeepingUnitId' => $sku->StockKeepingUnitInsertUpdateResult->Id

            );

            try{
                VtexConnector::$ws->ImageServiceInsertUpdate($data);
            }catch (Exception $e){
                Logger::alert('Falha ao inserir imagem a sku.',$e,VtexConnector::$ws->__getLastRequest());
            }

        }

    }

    private static function removeImagesForSku($sku){

        try{
            VtexConnector::$ws->ProductImageRemove(array('productId' => $sku->StockKeepingUnitInsertUpdateResult->Id));
        }catch (Exception $e){
            Logger::alert('Falha ao inserir imagem a sku.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function getActiveVisibleProducts()
    {
        $complexFilter = array(
            'complex_filter' => array(
                array(
                    'key' => 'visibility',
                    'value' => array('key' => 'eq', 'value' => '4')
                ),
                array(
                    'key' => 'status',
                    'value' => array('key' => 'eq', 'value' => '1')
                ),
//                array(
//                    'key' => 'referencia',
//                    'value' => array('key' => 'eq', 'value' => '43525')
//                ),
                array(
                    'key' => 'type',
                    'value' => array('key' => 'eq', 'value' => StoreConfigManager::getProductType())
                )
            )
        );

        try{
            return MagentoConnector::$ws->catalogProductList(MagentoConnector::getActiveSession(), $complexFilter);
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar lista de produtos.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function getProductInfo($productId){

        //Makes sure I fetch the correct product by ID.
        //ProductInfo returns ID or SKU with SKU having a priority
        //There are cases where SKU and ID may conflict fetching the wrong product
        $complexFilter = array(
            'complex_filter' => array(
                array(
                    'key' => 'status',
                    'value' => array('key' => 'eq', 'value' => '1')
                ),
                array(
                    'key' => 'product_id',
                    'value' => array('key' => 'eq', 'value' => $productId)
                )
            )
        );

        try{
            $product = MagentoConnector::$ws->catalogProductList(MagentoConnector::getActiveSession(), $complexFilter);
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar produto.',$e,MagentoConnector::$ws->__getLastRequest());
        }

        //With the product SKU it is garanteed to fetch the correct product
        $attributes = array(
            'attributes' => array(
                'name',
                'price',
                'special_price',
                'description',
                'short_description',
                'sku',
                'url_key',
                'weight',
                'meta_title',
                'meta_description',
                'visibility',
            ),
            'additional_attributes' => array(
                'nome_ecommerce',
                'ref_tamanho',
                'ref_cor',
                'medidas',
                'referencia',
                'manufacturer'
            )
        );

        try{
            return MagentoConnector::$ws->catalogProductInfo(MagentoConnector::getActiveSession(), $product[0]->sku, null, $attributes, null);
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar info de produto.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function getProductStock($productId){

        try{
            $productStock = MagentoConnector::$ws->catalogInventoryStockItemList(MagentoConnector::getActiveSession(),array($productId));
            return $productStock[0]->qty;
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar estoque de produto.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function createProductOnVtex($product){

        try{
            //Add profiling
            Logger::info("Criando/atualizando produto: " . $product->RefId);

            VtexConnector::$ws->ProductInsertUpdate($product->toVtex());
        }catch(Exception $e){
            Logger::alert('Falha ao criar produto na VTEX.',$e,MagentoConnector::$ws->__getLastRequest().'<br>');
        }

    }

    private static function createSKUforOrfanProduct($product){

        try{
            Logger::info("Criando/atualizando SKU: " . $product->RefId);

            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $product->RefId));
            if($result->StockKeepingUnitGetByRefIdResult)
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku($result->StockKeepingUnitGetByRefIdResult->Id));
            else
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku());

        }catch(Exception $e){
            Logger::alert('Falha ao criar SKU na VTEX.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function createSKUForProduct($sku,$productId){
        try{
            Logger::info("Criando/atualizando SKU: " . $sku->RefId);

            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $sku->RefId));

            if($result->StockKeepingUnitGetByRefIdResult)
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId,$result->StockKeepingUnitGetByRefIdResult->Id));
            else
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId));

        }catch(Exception $e){
            Logger::alert('Falha ao criar SKU na VTEX.',$e,MagentoConnector::$ws->__getLastRequest());
        }
    }

    private static function createStockForSku($sku, $qty){

        $dateOfAvailability = date("Y-m-d H:i:s");
        $dateOfAvailability = str_replace(" ", "T",$dateOfAvailability);

        $data = array(
            'wareHouseId' => "1_1",
            'itemId' => $sku,
            'availableQuantity' => $qty,
            'dateOfAvailability' => $dateOfAvailability
        );

        try{
            VtexConnector::$ws->WareHouseIStockableUpdateV3($data);
        }catch (Exception $e){
            Logger::alert('Falha ao inserir estoque em sku.',$e,VtexConnector::$ws->__getLastRequest());
        }

    }

    private static function getProductByRef($ref){
        try{
            return VtexConnector::$ws->ProductGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar produto pela referencia',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function activateSku($skuId){
        try{
            return VtexConnector::$ws->StockKeepingUnitActive(array('idStockKeepingUnit' => $skuId));
        }catch(Exception $e){
            Logger::info('Falha ao ativar o sku.');
        }
    }

    private static function getSkuByRefId($ref){
        try{
            return VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar sku pela referencia',$e,VtexConnector::$ws->__getLastRequest());
        }
    }
}