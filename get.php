#!/usr/bin/env php
<?php
require(__DIR__ . "/inc/common.php");

try {
    psGet::init($argv);
    $args=PsGet::args($argv);
    if (count($args)<2) {
        PsOut::error("Error! Enter object[s] you want to get.\n".basename(__FILE__)
                . " {".join("|",array_keys(psCli::$resources))."} id [property]...\n"
                . "Property '*' or empty properties means everything."
                . "\n");
    }
    
    $object=array_shift($args);
    $objects=psCli::upobject($object);
    psGet::readcfg(Array("global","get",$objects));
    $objectid = array_shift($args);
    $properties = array_flip($args);
    if (sizeof($properties)==0) {
        $properties=Array("*" => 1);
    }
    $webService = new PrestaShopWebservice(psList::$shop_url, psList::$shop_key, false);
    $opt = array('resource' => $objects);
    psOut::progress("Getting data for $objects($object)...");
    psOut::begin($object);
    $opt['id'] = $objectid;
    $xml = $webService->get($opt);
    $rowobject = json_decode(json_encode($xml->children()->$object));
    $rowdata=psGet::getValues($rowobject,$properties);
    psOut::write(Array(
            0 => $rowdata
            ));
    psOut::end($object);
} catch (PrestaShopWebserviceException $e) {
    // Here we are dealing with errors
    $trace = $e->getTrace();
    PsOut::error('Other error<br />' . $e->getMessage());
}


