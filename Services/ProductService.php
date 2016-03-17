<?php

class ProductService {

    public static function run(){

        self::cleanLog();

        $begin = date('H:m:s');

        $productList = self::getActiveVisibleProducts();

       //print_r($productList);

        //exibe lista de produtos que nao serão integrados por ultrapassar o limite de sku
        /*echo "<br>Os produtos listados em vermelho não serão integrados:<br>";
        foreach($productList as $product){
            $total = self::getSkusForCount($product->product_id);
            if(count($total)>49){
                echo "<font color=red>Produto Referencia: ".$product->sku." Total de skus: ".count($total)."</font><br>";
            }
        }*/

        $i = 0;
        $ii = 0;
        $p = 1;

        $text = trim($_POST['referencias']);
        $textAr = explode("\n", $text);
        $textAr = str_replace("\r", "", $textAr);
        $textAr = array_filter($textAr, 'trim');

        $totalProd = count($productList);

        foreach($productList as $product){

            $i++;
            if($i<0){
                continue;
            }

            if($_POST['definidos']=="true"){
                if (in_array($product->sku, $textAr)){
                //$i++;

                $ii++;
                if($p<0){
                    $p++;
                    continue;
                }

                echo "<br><font color=blue>Produto ".$i." - Total de produtos: ".$totalProd." - " .date('H:m:s')."</font><br>";
                self::doLog("Produto ".$i." - ".$product->sku." - Total de produtos: ".$totalProd." - ".$p." de ".count($textAr));
                $p++;
                sleep(1);
                $productData = self::getProductInfo($product->product_id);

                $productEssence = Product::withData($productData);

                $currentProduct = self::getProductByRef($product->sku);

                if($currentProduct->ProductGetByRefIdResult){
                    $productEssence->Id = $currentProduct->ProductGetByRefIdResult->Id;
                    $productEssence->DepartmentId = $currentProduct->ProductGetByRefIdResult->DepartmentId;
                    $productEssence->CategoryId = $currentProduct->ProductGetByRefIdResult->CategoryId;
                    $productEssence->BrandId = $currentProduct->ProductGetByRefIdResult->BrandId;
                }

                $skuList = MagentoConnector::getSKUsForProduct($product->product_id);

                // Limita a quantidade de skus por produto
                if(count($skuList)>49){
                    echo "<p color=red>Referencia: ".$product->sku." tem mais de 50 skus. Não sera enviado.</p>";
                    self::doLog('Produto com mais de 50 skus, nao sera enviado. '.$product->sku);
                    continue;
                }

                self::createProductOnVtex($productEssence);

                //continue;

                //Remove as imagens do produto
                //self::removeImagesForSku($currentProduct->ProductGetByRefIdResult->Id);

                if($productEssence->isOrfan()){

                    $productEssence->Id = null;

                    $currentSku = self::getSkuByRefId($productEssence->RefId);

                    if($currentSku->StockKeepingUnitGetByRefIdResult)
                        $productEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                    $sku = self::createSKUforOrfanProduct($productEssence,$currentProduct->ProductGetByRefIdResult->Id);

                    $skuStockQty = self::getProductStock($productEssence->essence->product_id);

                    self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                    self::removeImagesForSku($sku);

                    $skuImages = MagentoConnector::getImages($productEssence->essence->product_id);

                    self::createImageforSku($sku, $skuImages);

                    self::activateSku($sku->StockKeepingUnitInsertUpdateResult->Id);
                }
                else{

                    if($_POST['produto-ou-sku'] == "produto-sku"){
                        foreach($skuList as $skuId){

                            $skuData = null;
                            $skuEssence = null;
                            $currentSku = null;
                            $sku = null;
                            $skuImages = null;

                            $skuData = self::getProductInfo($skuId);

                            if(!$skuData){continue;}

                            $skuEssence = Product::withSku($skuData);

                            $currentSku = self::getSkuByRefId($skuEssence->RefId);

                            if($currentSku->StockKeepingUnitGetByRefIdResult)
                                $skuEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                            sleep(1);
                            $sku = self::createSKUforProduct($skuEssence, $currentProduct->ProductGetByRefIdResult->Id);

                            if($product->type=='configurable'){
                                $skuStockQty = self::getProductStock($skuData->product_id);
                            }else{
                                $skuStockQty = self::getProductStock($productEssence->essence->product_id);
                            }

                            self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                            // insere especificacao

                            //Tamanho
                            //$especificationValue_1 = MagentoConnector::getAttributeValue($skuData->additional_attributes[3]->value);
                            //self::insertEspecification($currentSku->StockKeepingUnitGetByRefIdResult->Id, 'Tamanho', $especificationValue_1);

                            //Cor
                            //$especificationValue_2 = MagentoConnector::getAttributeValue($skuData->additional_attributes[2]->value);
                            //self::insertEspecification($currentSku->StockKeepingUnitGetByRefIdResult->Id, 'Cor', $especificationValue_2);

                            //TODO subir especificacoes

                            //$images = MagentoConnector::$ws->catalogProductAttributeMediaList(MagentoConnector::getActiveSession(), $product->sku, '1');

                            //self::removeImagesForSku($currentSku->StockKeepingUnitGetByRefIdResult->Id);

                            $skuImages = self::getImagesForSku($productEssence->essence->product_id);

                            self::createImageforSku($sku, $skuImages);

                            self::activateSku($currentSku->StockKeepingUnitGetByRefIdResult->Id);
                        }
                    }
                }
            }
            }else{


                    $ii++;
                    if($p<0){
                        $p++;
                        continue;
                    }

                    echo "<br><font color=blue>Produto ".$i." - Total de produtos: ".$totalProd." - " .date('H:m:s')."</font><br>";
                    self::doLog("Produto ".$i." - ".$product->sku." - Total de produtos: ".$totalProd." - ".$p." de ".count($textAr));
                    $p++;
                    sleep(1);
                    $productData = self::getProductInfo($product->product_id);

                    $productEssence = Product::withData($productData);

                    $currentProduct = self::getProductByRef($product->sku);

                    if($currentProduct->ProductGetByRefIdResult){
                        $productEssence->Id = $currentProduct->ProductGetByRefIdResult->Id;
                        $productEssence->DepartmentId = $currentProduct->ProductGetByRefIdResult->DepartmentId;
                        $productEssence->CategoryId = $currentProduct->ProductGetByRefIdResult->CategoryId;
                        $productEssence->BrandId = $currentProduct->ProductGetByRefIdResult->BrandId;
                    }

                    $skuList = MagentoConnector::getSKUsForProduct($product->product_id);

                    // Limita a quantidade de skus por produto
                    if(count($skuList)>49){
                        echo "<p color=red>Referencia: ".$product->sku." tem mais de 50 skus. Não sera enviado.</p>";
                        self::doLog('Produto com mais de 50 skus, nao sera enviado. '.$product->sku);
                        continue;
                    }

                    self::createProductOnVtex($productEssence);

                    //continue;

                    //Remove as imagens do produto
                    //self::removeImagesForSku($currentProduct->ProductGetByRefIdResult->Id);

                    if($productEssence->isOrfan()){

                        $productEssence->Id = null;

                        $currentSku = self::getSkuByRefId($productEssence->RefId);

                        if($currentSku->StockKeepingUnitGetByRefIdResult)
                            $productEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                        $sku = self::createSKUforOrfanProduct($productEssence,$currentProduct->ProductGetByRefIdResult->Id);

                        $skuStockQty = self::getProductStock($productEssence->essence->product_id);

                        self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                        self::removeImagesForSku($sku);

                        $skuImages = MagentoConnector::getImages($productEssence->essence->product_id);

                        self::createImageforSku($sku, $skuImages);

                        self::activateSku($sku->StockKeepingUnitInsertUpdateResult->Id);
                    }
                    else{

                        if($_POST['produto-ou-sku'] == "produto-sku"){
                            foreach($skuList as $skuId){

                                $skuData = null;
                                $skuEssence = null;
                                $currentSku = null;
                                $sku = null;
                                $skuImages = null;

                                $skuData = self::getProductInfo($skuId);

                                if(!$skuData){continue;}

                                $skuEssence = Product::withSku($skuData);

                                $currentSku = self::getSkuByRefId($skuEssence->RefId);

                                if($currentSku->StockKeepingUnitGetByRefIdResult)
                                    $skuEssence->Id = $currentSku->StockKeepingUnitGetByRefIdResult->Id;

                                sleep(1);
                                $sku = self::createSKUforProduct($skuEssence, $currentProduct->ProductGetByRefIdResult->Id);

                                if($product->type=='configurable'){
                                    $skuStockQty = self::getProductStock($skuData->product_id);
                                }else{
                                    $skuStockQty = self::getProductStock($productEssence->essence->product_id);
                                }

                                self::createStockForSku($sku->StockKeepingUnitInsertUpdateResult->Id, $skuStockQty);

                                // insere especificacao

                                //Tamanho
                                //$especificationValue_1 = MagentoConnector::getAttributeValue($skuData->additional_attributes[3]->value);
                                //self::insertEspecification($currentSku->StockKeepingUnitGetByRefIdResult->Id, 'Tamanho', $especificationValue_1);

                                //Cor
                                //$especificationValue_2 = MagentoConnector::getAttributeValue($skuData->additional_attributes[2]->value);
                                //self::insertEspecification($currentSku->StockKeepingUnitGetByRefIdResult->Id, 'Cor', $especificationValue_2);

                                //TODO subir especificacoes

                                $images = MagentoConnector::$ws->catalogProductAttributeMediaList(MagentoConnector::getActiveSession(), $product->sku, '1');

                                self::removeImagesForSku($currentSku->StockKeepingUnitGetByRefIdResult->Id);

                                $skuImages = self::getImagesForSku($productEssence->essence->product_id);

                                self::createImageforSku($sku, $skuImages);

                                self::activateSku($currentSku->StockKeepingUnitGetByRefIdResult->Id);
                            }
                        }
                    }
                }

        }

        echo "<font color=red><b>Inicio do processo: ".$begin."<br>Fim do processo: ". date('H:m:s') ."</b></font><br><br>";
        self::doLog("Process completed.");
    }

