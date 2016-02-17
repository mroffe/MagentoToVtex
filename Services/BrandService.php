<?php

class BrandService {

    public static function run(){

        //TODO verificar se já existe

        $brand = VtexConnector::$ws->BrandInsertUpdate((new Brand(StoreConfigManager::getName()))->toVtex());

        //TODO definir no storeconfig
        BrandManager::setCurrentBrand($brand);

    }
}