<?php

class CustomerManager {

    //TODO listagem completa de clientes
    private static $customerList = array();
    private static $currentCustomer;
    public static $storeConfig;

    public static function getList(){

        if(count(self::$customerList) < 1)
            self::$customerList = array(
                new Customer($_POST['name'],$_POST['url'],$_POST['user'],$_POST['pass'])
            );

        return self::$customerList;
    }

    public static function setCurrentCustomer($customer){
        self::$currentCustomer = $customer;
        MagentoConnector::connect($customer);
        VtexConnector::connect($customer);
    }

    public static function getCurrentCustomer(){
        return self::$currentCustomer;
    }

    public static function setStoreConfig($config){
        self::$storeConfig = $config;
    }
}