    private static function getImagesForSku($skuId){
        try{
            return MagentoConnector::getImages($skuId);
        }catch(Exception $e){
            Logger::alert('Falha ao pegar imagens do sku.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao pegar imagens do sku '.$skuId);

        }
    }

    private static function createImageforSku($sku,$imageList){
        
        $picture = 1;

        foreach($imageList as $image){

            sleep(2);

            $clientUrl = StoreConfigManager::getUrl();

            $imageUrl = "http://$clientUrl/media/catalog/product$image";

            $thumb = new Imagick();
            $thumb->readImage($imageUrl);
            $thumb->resizeImage(600,800,Imagick::FILTER_LANCZOS,1);
            $thumb->writeImage('imagem.jpg');
            $thumb->clear();
            $thumb->destroy();

            $imageUrl = $_SERVER['HTTP_REFERER'].'imagem.jpg';

            $img = preg_replace('/[^a-z0-9 -]+/', '', $sku->StockKeepingUnitInsertUpdateResult->Name);
            $img = str_replace(' ', '-', $img);
            
            if($picture==2){
                $label = "Hover";
            }else{
                $label = "Front";
            }

            $data = array('image' => array(
                'Url' => $imageUrl,
                'Label' => $label,
                'StockKeepingUnitId' => $sku->StockKeepingUnitInsertUpdateResult->Id

            ) ) ;

            try{
                VtexConnector::$ws->ImageInsertUpdate($data);
            }catch (Exception $e){
                Logger::alert('</br>Falha ao inserir imagem a sku.',$e,VtexConnector::$ws->__getLastRequest());
            }

        }

    }

