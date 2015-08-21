<?php

class Brand {

    public $Name;
    public $KeyWords;
    public $Title;
    public $Description;

    public function __construct($name){
        $this->Name = $name;
        $this->KeyWords = $name;
        $this->Title = $name;
        $this->Description = $name;
    }

    public function toVtex(){
        return array(
            'Description' => $this->Description,
            'IsActive' => true,
            'Keywords' => $this->KeyWords,
            'Name' => $this->Name,
            'Title' => $this->Title
        );
    }
}