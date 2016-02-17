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
include 'Services/AttributeList.php';
include 'Services/ProductCategoryService.php';
include 'Services/ProductService.php';
include 'Services/countSkus.php';
include 'Managers/CustomerManager.php';
include 'Managers/StoreConfigManager.php';
include 'Helpers/Slug.php';

//Form

$_POST['referencias'] = "";




$_POST['name'] = "";
$_POST['url'] = "www..com.br";
$_POST['magentouser'] = "";
$_POST['magentopass'] = "";
$_POST['dbhost'] = "";
$_POST['dbname'] = "";
$_POST['userdb'] = "";
$_POST['passdb'] = "";
$_POST['definidos'] = "false";
$_POST['produto-ou-sku'] = "produto-sku";
$_POST['type'] = "configurable";
$_POST['accountname'] = "";
$_POST['vtexuser'] = "";
$_POST['vtexpass'] = "";
$_POST['vtexdepartament'] = "1";
$_POST['vtexcategory'] = "1";
$_POST['vtexbrand'] = "2000000";


$storeConfig = StoreConfig::withPost($_POST);

//$storeConfig = StoreConfig::withData($storeConfigData);

StoreConfigManager::setStoreConfig($storeConfig);

foreach(CustomerManager::getList() as $customer){

    CustomerManager::setCurrentCustomer($customer);
    CustomerManager::setStoreConfig($storeConfig);

  //BrandService::run();
  //CategoryService::run();
  ProductService::run();
  //countSkus::run();
  //ProductCategoryService::run();
  //AttributeList::run();

}





