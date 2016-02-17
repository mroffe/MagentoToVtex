<?php

class StoreConfig {

    public $departmentId;
    public $categoryId;
    public $brandId;
    public $name;
    public $url;
    public $listStoreId;
    public $showWithoutStock;
    public $dbHost;
    public $dbName;
    public $dbUser;
    public $dbPass;
    public $vtexUser;
    public $vtexPass;
    public $vtexAccountName;
    public $magentoUser;
    public $magentoPass;
    public $productType;

    public function __construct(){
        $this->listStoreId = 1;
        $this->showWithoutStock = true;
    }

    public static function withPost($post){
        $instance = new self();

        $instance->departmentId = $post['vtexdepartament'];
        $instance->categoryId = $post['vtexcategory'];
        $instance->brandId = $post['vtexbrand'];
        $instance->name = $post['name'];
        $instance->url = $post['url'];
        $instance->dbHost = $post['dbhost'];
        $instance->dbName = $post['dbname'];
        $instance->dbUser = $post['userdb'];
        $instance->dbPass = $post['passdb'];
        $instance->vtexUser = $post['vtexuser'];
        $instance->vtexPass = $post['vtexpass'];
        $instance->vtexAccountName = $post['accountname'];
        $instance->magentoUser = $post['magentouser'];
        $instance->magentoPass = $post['magentopass'];
        $instance->productType = $post['type'];

        return $instance;
    }

    public static function withData($data){
        $instance = new self();

        $instance->departmentId = $data['departmentId'];
        $instance->categoryId = $data['categoryId'];
        $instance->brandId = $data['brandId'];
        $instance->name = $data['name'];
        $instance->url = $data['url'];
        $instance->dbHost = $data['dbHost'];
        $instance->dbName = $data['dbName'];
        $instance->dbUser = $data['dbUser'];
        $instance->dbPass = $data['dbPass'];
        $instance->vtexUser = $data['vtexUser'];
        $instance->vtexPass = $data['vtexPass'];
        $instance->vtexAccountName = $data['vtexAccountName'];
        $instance->magentoUser = $data['magentoUser'];
        $instance->magentoPass = $data['magentoPass'];
        $instance->productType = $data['productType'];

        return $instance;
    }

}