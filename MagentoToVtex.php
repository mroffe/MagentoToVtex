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


//static Form mode

$_POST['referencias'] = "";

$_POST['name'] = $_POST['data']['name'];
$_POST['url'] = $_POST['data']['url'];
$_POST['magentouser'] = $_POST['data']['magentouser'];
$_POST['magentopass'] = $_POST['data']['magentopass'];
$_POST['dbhost'] = $_POST['data']['dbhost'];
$_POST['dbname'] = $_POST['data']['dbname'];
$_POST['userdb'] = $_POST['data']['userdb'];
$_POST['passdb'] = $_POST['data']['passdb'];
$_POST['referencias'] = $_POST['data']['referencias'];
$_POST['definidos'] = $_POST['data']['definidos'];
$_POST['produto-ou-sku'] = "produto-sku";
$_POST['type'] = $_POST['data']['type'];
$_POST['accountname'] = $_POST['data']['accountname'];
$_POST['vtexuser'] = $_POST['data']['vtexuser'];
$_POST['vtexpass'] = $_POST['data']['vtexpass'];
$_POST['vtexdepartament'] = $_POST['data']['vtexdepartament'];
$_POST['vtexcategory'] = $_POST['data']['vtexcategory'];
$_POST['vtexbrand'] = $_POST['data']['vtexbrand'];
$_POST['attributeid'] = $_POST['data']['attributeid'];
$do = $_POST['data']['do'];


$storeConfig = StoreConfig::withPost($_POST);

//$storeConfig = StoreConfig::withData($storeConfigData);

StoreConfigManager::setStoreConfig($storeConfig);

foreach(CustomerManager::getList() as $customer){

    CustomerManager::setCurrentCustomer($customer);
    CustomerManager::setStoreConfig($storeConfig);

  //BrandService::run();
  //CategoryService::run();
  if($do=="productservice"){
      ProductService::run();
  }
  //countSkus::run();
  //ProductCategoryService::run();
  if($do=="attributelist"){
      AttributeList::run();
  }
  if($do==""){
      echo "route not found.";
  }

}





