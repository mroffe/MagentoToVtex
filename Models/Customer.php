<?php

class Customer {
    public $name;
    public $url;
    public $user;
    public $pass;

    public function __construct($name, $url, $user, $pass){
        $this->name = $name;
        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
    }
}