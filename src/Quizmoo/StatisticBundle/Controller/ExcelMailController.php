<?php


namespace Quizmoo\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\MultipleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\PictorialQuestion;
use Quizmoo\QuestionnaireBundle\Entity\RatingScale;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\QuestionnaireBundle\Entity\CommentBox;
use Quizmoo\QuestionnaireBundle\Entity\MatrixOfChoiceQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MatrixOfDropDownQuestion;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use  Quizmoo\QuestionnaireBundle\BusinessLogic\ExcelDocument;


class ExcelMailController extends Controller
{
	public function indexAction($questionnaireId, Request $request){

		$id =  $questionnaireId;

    	$questionnaire= $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->find($id);

		//$user = $this->getUser();
		$mails=$request->request->get('mails');
		
		//$description = "You have received a questionnaire from ".$this->getUser()->getUsername();
		$description = "Vous avez reçu les résultats de sondage sous format Excel de la part de ".$this->getUser()->getUsername();

		

		$excelService = $this->get('xls.service_xls5');
		    
		$documnent = new ExcelDocument($questionnaire,$excelService,$questionnaire->getQuestionnaireName());

		
		
		$mail=  \trim($mails);
		$myArray = \explode(',', $mail);
		$path=$documnent->getExcelDoc();
		for($i = 0, $size = count($myArray); $i < $size; ++$i) {

			
	        $message = \Swift_Message::newInstance()
	            ->setSubject($questionnaire->getQuestionnaireName())
	            ->setFrom(array('noreply@quizmoo.com' => 'Quizmoo'))
	            ->setTo($myArray[$i])
	            ->attach(\Swift_Attachment::fromPath($path))
	            ->setContentType("text/html")
	            ->setBody(
	                $this->renderView(
	                    'QuizmooQuestionnaireBundle:SendMailer:Excelendmailer.mobile.html.twig',array('description'=>$description)
	    
	                )

	            );
	            $this->get('mailer')->send($message);

    	}

    	unlink($path);
	    $return=array("responseCode"=>200, "message"=>"Mails sent with succes ");
	    $return=json_encode($return);
	   	
	    return new Response($return,200,array('Content-Type'=>'application/json'));


         
    }	
}
