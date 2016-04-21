<?php

namespace Quizmoo\StatisticBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
use Symfony\Component\HttpFoundation\Request;

class DownloadController extends Controller
{
	public function downloadAllChartsAction(Request $request,$id)
	{
		//get the questionnaire 
		$em = $this->getDoctrine()->getManager();
        $params = $this->getRequest()->query->all();
        $query_string = $_SERVER['QUERY_STRING'];
        $questionnaire= $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->find($id);
        //get the email of the current user
        $email=$this->get('security.context')->getToken()->getUser()->getEmail();
        //check if the number of answer is greater than 0
        if($questionnaire->getNumberOfAnswers()>0)
        {
        	//check if the questionnaire is closed or open
	        if($questionnaire->getState()==2 and count($params) == 0)
	        {
	        	//check if the charts exist already
	        	$filename="charts/$id/charts.zip";

	        	if(file_exists($filename))
	        	{   header('Content-Type', 'application/zip');
        			header('Content-disposition: attachment; filename="charts.zip"');
        			header('Content-Length: ' . filesize($filename));
        			readfile($filename);
        			$response = array("code" => 100, "success" => true);
        			return new Response(json_encode($response));
	        	}else{  $this->callAsyncTask($id,$email,true,$params,$query_string); }

	        }else{
                $this->callAsyncTask($id,$email,false,$params,$query_string);
                $response = array("code" => 200, "success" => true);
                return new Response(json_encode($response));
            }
            
        }else{ return new Response("you must have at least one response"); }
         
        
	}
	public function emailAllChartsAction($id,$email,$save)
	{
		ini_set('memory_limit', '-1');
		set_time_limit (0);
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
            }else if ($question instanceof MultipleChoice) {
                $chartType = "Question à choix multiple";
                $imgs=$imageCreator->createImage($chartType,$question,"2000","4",$imagetype,$params,$em);
            }
            $all_imgs[substr($question->getQuestionText(),0,30)]=$imgs;
        }
        if(!file_exists("charts/$id"))
            mkdir("charts/$id");
        $zip = new \ZipArchive();
        $zipName = "charts/$id/charts.zip";
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
                    die();
                }
            }
        } 
        $zip->close();

        $this->sendEmail($email,$zipName,$questionnaire->getQuestionnaireName());
		if($save=="false")
		{
			unlink($zipName);
		}
        return new Response("Mail sent secussefuly");
	}
	private function sendEmail($email,$file,$title)
	{
        
		 $message = \Swift_Message::newInstance()
            ->setSubject($title." Charts")
            ->setFrom(array('noreply@quizmoo.com' => 'Quizmoo'))
            ->setTo(urldecode($email))
            ->setContentType("text/html")
            ->setBody("Charts")
            ->attach(\Swift_Attachment::fromPath($file));
           
            $this->get('mailer')->send($message);
	}
	private function callAsyncTask($id,$email,$savefile=false,$params,$query_string)
	{
		$errno = '';
		$errstr = '';
		
		set_time_limit(0);
		
		$fp = fsockopen(SERVER, 80, $errno, $errstr, 3600);
		if (!$fp) {
		   echo "$errstr ($errno)<br />\n";
           die();
		   return false;
		}
		$url="";
		if($savefile){
            if(count($params)==0){
			$url=BASE_URL."{$id}/{$email}/true";
            }else{
            $url=BASE_URL."{$id}/{$email}/true?{$query_string}";
            }
		}else{
			if(count($params)==0){
            $url=BASE_URL."{$id}/{$email}/false";
            }else{
            $url=BASE_URL."{$id}/{$email}/false?{$query_string}";
            }
        }
		$out = "GET $url HTTP/1.1\r\n";
		$out .= "Host: ".SERVER."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		//echo "$out";
       
		stream_set_blocking($fp, false);
		stream_set_timeout($fp, 86400);
		fwrite($fp, $out);
		
		return true;
	}
	

}