<?php

class Product {

    public $Id;
    public $BrandId;
    public $CategoryId;
    public $DepartmentId;
    public $Name;
    public $Description;
    public $DescriptionShort;
    private $LinkId;
    public $RefId;
    public $Title;
    public $MetaTagDescription;
    public $KeyWords;
    public $IsActive;
    public $IsVisible;
    public $ListStoreId;
    public $ShowWithoutStock;
    public $Type;
    public $Visibility;
    public $essence;

    public function __construct(){}

    public function withData($data)
    {
        $instance = new self();

        $instance->essence = $data;
        $instance->Name = trim($data->name);
        $instance->Description = trim($data->description);
        $instance->DescriptionShort = trim($data->short_description);
        $instance->LinkId = trim($data->url_key);
        $instance->RefId = trim($data->sku);
        $instance->Title = trim($data->meta_title);
        $instance->MetaTagDescription = trim($data->meta_description);
        $instance->KeyWords = $instance->getAdditionalAttributes('referencia');
        $instance->IsActive = true;
        $instance->IsVisible = true;
        $instance->Type = $data->type;
        $instance->Visibility = $data->visibility;
        $instance->NomeEcommerce = $instance->getAdditionalAttributes('nome_ecommerce');
        $instance->Color = $instance->getAdditionalAttributes('ref_cor');
        $instance->Size = $instance->getAdditionalAttributes('ref_tamanho');

        return $instance;
    }

    public function withSku($sku){

        $instance = new self();

        $instance->essence = $sku;
        $instance->Name = trim($sku->name);
        $instance->LinkId = trim($sku->url_key);
        $instance->RefId = trim($sku->sku);
        $instance->KeyWords = $instance->getAdditionalAttributes('referencia');
        $instance->IsActive = true;
        $instance->IsVisible = true;
        $instance->Type = $sku->type;
        $instance->Visibility = $sku->visibility;
        $instance->NomeEcommerce = $instance->getAdditionalAttributes('nome_ecommerce');
        $instance->Color = $instance->getAdditionalAttributes('ref_cor');
        $instance->Size = $instance->getAdditionalAttributes('ref_tamanho');

        return $instance;
    }

    public function getAdditionalAttributes($attributeName){
        try{
            foreach($this->essence->additional_attributes as $attribute)
                if($attribute->key == $attributeName)
                    return $attribute->value;
        }catch(Exception $e){
            return null;
        }
    }

    public function getName(){
        return $this->NomeEcommerce ? $this->NomeEcommerce : $this->Name;
    }

    public function getBrandId(){
        return $this->BrandId ? $this->BrandId : StoreConfigManager::getBrandId();
    }

    public function getCategoryId(){
        return $this->CategoryId ? $this->CategoryId : StoreConfigManager::getCategoryId();
    }

    public function getDepartmentId(){
        return $this->DepartmentId ? $this->DepartmentId : StoreConfigManager::getDepartmentId();
    }

    public function getShowWithoutStock(){
        return $this->ShowWithoutStock ? $this->ShowWithoutStock : StoreConfigManager::getShowWithoutStock();
    }

    public function getListStoreId(){
        return $this->ListStoreId ? $this->ListStoreId : StoreConfigManager::getListStoreId();
    }

    public function getMetaTagDescription(){
        return $this->MetaTagDescription ? $this->MetaTagDescription : $this->Description;
    }

    public function getTitle(){
        return $this->Title ? $this->Title : $this->getName();
    }

    public function isOrfan(){
        if($this->Visibility == 4 && $this->Type == 'simple')
            return true;
        return false;
    }

    public function getLinkId(){
        return Slug::slugify($this->getName()."-".$this->RefId);
    }

    public function toVtex(){
        return $this->Id ? $this->updateData() : $this->createData();
    }

    public function createData(){
        return array(
            'productVO' => array(
                'BrandId' => $this->getBrandId(),
                'CategoryId' => $this->getCategoryId(),
                'DepartmentId' => $this->getDepartmentId(),
                'Name' => substr($this->getName(),0,150),
                'Description' => $this->Description,
                'LinkId' => $this->getLinkId(),
                'RefId' => $this->RefId,
                'DescriptionShort' => $this->DescriptionShort,
                'MetaTagDescription' => $this->getMetaTagDescription(),
                'ShowWithoutStock' => $this->getShowWithoutStock(),
                'Title' => substr($this->getTitle(),0,150),
                'ListStoreId' => $this->getListStoreId(),
                'IsVisible' => true,
                'IsActive' => true,
                'KeyWords' => $this->KeyWords
            )
        );
    }

    private function updateData(){
        return array_merge_recursive(
            $this->createData(),
            array('productVO' =>
                array('Id' => $this->Id)
            )
        );
    }

    public function getListPrice(){
        if(property_exists($this->essence, 'special_price') && $this->essence->special_price != $this->essence->price)
            return array(
                'stockKeepingUnitVO' => array(
                    'Price' => $this->essence->special_price,
                    'ListPrice' => $this->essence->price
                )
            );
        else
            return array(
                'stockKeepingUnitVO' => array(
                    'Price' => $this->essence->price,
                    'ListPrice' => $this->essence->price
                )
            );
    }

    public function toOrfanSku($id = null){
        if($id){
            return array_merge_recursive(
                $this->getListPrice(),
                array(
                    'stockKeepingUnitVO' => array(
                        'Name' => $this->Name,
                        'Height' => 1,
                        'Width' => 1,
                        'Length' => 1,
                        'WeightKg' => $this->essence->weight*1000,
                        'CubicWeight' => $this->essence->weight*1000,
                        'IsKit' => false,
                        'RefId' => $this->RefId,
                        'IsActive' => true,
                        'ModalId' => 1,
                        'ProductId' => $this->Id
                    )
                )
            );
        }
        else{
            return array_merge_recursive(
                $this->getListPrice(),
                array(
                    'stockKeepingUnitVO' => array(
                        'Id' => $id,
                        'Name' => $this->Name,
                        'Height' => 1,
                        'Width' => 1,
                        'Length' => 1,
                        'WeightKg' => $this->essence->weight*1000,
                        'CubicWeight' => $this->essence->weight*1000,
                        'IsKit' => false,
                        'RefId' => $this->essence->sku,
                        'IsActive' => true,
                        'ModalId' => 1,
                        'ProductId' => $this->Id
                    )
                )
            );
        }
    }

    public function toSku($productId, $id = null){
        if($id){
            return array_merge_recursive(
                $this->getListPrice(),
                array(
                    'stockKeepingUnitVO' => array(
                        'Name' => $this->Name,
                        'Height' => 1,
                        'Width' => 1,
                        'Length' => 1,
                        'WeightKg' => $this->essence->weight*1000,
                        'CubicWeight' => $this->essence->weight*1000,
                        'IsKit' => false,
                        'RefId' => $this->RefId,
                        'IsActive' => true,
                        'ModalId' => 1,
                        'ProductId' => $productId
                    )
                )
            );
        }
        else{
            return array_merge_recursive(
                $this->getListPrice(),
                array(
                    'stockKeepingUnitVO' => array(
                        'Id' => $id,
                        'Name' => $this->Name,
                        'Height' => 1,
                        'Width' => 1,
                        'Length' => 1,
                        'WeightKg' => $this->essence->weight*1000,
                        'CubicWeight' => $this->essence->weight*1000,
                        'IsKit' => false,
                        'RefId' => $this->essence->sku,
                        'IsActive' => true,
                        'ModalId' => 1,
                        'ProductId' => $productId
                    )
                )
            );
        }
    }
}