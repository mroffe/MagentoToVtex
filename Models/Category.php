<?php

class Category {

    public $Id;
    public $Name;
    private $Title;
    public $Description;
    public $Keywords;
    public $FatherCategoryId;

    public function __construct($data,$parent = null){
        $this->Name = $data->name;
        $this->FatherCategoryId = $parent;
        $this->Keywords = property_exists($data, 'meta_keywords') ? $data->meta_keywords : '';
        $this->Description = property_exists($data, 'meta_description') ? $data->meta_description : '';
        $this->Title = property_exists($data, 'meta_title') ? $data->meta_title : '';
    }

    public function getTitle(){
        return ($this->Title == '' ? $this->Name : $this->Title);
    }

    public function getKeywords(){
        return ($this->Keywords == '' ? $this->Name : $this->Keywords);
    }

    public function getDescription(){
        return ($this->Description == '' ? $this->Name : $this->Description);
    }

    public function toVtex(){
        return $this->Id ? $this->updateData() : $this->createData();
    }

    private function createData(){
        return array(
            'category' => array(
                'FatherCategoryId' => $this->FatherCategoryId,
                'Name' => substr($this->Name,0,100),
                'Title' => substr($this->getTitle(),0,150),
                'Keywords' => substr($this->getKeywords(), 0, 200),
                'Description' => $this->getDescription(),
                'IsActive' => true
            )
        );
    }

    private function updateData(){
        return array_merge_recursive(
            $this->createData(),
            array('category' =>
                array('Id' => $this->Id)
            )
        );
    }
}