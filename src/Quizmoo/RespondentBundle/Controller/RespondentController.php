<?php

namespace Quizmoo\RespondentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer;
use Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\Request;
use Quizmoo\QuestionnaireBundle\BusinessLogic\Utils;
use Quizmoo\RespondentBundle\Entity\Answer;

class RespondentController extends Controller {

	private $questionnaire_id;

	private $checkGlobalAnswer;

	public function getFormAction($hash) {

	$this->get("session")->set('whichTwig', Utils::isMobile() ? 'mobile.':'');

	// if the BC is requesting the questionnaire he could get it as much as he would
	$questionnaire = $this->getDoctrine()
			->getEntityManager()
			->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
			->findByHash($hash);

	if($questionnaire){

		//display send / multiple questions per page
		$nbAnswers = $questionnaire->getNumberOfAnswers();
		if($questionnaire->getDisplaySingleQuestion()){
			$preview =  'QuizmooRespondentBundle:Questionnaire:previewInSequences.';
		}else{
			$preview =  'QuizmooRespondentBundle:Questionnaire:preview.';
		}	

		$anonymous = true ;
		$securityContext = $this->container->get('security.context');
		if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') ){
		    
		    $anonymous = false ;
		} 

		if (! $anonymous ){
			$currentUser = $this->get('security.context')->getToken()->getUser();
			// if the request comes from the owner of the questionnaire 
			// he will always get the form 

			//if the request comes from an agent
			$em = $this->getDoctrine()->getManager();
			//$share = $em->getRepository('QuizmooQuestionnaireBundle:Share')->findShareBySurveyAndAgent($questionnaire,$currentUser);

			if ($currentUser->getId() == $questionnaire->getUser()->getId() ){
				
				return $this->render($preview.$this->get('session')->get('whichTwig','').'html.twig',
				 array('questionnaire' => $questionnaire,'respondent'=>'BC','nbAnswers' => $nbAnswers));

			} 
		
		} 

		$request = $this->getRequest();
		
		$value = unserialize($request->cookies->get('answered'));
		if (!$value){
		 	$value = array();
		 }

		if (in_array($hash, $value)){
			
			$response = $this->render('QuizmooRespondentBundle:Respondent:answer_questionnaire.'.$this->get('session')->get('whichTwig','').'html.twig');
			return $response ;
		} 
		
		if ($questionnaire->getState() != CLOSED_QUESTIONNAIRE){
			$response =  $this->render($preview.$this->get('session')->get('whichTwig','').'html.twig',
			 array('questionnaire' => $questionnaire ,'respondent'=>'ANON'));
			 
		} else {
			 $response =  $this->render('QuizmooRespondentBundle:Respondent:closed.'.$this->get('session')->get('whichTwig','').'html.twig');
		}

	}else{
        // You have to get your New link
        $response =  $this->render('QuizmooRespondentBundle:Respondent:linked.'.$this->get('session')->get('whichTwig','').'html.twig');
    }
	
