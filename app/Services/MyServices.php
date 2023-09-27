<?php 

class MyServices
{

   public static function preventNullFromArray($arrayKey, $array,$return) 
   {

        if(array_key_exists($arrayKey,$array))
            return intval(strval($array[$arrayKey]))
        else return $return;
   }
	
}