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

        return MagentoConnector::$sessions[StoreConfigManager::getName()];
    }

    public static function connect(){
        try{
            MagentoConnector::$ws = new SoapClient('https://'.StoreConfigManager::getUrl().'/api/v2_soap/?wsdl',array(
                'trace' => 1,
                'exception' => 0
            ));
        }catch(SoapFault $e){
            Logger::error('Falha ao recuperar WSDL do Webservice.',$e,null);
        }

        try{
            MagentoConnector::$sessions[StoreConfigManager::getName()] = MagentoConnector::$ws->login(StoreConfigManager::getMagentoUser(),StoreConfigManager::getMagentoPass());
        }catch (SoapFault $e){
            Logger::error('Falha ao autenticar no Webservice.',$e,MagentoConnector::$ws->__getLastRequest());
        }
    }

    public static function getImages($sku){

        $sql = "SELECT g.value, v.defaultimg FROM `catalog_product_entity_media_gallery` as g
JOIN `catalog_product_entity_media_gallery_value` as v
ON g.value_id = v.value_id
WHERE v.disabled = 0 AND
g.entity_id = $sku
ORDER BY v.position";

        $con=mysql_connect(StoreConfigManager::getDbHost(),StoreConfigManager::getDbUser(),StoreConfigManager::getDbPass()) or
        die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(StoreConfigManager::getDbName()));

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

        $con=mysql_connect(StoreConfigManager::getDbHost(),StoreConfigManager::getDbUser(),StoreConfigManager::getDbPass()) or
        die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(StoreConfigManager::getDbName()));

        $result = mysql_query($sql);

        $skuList = array();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $skuList[] = $row['child_id'];
        }

        mysql_free_result($result);

        return $skuList;

    }

    public static function getSkusToCount($sku){

        $sql = "SELECT * FROM `catalog_product_relation` WHERE `parent_id` = $sku";

        $con=mysql_connect(StoreConfigManager::getDbHost(),StoreConfigManager::getDbUser(),StoreConfigManager::getDbPass()) or
            die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(StoreConfigManager::getDbName()));

        $result = mysql_query($sql);

        $skuList = array();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $skuList[] = $row['value'];
        }

        mysql_free_result($result);

        return $skuList;

    }

    public static function getAttributeValue($optionId){

        $sql = "SELECT `value` FROM `eav_attribute_option_value` WHERE `option_id` = $optionId";

        $con=mysql_connect(StoreConfigManager::getDbHost(),StoreConfigManager::getDbUser(),StoreConfigManager::getDbPass()) or
            die("Could not connect: " . mysql_error());

        mysql_select_db(strtolower(StoreConfigManager::getDbName()));

        $result = mysql_query($sql);

        $skuList = array();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $skuList[] = $row['value'];
        }

        mysql_free_result($result);

        return $skuList;

    }
}