    private static function removeImagesForSku($sku){

        try{
            VtexConnector::$ws->StockKeepingUnitImageRemove(array('stockKeepingUnitId' => $sku));
        }catch (Exception $e){
            Logger::alert('</br>Falha ao REMOVER imagem DO PRODUTO.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao REMOVER imagem DO sku. '.$sku);
        }
    }

    public static function getActiveVisibleProducts()
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
//                    'key' => 'category',
//                    'value' => array('key' => 'eq', 'value' => 'saias')
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
            self::doLog('Falha ao recuperar lista de produtos.');
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
            self::doLog('Falha ao recuperar produto.');
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
            Logger::alert('Falha ao recuperar info de produto no magento.',$e,MagentoConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar info de produto no magento.' );
        }

    }

    private static function getProductStock($productId){

        try{
            $productStock = MagentoConnector::$ws->catalogInventoryStockItemList(MagentoConnector::getActiveSession(),array($productId));
            return $productStock[0]->qty;
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar estoque de produto.',$e,MagentoConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar estoque de produto. ');
        }

    }

    private static function createProductOnVtex($product){

        try{
            //Add profiling
            Logger::info("<br>Criando/atualizando produto: " . $product->RefId . "<br>");
            self::doLog("<br>Criando/atualizando produto: " . $product->RefId . "<br>");

            VtexConnector::$ws->ProductInsertUpdate($product->toVtex());

        }catch(Exception $e){
            Logger::alert('Falha ao criar produto na VTEX.',$e,MagentoConnector::$ws->__getLastRequest().'<br><br>');
            self::doLog('Falha ao criar produto na VTEX.' );
        }

    }

    private static function createSKUforOrfanProduct($product,$productId){

        try{
            Logger::info("<br>Criando/atualizando SKU: " . $product->RefId. "<br>");

            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $product->RefId));
            if($result->StockKeepingUnitGetByRefIdResult){
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku($result->StockKeepingUnitGetByRefIdResult->Id));
            }else{
               return VtexConnector::$ws->StockKeepingUnitInsertUpdate($product->toOrfanSku($productId));
            }
        }catch(Exception $e){
            Logger::alert('<br>Falha ao criar SKU na VTEX.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('<br>Falha ao criar SKU na VTEX.');
        }
    }

    private static function createSKUForProduct($sku,$productId){
        try{
            Logger::info("<br>Criando/atualizando SKU: " . $sku->RefId . "<br>");

            self::doLog('Criando SKU na VTEX:'.$sku->RefId);

            $result = VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $sku->RefId));

            if($result->StockKeepingUnitGetByRefIdResult)
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId,$result->StockKeepingUnitGetByRefIdResult->Id));
            else
                return VtexConnector::$ws->StockKeepingUnitInsertUpdate($sku->toSku($productId));

        }catch(Exception $e){
            Logger::alert('<br>Falha ao criar SKU na VTEX.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao criar SKU na VTEX. sku: ');
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
            Logger::alert('<br>Falha ao inserir estoque em sku.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao inserir estoque no sku '.$sku);
        }

    }

    private static function getProductByRef($ref){
        try{
            return VtexConnector::$ws->ProductGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::alert('<br>Falha ao recuperar produto pela referencia',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar produto pela referencia '.$ref);
        }
    }

    private static function activateSku($skuId){
        try{
            return VtexConnector::$ws->StockKeepingUnitActive(array('idStockKeepingUnit' => $skuId));
            self::doLog('Ativando o sku '.$skuId);
        }catch(Exception $e){
            Logger::info('Falha ao ativar o sku.<br>');
            self::doLog('Falha ao ativar o sku '.$skuId);
        }
    }

    private static function getSkuByRefId($ref){
        try{
            return VtexConnector::$ws->StockKeepingUnitGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::alert('<br>Falha ao recuperar sku pela referencia',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar sku pela referencia '.$ref);
        }
    }

    private static function getSkusForCount($skuId){
        try{
            return MagentoConnector::getSkusToCount($skuId);
        }catch(Exception $e){
            Logger::alert('Falha ao contar os skus.',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Falha ao contar os skus.');
        }
    }

    private static function insertEspecification($skuId, $skuFieldName, $skuValue){
        try{
            self::doLog("Gravando especificacao no sku ".$skuId."-".$skuFieldName."-".$skuValue[0] );
            return VtexConnector::$ws->StockKeepingUnitEspecificationInsert(array('idSku' => $skuId, 'fieldName' => $skuFieldName, 'fieldValues' => array('string' => $skuValue)));
        }catch(Exception $e){
            Logger::alert('<br>Falha ao gravar especificacao',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog("Falha ao gravar especificacao no sku ".$skuId."-".$skuFieldName."-".$skuValue );
        }
    }

    function doLog($text){
        $filename = "integration.txt";
        $fh = fopen($filename, "a") or die("Could not open log file.");
        fwrite($fh, date("d-m-Y, H:i")." - $text\n") or die("Could not write file!");
        fclose($fh);
    }

    function cleanLog(){
        $filename = "integration.txt";
        $fh = fopen($filename, "w+") or die("Could not open log file.");
        fwrite($fh, date("d-m-Y, H:i")." - log anterior pagado \n") or die("Could not write file!");
        fclose($fh);
    }
}
