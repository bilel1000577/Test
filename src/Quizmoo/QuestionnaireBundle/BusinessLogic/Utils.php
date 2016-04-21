<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author test
 * 
 */
namespace Quizmoo\QuestionnaireBundle\BusinessLogic;
class Utils {
	
	static public function slugify($text)
    {
        // replace all non letters or digits by -
        $text = preg_replace('/\W+/', '-', $text);
 
        // trim and lowercase
        $text = strtolower(trim($text, '-'));
 
        return $text;
    }
	
	static public function isMobile()
   	{
   		return false;	
	}
}

?>
