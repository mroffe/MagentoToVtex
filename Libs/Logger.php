<?php

class Logger {
    //TODO deveria ter um info pra fazer o log das operações com sucesso
    public static function log($message,Exception $exeception = null,$request = null){

        print_r(CustomerManager::getCurrentCustomer()->name.": ".$message."\n");

//        if($request)
//            print_r($request."\n");
//
//        if($exeception){
//            print_r($exeception->getMessage()."\n");
//        }
    }

    public static function error($message,Exception $exeception = null,$request = null){

        print_r(CustomerManager::getCurrentCustomer()->name.": ".$message."\n");

        if($request)
            print_r($request."\n");

        if($exeception){
            print_r($exeception->getMessage()."\n");
            throw $exeception;
        }
    }
}