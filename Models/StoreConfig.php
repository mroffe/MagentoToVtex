<?php

class StoreConfig {

    public $DepartmentId;
    public $CategoryId;
    public $BrandId;
    public $ListStoreId;
    public $ShowWithoutStock;

    public function __construct($departmentId, $categoryId, $brandId, $listStoreId, $showWithoutStock)
    {
        $this->DepartmentId = $departmentId;
        $this->CategoryId = $categoryId;
        $this->BrandId = $brandId;
        $this->ListStoreId = $listStoreId;
        $this->ShowWithoutStock = $showWithoutStock;
    }

}