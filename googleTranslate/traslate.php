<?php

require_once('vendor/autoload.php');

use Stichoza\GoogleTranslate\GoogleTranslate;

function translate($txt, $from, $to){
    $tr = new GoogleTranslate(); 
    $tr->setSource($from); 
    $tr->setSource(); 
    $tr->setTarget($to); 
    $txt = str_replace("&nbsp;", " ", $txt);
    $result =  $tr->translate($txt);
    return $result;
}
