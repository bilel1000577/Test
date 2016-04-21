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


class ImagesMailController extends Controller
{
	public function indexAction($chartType,$questionnaireId, Request $request){


		//$user = $this->getUser();
		$mails=$request->request->get('mails');
		$png=$request->request->get('png');
		$jpg=$request->request->get('jpg');
		$pdf=$request->request->get('pdf');

		//export graph

		$filename = 'chart';

		$tempName = md5(rand());

		// allow no other than predefined types
		if (isset($png)|| $png!='') {
			$typeString = '-m image/png';
			$ext = 'png';

		} elseif (isset($jpg)|| $jpg!='') {
			$typeString = '-m image/jpeg';
			$ext = 'jpg';

		} elseif (isset($pdf)|| $pdf!='') {
			$typeString = '-m application/pdf';
			$ext = 'pdf';
		}

		$outfile = "c:/chart.pdf";


		header("Content-Disposition: attachment; filename=chart.pdf");
		
		echo file_get_contents($outfile);
		unlink($outfile);


		//Mail
		//$description = "You have received a questionnaire from ".$this->getUser()->getUsername();
		$description = "Vous avez reçu les résultats de sondage de la part de ".$this->getUser()->getUsername();
		
		$mail=  \trim($mails);
		$myArray = \explode(',', $mail);

		for($i = 0, $size = count($myArray); $i < $size; ++$i) {


	        $message = \Swift_Message::newInstance()
	            ->setSubject($chartType)
	            ->setFrom(array('noreply@quizmoo.com' => 'Quizmoo'))
	            ->setTo($myArray[$i])
	            ->attach(\Swift_Attachment::fromPath($outfile))
	            ->setContentType("text/html")
	            ->setBody(
	                $this->renderView(
	                    'QuizmooQuestionnaireBundle:SendMailer:Excelendmailer.'.$this->get('session')->get('whichTwig','').'html.twig',array('description'=>$description)
	    
	                )
	            );
    	}

    	$this->get('mailer')->send($message);

	    $return=array("responseCode"=>200, "message"=>"Mails sent with succes ");
	    $return=json_encode($return);
	    return new Response($return,200,array('Content-Type'=>'application/json'));
         
    }	
}

























