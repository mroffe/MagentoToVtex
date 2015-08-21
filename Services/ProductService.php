<?php

class ProductService {

    public static function run(){

        $productList = self::getActiveVisibleProducts();

        foreach($productList as $product){
            $productData = self::getProductInfo($product->product_id);
            $productEssence = new Product($productData);

            $currentProduct = self::getProductByRef($productEssence->RefId);

            if($currentProduct->ProductGetByRefIdResult)
                $productEssence->Id = $currentProduct->ProductGetByRefIdResult->Id;

            self::createProductOnVtex($productEssence);

            if($productEssence->isOrfan()){

                $productEssence->Id = null;

                $currentSku = self::getSkuByRefId($productEssence->RefId);

                if($currentSku->StockKeepingUnitGetByRefIdResult)
                    $productEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                $sku = self::createSKUforOrfanProduct($productEssence);

                //TODO subir estoque
                //$skuStock = self::getProductStock($productEssence->essence->product_id);
                //self::createStockForSku($sku->Id);

                $skuImages = MagentoConnector::getImages($productEssence->essence->product_id);

                //TODO Checkimages antes de criar
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

                    $skuEssence = new Product($skuData);

                    $currentSku = self::getSkuByRefId($skuEssence->RefId);

                    if($currentSku->StockKeepingUnitGetByRefIdResult)
                        $skuEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                    $sku = self::createSKUforProduct($skuEssence, $currentProduct->ProductGetByRefIdResult->Id);

                    //TODO subir estoque
                    //$skuStock = self::getProductStock($productEssence->essence->product_id);
                    //self::createStockForSku($sku->Id);

                    //TODO subir especificacoes

                    $skuImages = MagentoConnector::getImages($productEssence->essence->product_id);

                    //TODO Checkimages antes de criar
                    self::createImageforSku($sku, $skuImages);

                    self::activateSku($sku->StockKeepingUnitInsertUpdateResult->Id);
                }
            }
        }
    }

    private static function createImageforSku($sku,$imageList){

        foreach($imageList as $image){
            $clientUrl = CustomerManager::getCurrentCustomer()->url;
            $imageUrl = "http://$clientUrl/media/catalog/product$image";

            $data = array(
                'urlImage' => $imageUrl,
                'imageName' => $sku->StockKeepingUnitInsertUpdateResult->Name,
                'stockKeepingUnitId' => $sku->StockKeepingUnitInsertUpdateResult->Id

            );

            try{
                VtexConnector::$ws->ImageServiceInsertUpdate($data);
            }catch (Exception $e){
                Logger::log('Falha ao inserir imagem a sku.',$e,VtexConnector::$ws->__getLastRequest());
            }

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
                array(
                    'key' => 'type',
                    'value' => array('key' => 'eq', 'value' => 'configurable')
                )
            )
        );

        try{
            return MagentoConnector::$ws->catalogProductList(MagentoConnector::getActiveSession(), $complexFilter);
        }catch(Exception $e){
            Logger::log('Falha ao recuperar lista de produtos.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function getProductInfo($productId){

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
            return MagentoConnector::$ws->catalogProductInfo(MagentoConnector::getActiveSession(), $productId, null, $attributes, null);
        }catch(Exception $e){
            Logger::log('Falha ao recuperar info de produto.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function getProductStock($productId){

        try{
            return MagentoConnector::$ws->catalogInventoryStockItemList(MagentoConnector::getActiveSession(), $productId);
        }catch(Exception $e){
            Logger::log('Falha ao recuperar estoque de produto.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function createProductOnVtex($product){

        try{
            $result = VtexConnector::$ws->ProductInsertUpdate($product->toVtex());
        }catch(Exception $e){
            Logger::log('Falha ao criar produto na VTEX.',$e,MagentoConnector::$ws->__getLastRequest());
        }

    }

    private static function createSKUforOrfanProduct($product){

        try{
            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $product->RefId));
            if($result->StockKeepingUnitGetByRefIdResult)
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku($result->StockKeepingUnitGetByRefIdResult->Id));
            else
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku());
        }catch(Exception $e){
            Logger::log('Falha ao criar SKU na VTEX.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function createSKUForProduct($sku,$productId){
        try{
            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $sku->RefId));

            if($result->StockKeepingUnitGetByRefIdResult)
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId,$result->StockKeepingUnitGetByRefIdResult->Id));
            else
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId));
        }catch(Exception $e){
            Logger::log('Falha ao criar produto na VTEX.',$e,MagentoConnector::$ws->__getLastRequest());
        }
    }

    private static function createStockForSku($sku){
        //TODO
    }

    private static function getProductByRef($ref){
        try{
            return VtexConnector::$ws->ProductGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::log('Falha ao recuperar produto pela referencia',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function activateSku($skuId){
        try{
            return VtexConnector::$ws->StockKeepingUnitActive(array('idStockKeepingUnit' => $skuId));
        }catch(Exception $e){
            Logger::log('Falha ao ativar o sku',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function getSkuByRefId($ref){
        try{
            return VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::log('Falha ao recuperar sku pela referencia',$e,VtexConnector::$ws->__getLastRequest());
        }
    }
}