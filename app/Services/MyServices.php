<?php

namespace App\Services;

class MyServices
{

   public static function preventNullsFromArray($arrayKey, $array,$return='')
   {

        if(array_key_exists($arrayKey,$array))
            return strval($array[$arrayKey]);

        else return $return;
   }

   public static function zeroIfNullOrBlank($arrayKey, $array,$return='')
   {

        if(array_key_exists($arrayKey,$array))
            return floatval(strval($array[$arrayKey]));

        else return $return;
   }

}
