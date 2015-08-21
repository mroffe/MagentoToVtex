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

    public function __construct($data)
    {
        $this->essence = $data;
        $this->Name = trim($data->name);
        $this->Description = trim($data->description);
        $this->DescriptionShort = trim($data->short_description);
        $this->LinkId = trim($data->url_key);
        $this->RefId = trim($data->sku);
        $this->Title = trim($data->meta_title);
        $this->MetaTagDescription = trim($data->meta_description);
        $this->KeyWords = $this->getAdditionalAttributes('referencia');
        $this->IsActive = true;
        $this->IsVisible = true;
        $this->Type = $data->type;
        $this->Visibility = $data->visibility;
        $this->NomeEcommerce = $this->getAdditionalAttributes('nome_ecommerce');
        $this->Color = $this->getAdditionalAttributes('ref_cor');
        $this->Size = $this->getAdditionalAttributes('ref_tamanho');
    }

    public function getAdditionalAttributes($attributeName){
        try{
            foreach($this->essence->additional_attributes as $attribute)
            {
                if($attribute->key == $attributeName)
                    return $attribute->value;
            }
        }catch(Exception $e){
            return null;
        }
    }

    public function getBrandId(){
        $this->BrandId ? $this->BrandId : CustomerManager::$storeConfig->BrandId;
    }

    public function getCategoryId(){
        $this->CategoryId ? $this->CategoryId : CustomerManager::$storeConfig->CategoryId;
    }

    public function getDepartmentId(){
        $this->DepartmentId ? $this->DepartmentId : CustomerManager::$storeConfig->DepartmentId;
    }

    public function getShowWithoutStock(){
        $this->ShowWithoutStock ? $this->ShowWithoutStock : CustomerManager::$storeConfig->ShowWithoutStock;
    }

    public function getListStoreId(){
        $this->ListStoreId ? $this->ListStoreId : CustomerManager::$storeConfig->ListStoreId;
    }

    public function isOrfan(){
        if($this->Visibility == 4 && $this->Type == 'simple')
            return true;
        return false;
    }

    public function toVtex(){
        return $this->Id ? $this->updateData() : $this->createData();
    }

    private function updateData(){
        return array_merge_recursive(
            $this->createData(),
            array('productVO' =>
                array('Id' => $this->Id)
            )
        );
    }

    public function getLinkId(){
        return strtolower(str_replace(" ","-",$this->Name));
    }

    public function createData(){
        return array(
            'productVO' => array(
                'BrandId' => CustomerManager::$storeConfig->BrandId,
                'CategoryId' => CustomerManager::$storeConfig->CategoryId,
                'DepartmentId' => CustomerManager::$storeConfig->DepartmentId,
                'Name' => substr($this->Name,0,150),
                'Description' => $this->Description,
                'LinkId' => $this->getLinkId(),
                'RefId' => $this->RefId,
                'DescriptionShort' => $this->DescriptionShort,
                'MetaTagDescription' => $this->MetaTagDescription,
                'ShowWithoutStock' => CustomerManager::$storeConfig->ShowWithoutStock,
                'Title' => substr($this->Title,0,150),
                'ListStoreId' => CustomerManager::$storeConfig->ListStoreId,
                'IsVisible' => true,
                'IsActive' => true,
                'KeyWords' => $this->KeyWords
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