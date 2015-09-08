<?php
include 'Libs/httpful.phar';
include 'Libs/Logger.php';
include 'Connectors/MagentoConnector.php';
include 'Connectors/VtexConnector.php';
include 'Models/Customer.php';
include 'Models/Category.php';
include 'Models/Product.php';
include 'Models/Sku.php';
include 'Models/StoreConfig.php';
include 'Services/CategoryService.php';
include 'Services/ProductService.php';
include 'Managers/CustomerManager.php';
include 'Managers/StoreConfigManager.php';
include 'Helpers/Slug.php';

//Form
$storeConfig = StoreConfig::withPost($_POST);

//Manual run
//$storeConfigData = array(
//    'departmentId' => '',
//    'categoryId' => '',
//    'brandId' => '',
//    'name' => '',
//    'url' => '',
//    'dbHost' => '',
//    'dbName' => '',
//    'dbUser' => '',
//    'dbPass' => '',
//    'vtexUser' => '',
//    'vtexPass' => '',
//    'vtexAccountName' => '',
//    'magentoUser' => '',
//    'magentoPass' => '',
//    'productType' => ''
//);

//$storeConfig = StoreConfig::withData($storeConfigData);

StoreConfigManager::setStoreConfig($storeConfig);

foreach(CustomerManager::getList() as $customer){

    CustomerManager::setCurrentCustomer($customer);
    CustomerManager::setStoreConfig($storeConfig);

    //BrandService::run();
    //CategoryService::run();
    ProductService::run();
}