		return $response ;
	}

	public function answerAction(Request $request, $questionnaire_id) {
		//Create a new answer object
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getManager();
		$questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire_id);
		if($request->getMethod() == 'POST')
		{
			$globalanswer = new Answer();
			$today =new \DateTime("NOW");
			$globalanswer->setTimespan($today);

			$this->questionnaire_id=$questionnaire_id;
			$this->checkGlobalAnswer= true;
			$this->get("session")->set('whichTwig', Utils::isMobile() ? 'mobile.':'');
			

			
			$cookie =unserialize($request->cookies->get('answered'));

			if (!$cookie){
			 	$cookie = array();
			}
			
			$questionIds = $request->request->get('questionId');

			if ( is_array($questionIds) ){

				foreach ($questionIds as $questionId) {
					
					$question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
					
					if (null == $question) continue ;
					
					
					if ($question instanceof SingleTextBoxQuestion) {
						
						$answerText = $request->request->get('singleTextBoxTextArea'.$questionId);
						
						$this->persistSingleTextBoxAnswer($question, $answerText,$globalanswer);
						
						
					}else if ($question instanceof MultipleChoice) {
						
						$checkedIds  = $request->request->get('selectMultipleChoice'.$questionId);
						if (null !== $checkedIds){
							$res=$this->persistMultipleChoiceAnswer($question, $checkedIds,$globalanswer);
							if($res!=null)
						 		return $res;
							
						}
		
					}else if ($question instanceof RankingQuestion) {

							$count=Count($question->getAnswerOptions());
							$ranks= array();
							for ($i=1; $i <=$count ; $i++) { 
								$answerOpid=$request->request->get($question->getId().$i);
								$ranks[$answerOpid]=$i;
							}
						 	$res=$this->persistRankingQuestion($question, $ranks,$globalanswer);
						 	if($res!=null)
						 		return $res;
					} 
				}
			}

			$answered = unserialize( $request->cookies->get('answered'));
			if($this->checkGlobalAnswer){
	        $questionnaire->setNumberOfAnswers($questionnaire->getNumberOfAnswers()+1);
	        $em->persist($questionnaire);
	        }
	        $em->flush();

    	
    	$type = $request->get('type');
    	if($questionnaire->getDisplaySingleQuestion()){
			$preview =  'QuizmooRespondentBundle:Questionnaire:previewInSequences.';
		}else{
			$preview =  'QuizmooRespondentBundle:Questionnaire:preview.';
		}


        if($type == 'BC'){
        	return $this->redirect($this->generateUrl('quizmoo_respondent', array('hash'=>$questionnaire->getHash())));
        }elseif ($type == 'ANON' || in_array($questionnaire->getHash(), $cookie)) {
        	$url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$questionnaire->getHash()));   
            $url = "http://".$_SERVER['SERVER_NAME'].$url;
			$response = $this->render('QuizmooRespondentBundle:Respondent:answer_questionnaire.'.$this->get('session')->get('whichTwig','').'html.twig',array('id'=>$questionnaire_id,'url'=>$url));
			array_push($cookie, $questionnaire->getHash());
			$cookieObject = new Cookie('answered', serialize($cookie), time() + 3600 * 24 * 7);
			$response->headers->setCookie($cookieObject);
        }
        
		return $response ;
		}
		return $this->redirect($this->generateUrl('quizmoo_respondent', array('hash'=>$questionnaire->getHash())));
	}
	
	private function persistRankingQuestion($question, $ranks,$globalanswer){
		$em = $this->getDoctrine()->getManager();
		if ($question) {
			$answer = new \Quizmoo\RespondentBundle\Entity\RankingAnswer();
			$answer->setRankingQuestion($question);
			$answer->setAnswer($globalanswer);
			$em->persist($answer);
			foreach ($ranks as $key => $value) {
				$answerOption = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($key);
				if(!$answerOption)
				{
					$logger = $this->get('logger');
					$ranksString =  implode(",",$ranks);
					$logger->info('Error while getting Answer option with id '.$key. 'ranks val are : '.$ranksString);
					return $this->somethingbad();
				}
					
				$rankEntity  = new \Quizmoo\RespondentBundle\Entity\Rank();
				$rankEntity->setRank($value);
					$rankEntity->setAnswerOption($answerOption);
					$rankEntity->setRankingAnswer($answer);
					$em->persist($rankEntity);
			}
			
			//$em->flush();
		}
		
	}

	private function persistSingleTextBoxAnswer($question,$answerText,$globalanswer) {
		if ($question) {
			$answer = new SingleTextBoxAnswer();
			$answer->setSingleTexBoxQuestion($question);
			$answer->setAnswerText($answerText);
			$em = $this->getDoctrine()->getManager();
			$answer->setAnswer($globalanswer);
			$globalanswer->addSingletextBoxAnsr($answer);
			$em->persist($answer);	
		}
	}

	private function persistMultipleChoiceAnswer($question, $checkedIds,$globalanswer) {

		if ($question) {
			$answer = new MultipleChoiceAnswer();
			$em = $this->getDoctrine()->getManager();

			if ($question->getIsSingle()){
				$answerOption = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($checkedIds);
				if(!$answerOption)
				{
					return $this->somethingbad();
				}
				$answer->addAnswerOption($answerOption);
			} else {
				foreach ($checkedIds as $answerOptionId) {
					$answerOption = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answerOptionId);
					if(!$answerOption)
					{
						return $this->somethingbad();
					}
					$answer->addAnswerOption($answerOption);
				
				}
				
			}

			$answer->setMultipleChoiceQuestion($question);
			$answer->setAnswer($globalanswer);
			$em->persist($answer);		
		}
	}

	private function somethingbad()
	{
		$this->checkGlobalAnswer = false;
		$em = $this->getDoctrine()->getManager();
		$questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($this->questionnaire_id);
		$url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$questionnaire->getHash())); 
		$url = "http://".$_SERVER['SERVER_NAME'].$url;
		$response = $this->render('QuizmooRespondentBundle:Respondent:answer_questionnaire.'.$this->get('session')->get('whichTwig','').'html.twig',array('id'=>$this->questionnaire_id,'url'=>$url));
		return $response;
	}

}
