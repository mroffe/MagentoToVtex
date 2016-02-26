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

        self::cleanLog();

        //GET PRODUCT ATTRIBUTE OPTION VALUES

        $attributeId = $_POST['attributeid']; // 136 tam 137 cor

        $attributes = self::getAttributeValues($attributeId);

        echo '<font color=blue>Attribute-'.$attributeId.'</font><br>';

        self::doLog("List attribute ".$attributeId);

        foreach ($attributes as $attribute) {

            echo $attribute->label."<br>";
            self::doLog($attribute->label);

        }

        self::doLog("Process completed.");

    }

    public static function getAttributeValues($attributeId){
        return MagentoConnector::$ws->catalogProductAttributeOptions(MagentoConnector::getActiveSession(), $attributeId);
    }

    function doLog($text){
        $filename = "integration.txt";
        $fh = fopen($filename, "a") or die("Could not open log file.");
        fwrite($fh, date("d-m-Y, H:i")." - $text\n") or die("Could not write file!");
        fclose($fh);
    }

    function cleanLog(){
        $filename = "integration.txt";
        $fh = fopen($filename, "w+") or die("Could not open log file.");
        fwrite($fh, date("d-m-Y, H:i")." - log anterior pagado \n") or die("Could not write file!");
        fclose($fh);
    }
}
