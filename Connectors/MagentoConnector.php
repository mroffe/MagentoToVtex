<?php
/**
 * Created by PhpStorm.
 * User: rgoytacaz
 * Date: 12/03/15
 * Time: 10:38
 */

class MagentoConnector {

    public static $ws;
    private static $sessions = [];

    public static function getActiveSession(){

        return MagentoConnector::$sessions[CustomerManager::getCurrentCustomer()->name];
    }

    public static function connect($customer){
        try{
            MagentoConnector::$ws = new SoapClient('http://'.$customer->url.'/api/v2_soap/?wsdl',array(
                'trace' => 1,
                'exception' => 0
            ));
        }catch(SoapFault $e){
            Logger::error('Falha ao connectar ao Webservice.',$e,null);
        }

        try{
            MagentoConnector::$sessions[$customer->name] = MagentoConnector::$ws->login($customer->user,$customer->pass);
        }catch (SoapFault $e){
            Logger::error('Falha ao autenticar no Webservice.',$e,MagentoConnector::$ws->__getLastRequest());
        }
    }

    public static function getImages($sku){

        $sql = "SELECT g.value FROM `catalog_product_entity_media_gallery` as g
JOIN `catalog_product_entity_media_g-allery_value` as v
ON g.value_id = v.value_id
WHERE v.disabled = 0 AND
g.entity_id = $sku";

        $con=mysql_connect("","","") or
        die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(CustomerManager::getCurrentCustomer()->name));

        $result = mysql_query($sql);

        $imageList = array();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $imageList[] = $row['value'];
        }

        mysql_free_result($result);

        return $imageList;

    }

    public static function getSKUsForProduct($id){

        $sql = "SELECT s.child_id FROM `catalog_product_relation` as s
WHERE s.parent_id = $id";

        $con=mysql_connect("","","") or
        die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(CustomerManager::getCurrentCustomer()->name));

        $result = mysql_query($sql);

        $skuList = array();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $skuList[] = $row['child_id'];
        }

        mysql_free_result($result);

        return $skuList;

    }
}