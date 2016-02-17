<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vtexuser
 * Date: 10/1/15
 * Time: 1:07 PM
 * To change this template use File | Settings | File Templates.
 */
class countSkus
{
    public static function run(){

        $productList = ProductService::getActiveVisibleProducts();

        foreach($productList as $product){

            echo $product->id;

        }




    }

}
