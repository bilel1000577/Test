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
use Quizmoo\QuestionnaireBundle\Entity\SelectField;
use Quizmoo\QuestionnaireBundle\Entity\MatrixOfChoiceQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MatrixOfDropDownQuestion;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class MobileExportController extends Controller
{
	public function indexAction($chartType,$questionnaireId){

		$em = $this->getDoctrine()->getManager();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionnaireId);
        

        return $this->render('StatisticBundle::exportdiagramme.'.$this->get('session')->get('whichTwig','').'html.twig', array('questionText' =>$question->getQuestionText(),'chartType'=>$chartType,'questionnaireId'=>$questionnaireId));
         
    }
    //premuim functionalty 
    public function sendImageAsemailAction($chartType,$questionnaireId)
    {
    	$request = $this->get('request');
        
        $message = $request->getContent();
        $params=explode("&", $message);
        $imagesparams=explode("=",$params[0]);
        $imagetype=$imagesparams[1];
        $mailsparams=explode("=",$params[1]);
        $mails=explode(",",urldecode($mailsparams[1]));
        
        if(count($mails>0))
        {
            if($this->validateEmails($mails))
            {
                $imageCreator=$this->get('statistic.ImageCreatorService');
                $em = $this->getDoctrine()->getManager();
                $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionnaireId);
                $imgs=$imageCreator->createImage($chartType,$question,"300","2",$imagetype);
                if($imgs!=null)
                {
                    $this->sendEmails($mails,$imgs,$imagetype);
                    return $this->render('StatisticBundle::mail_sent.'.$this->get('session')->get('whichTwig','').'html.twig',array('id_msg'=>"1"));
                    //return new Response("Your chart has been sent successfully");
                }
                else
                {
                	return $this->render('StatisticBundle::mail_sent.'.$this->get('session')->get('whichTwig','').'html.twig',array('id_msg'=>"2"));
                    //return new Response("Something wrong happen!! please try again later");
                }
            }
            else
            	return $this->render('StatisticBundle::mail_sent.'.$this->get('session')->get('whichTwig','').'html.twig',array('id_msg'=>"3"));
                //return new Response("You should provide valid emails");
            
        }
        else
        	return $this->render('StatisticBundle::mail_sent.'.$this->get('session')->get('whichTwig','').'html.twig',array('id_msg'=>"4"));
            //return new Response("You should provide at least one valid email");
        
        
        
    	
    }	
    private function validateEmails($mails)
    {
        foreach ($mails as $key) {
            if(!filter_var(urldecode($key), FILTER_VALIDATE_EMAIL))
                return false;
        }
        return true;
    }
    private function sendEmails($mails,$imgs,$imagetype)
    {
        $type="";
        if($imagetype!="pdf")
                $type.="image/".$imagetype;
            else
                $type="pdf";
        foreach ($mails as $key) {
         $filename="chart.".$imagetype;
         $message = \Swift_Message::newInstance()
            ->setSubject("Graphiques envoyés sur portable")
            ->setFrom(array('noreply@quizmoo.com' => 'Quizmoo'))
            ->setTo(urldecode($key))
            ->setContentType("text/html")
            ->setBody(
                "test image"
            );
         foreach ($imgs as $img) {
             $attachment = \Swift_Attachment::newInstance()
                ->setFilename($filename)
                ->setContentType($type)
                ->setBody($img);
            $message->attach($attachment);
         }
        

        $this->get('mailer')->send($message);
        }
       
    }

    public function downloadChartAction(Request $request,$questionId,$chartType)
    {
        $imagetype="png";
        $imageCreator=$this->get('statistic.ImageCreatorService');
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
        $params = $this->getRequest()->query->all();
        $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
        $zip = new \ZipArchive();
        $zipName = substr($question->getQuestionText(),0,30).time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        $count = 1;
        foreach ($imgs as $key=> $img) {
                $im = imagecreatefromstring($img);
                if ($im !== false) {
                $zip->addFromString( $count.'.png', $img );
                $count = $count+1;
                }else {
                    echo 'An error occurred.';
                }
        }

        $zip->close();

        header('Content-Type', 'application/zip');
        header('Content-disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName);
        $response = array("code" => 100, "success" => true);
        return new Response(json_encode($response));
    }


    public function downloadAllChartAction(Request $request,$id)
    {
        ini_set('memory_limit', '-1');
        $em = $this->getDoctrine()->getManager();
        $params = $this->getRequest()->query->all();
        $questionnaire= $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->find($id);
        $imagetype="png";
        $imageCreator=$this->get('statistic.ImageCreatorService');
        $questions = $questionnaire->getQuestions();
        $all_imgs = array();
        $imgs = array();
        foreach ($questions as $key => $question) {
            if($question instanceof RankingQuestion){
                $chartType = "Mettre dans l'ordre";
                $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
            }else if ($question instanceof SelectField) {
                $chartType = "Choisir à partir d'une liste";
                $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
            }else if ($question instanceof MultipleChoice) {
                $chartType = "Question à choix multiple";
                $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
            }else if ($question instanceof RatingScale) {
                $chartType = "Echelle d'appréciation";
                $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
            }
            $all_imgs[substr($question->getQuestionText(),0,30)]=$imgs;
        }
        $zip = new \ZipArchive();
        $zipName = $questionnaire->getQuestionnaireName().time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        foreach ($all_imgs as $key1 => $value) 
        {
            $count = 1;
             foreach ($value as $key=> $img) {
                $im = imagecreatefromstring($img);
                if ($im !== false) {
                $zip->addFromString($key1.'/'.$count.'.png', $img );
                $count = $count+1;
                }else {
                    echo 'An error occurred.';
                }
            }
        } 
        $zip->close();
        header('Content-Type', 'application/zip');
        header('Content-disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName);
        $response = array("code" => 100, "success" => true);
        return new Response(json_encode($response));
    }
    
}








