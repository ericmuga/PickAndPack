<?php 

namespace App\Services;

class MyServices
{

   public static function preventNullsFromArray($arrayKey, $array,$return='') 
   {

        if(array_key_exists($arrayKey,$array))
            return intval(strval($array[$arrayKey]));
        
        else return $return;
   }
	
}