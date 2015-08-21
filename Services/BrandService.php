<?php

class BrandService {

    public static function run(){

        $brand = VtexConnector::$ws->BrandInsertUpdate((new Brand(CustomerManager::getCurrentCustomer()->name))->toVtex());

        BrandManager::setCurrentBrand($brand);

    }
}