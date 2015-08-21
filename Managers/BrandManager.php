<?php

class BrandManager {

    private static $currentBrand;

    public static function setCurrentBrand($brand){
        self::$currentBrand = $brand;
    }

    public static function getCurrentBrand(){
        return self::$currentBrand;
    }
}