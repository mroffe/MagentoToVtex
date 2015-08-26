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

    $storeConfig = new StoreConfig($_POST['vtexdepartament'],$_POST['vtexcategory'],$_POST['vtexbrand'],1,true);
    CustomerManager::setCurrentCustomer($customer);
    CustomerManager::setStoreConfig($storeConfig);

//    CategoryService::run();
    ProductService::run();

}





