<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 11/19/15
 * Time: 9:17 PM
 * To change this template use File | Settings | File Templates.
 */
class AttributeList
{
    public static function run(){

        //GET PRODUCT ATTRIBUTE OPTION VALUES

        $attributeId = '137'; // 136 tam 137 cor

        $attributes = self::getAttributeValues($attributeId);

        echo '<font color=blue>Attribute-'.$attributeId.'</font><br>';

        foreach ($attributes as $attribute) {

            echo $attribute->label."<br>";

        }

    }

    public static function getAttributeValues($attributeId){
        return MagentoConnector::$ws->catalogProductAttributeOptions(MagentoConnector::getActiveSession(), $attributeId);
    }
}
