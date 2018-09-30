<?php
 
namespace App\Classes;

use Illuminate\Http\Request;
use App\Models\CompanyAddress;

class StringClass {
    function str2alias($string) {

	$list = array('“', '”', "(", ")", "%", "`", "~", "!", "@", "#", "$", "^", "&", "*", "[", "]", "{", "}", ";", ":", "'", ",", ".", "<", ">", "?", "/", "|", '"', "_", "=", "+");

	$string = trim($string);

	$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

	$string = str_replace(" ", '-', $string);

	$string = stripslashes($string);



	for($i=0; $i<=sizeof($list); $i++) {

		@$string = str_replace($list[$i], '', $string);

	}

	$string = str_replace("--", '-', strtolower($string));



	return $string;

    }
	function getRomawi($bln){
                switch ($bln){
                    case 1: 
                        return "I";
                        break;
                    case 2:
                        return "II";
                        break;
                    case 3:
                        return "III";
                        break;
                    case 4:
                        return "IV";
                        break;
                    case 5:
                        return "V";
                        break;
                    case 6:
                        return "VI";
                        break;
                    case 7:
                        return "VII";
                        break;
                    case 8:
                        return "VIII";
                        break;
                    case 9:
                        return "IX";
                        break;
                    case 10:
                        return "X";
                        break;
                    case 11:
                        return "XI";
                        break;
                    case 12:
                        return "XII";
                        break;
                }
	}
}