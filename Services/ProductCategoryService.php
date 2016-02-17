<?php

class ProductCategoryService {

    public static function run(){

       self::cleanlog();

       // GET MAGENTO CATEGORIES
       $categorias = self::getCategoriesInMagento();

       echo count($categorias->children[0]->children)." <font color=blue>Categories founded.</font><br>";

       $i = 1;

       foreach($categorias->children[0]->children as $categoria){

           echo $i."<font color=blue> - category: ".$categoria->name."</font><br>";

	  if($categoria->name!=$_GET['categoria']){continue;}

	  self::doLog('Listando produtos da categoria: '.$categoria->name);
           
          /*if($categoria->children){

               foreach($categoria->children as $children){

                   echo "<font color=blue>Children - ".$children->name."</font><br>";

                   //VTEX CATEGORY CHECK
                   $vtexCategory = self::getCategoryInVtex($children->name);

                   if ($vtexCategory->CategoryGetByNameResult->Name != ""){

                       //GET CATEGORY PRODUCTS
                       $visiveis = self::getActiveVisibleProductsOfCategory($children->category_id);

                       foreach($visiveis as $item){

                           //GET VTEX PRODUCT
                           $referencia = self::getProductByRef($item->sku);

                           if($referencia->ProductGetByRefIdResult->CategoryId){

                               $referencia->ProductGetByRefIdResult->CategoryId = $vtexCategory->CategoryGetByNameResult->Id;
                               $referencia->ProductGetByRefIdResult->DepartmentId = $vtexCategory->CategoryGetByNameResult->Id;

                               //DEFINE PRODUCT CATEGORY
                               self::createProductOnVtex($referencia);
                           }

                       }

                   }else{
                       echo "<br>Children - ".$children->name." not found in vtex<br>";
                   }
               }

           }else{*/

               echo "<font color=blue>Category - ".$categoria->name."</font><br>";

               //VTEX CATEGORY CHECK
               $vtexCategory = self::getCategoryInVtex($categoria->name);

               if ($vtexCategory->CategoryGetByNameResult->Name != ""){

                   //GET CATEGORY PRODUCTS
                   $visiveis = self::getActiveVisibleProductsOfCategory($categoria->category_id);

                   foreach($visiveis as $item){

                       //GET VTEX PRODUCT
                       $referencia = self::getProductByRef($item->sku);

                       if($referencia->ProductGetByRefIdResult->CategoryId){
                           $referencia->ProductGetByRefIdResult->CategoryId = $vtexCategory->CategoryGetByNameResult->Id;
                           $referencia->ProductGetByRefIdResult->DepartmentId = $vtexCategory->CategoryGetByNameResult->Id;

                           //DEFINE PRODUCT CATEGORY
                           self::createProductOnVtex($referencia);
                       }
                   }

               }else{
                   echo "<br>Category - ".$categoria->name." not found in vtex<br>";
               }

           //}

       }

    }

    public static function getActiveVisibleProductsOfCategory($categoryId)
    {
        try{
            return MagentoConnector::$ws->catalogCategoryAssignedProducts(MagentoConnector::getActiveSession(), $categoryId);
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar lista de produtos.',$e,MagentoConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar lista de produtos.');
        }

    }

    public static function getCategoriesInMagento()
    {
        try{
            return MagentoConnector::$ws->catalogCategoryTree(MagentoConnector::getActiveSession());
        }catch(Exception $e){
            Logger::alert('Falha ao recuperar lista de produtos.',$e,MagentoConnector::$ws->__getLastRequest());
            self::doLog('Falha ao recuperar lista de produtos.');
        }

    }

    private static function getCategoryInVtex($categoryName){

        try{
            return VtexConnector::$ws->CategoryGetByName(array('nameCategory' => $categoryName));
        }catch(Exception $e){
            Logger::alert('Falha ao pegar categoria VTEX.',$e,MagentoConnector::$ws->__getLastRequest().'<br><br>');
        }

    }

    private static function getProductByRef($ref){
        try{
            return VtexConnector::$ws->ProductGetByRefId(array('refId' => $ref));
        }catch(Exception $e){
            Logger::alert('<br>Falha ao recuperar produto pela referencia',$e,VtexConnector::$ws->__getLastRequest());
            self::doLog('Product not found - refid: '.$ref);
        }
    }

    private static function createProductOnVtex($product){

        try{
            self::doLog("Atualizando produto: " . $product->ProductGetByRefIdResult->RefId);

            VtexConnector::$ws->ProductInsertUpdate(
                array(
                    'productVO' => array(
                        'Id' => $product->ProductGetByRefIdResult->Id,
                        'BrandId' => $product->ProductGetByRefIdResult->BrandId,
                        'CategoryId' => $product->ProductGetByRefIdResult->CategoryId,
                        'DepartmentId' => $product->ProductGetByRefIdResult->DepartmentId,
                        'Name' => $product->ProductGetByRefIdResult->Name,
                        'Description' => $product->ProductGetByRefIdResult->Description,
                        'LinkId' => $product->ProductGetByRefIdResult->LinkId,
                        'RefId' => $product->ProductGetByRefIdResult->RefId,
                        'DescriptionShort' => $product->ProductGetByRefIdResult->DescriptionShort,
                        'MetaTagDescription' => $product->ProductGetByRefIdResult->MetaTagDescription,
                        'ShowWithoutStock' => false,
                        'Title' => $product->ProductGetByRefIdResult->Title,
                        'ListStoreId' => $product->ProductGetByRefIdResult->ListStoreId,
                        'IsVisible' => true,
                        'IsActive' => true,
                        'KeyWords' => $product->ProductGetByRefIdResult->KeyWords
                    )
                )
            );

        }catch(Exception $e){
            self::doLog('Falha ao atualizar produto na VTEX. Produto: '.$product->ProductGetByRefIdResult->RefId );
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
