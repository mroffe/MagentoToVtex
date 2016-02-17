<?php

class Logger {

    public static function info($message){

        print_r(StoreConfigManager::getName().": ".$message."\n");
    }

    public static function alert($message,Exception $exeception, $request){

        self::info($message);

        if($request)
            print_r($request."\n");

        if($exeception){
            print_r($exeception->getMessage()."\n");
        }
    }

    public static function error($message,Exception $exeception, $request){

        self::alert($message,$exeception,$request);

        throw $exeception;
    }
}