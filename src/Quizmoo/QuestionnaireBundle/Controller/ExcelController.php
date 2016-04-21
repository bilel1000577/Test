<?php

/**
 * Description of ExcelController
 *
 * @author Bilel
 */
namespace Quizmoo\QuestionnaireBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use  Quizmoo\QuestionnaireBundle\BusinessLogic\ExcelDocument;

class ExcelController extends Controller {
	 public function exportAction($id){

	 	$em = $this->getDoctrine()->getManager();
	    
	    $questionnaire= $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->find($id);

	    $excelService = $this->get('xls.service_xls5');
	    
	    $documnent = new ExcelDocument($questionnaire,$excelService);
	    
	    return $documnent->export();
    }
}

?>
