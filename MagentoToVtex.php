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

foreach(CustomerManager::getList() as $customer){

    $storeConfig = new StoreConfig(42,42,2000000,1,true);
    CustomerManager::setCurrentCustomer($customer);
    CustomerManager::setStoreConfig($storeConfig);

//    CategoryService::run();
    ProductService::run();

    echo $customer;

}





