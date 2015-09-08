<?php

class StoreConfigManager {

    private static $storeConfig;

    public static function setStoreConfig($config){
        self::$storeConfig = $config;
    }

    public static function getShowWithoutStock(){
        return self::$storeConfig->showWithoutStock;
    }

    public static function getListStoreId(){
        return self::$storeConfig->listStoreId;
    }

    public static function getDepartmentId(){
        return self::$storeConfig->departmentId;
    }

    public static function getCategoryId(){
        return self::$storeConfig->categoryId;
    }

    public static function getBrandId(){
        return self::$storeConfig->brandId;
    }

    public static function getName(){
        return self::$storeConfig->name;
    }

    public static function getUrl(){
        return self::$storeConfig->url;
    }

    public static function getDbHost(){
        return self::$storeConfig->dbHost;
    }

    public static function getDbName(){
        return self::$storeConfig->dbName;
    }

    public static function getDbUser(){
        return self::$storeConfig->dbUser;
    }

    public static function getDbPass(){
        return self::$storeConfig->dbPass;
    }

    public static function getVtexUser(){
        return self::$storeConfig->vtexUser;
    }

    public static function getVtexPass(){
        return self::$storeConfig->vtexPass;
    }

    public static function getVtexAccountName(){
        return self::$storeConfig->vtexAccountName;
    }

    public static function getMagentoUser(){
        return self::$storeConfig->magentoUser;
    }

    public static function getMagentoPass(){
        return self::$storeConfig->magentoPass;
    }

    public static function getProductType(){
        return self::$storeConfig->productType;
    }
}