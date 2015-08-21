<?php
/**
 * Created by PhpStorm.
 * User: rgoytacaz
 * Date: 12/03/15
 * Time: 10:38
 */

class VtexConnector {

    public static $ws;
    public static $api;

    public static function connect($customer){
        try{
            VtexConnector::$ws = new SoapClient('http://webservice-'.$_POST['accountname'].'.vtexcommerce.com.br/service.svc?wsdl',
                array(
                    'login'          => $_POST['vtexuser'],
                    'password'       => $_POST['vtexpass'],
                    'trace' => 1,
                    'exception' => 0
                ));
        }catch (SoapFault $e){
            Logger::error('Falha ao connectar ao Webservice.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }
}