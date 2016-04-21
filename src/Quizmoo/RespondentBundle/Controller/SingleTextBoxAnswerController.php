<?php

namespace Quizmoo\RespondentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
use Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer;

/**
 * SingleTextBoxAnswer controller.
 *
 */
class SingleTextBoxAnswerController extends Controller
{
public function putsingleTextBoxAnswerAction(Request $request) {
    //http://localhost/quizmoo/trunk/web/app_dev.php/respondent/v1/put_singletextboxanswer
    //$string = '{"singletextboxquestion_id": 101, "attributes" : {"answerText" : "first test response" }}';
    if (is_object(json_decode($request->getContent()))) {
    $data = json_decode($request->getContent(),true);
   
    $singletextboxanswer  = new SingleTextBoxAnswer();
    $em = $this->getDoctrine()->getManager();
    
    $singletextboxquestion = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($data['singletextboxquestion_id']);
  
    $singletextboxanswer -> setSingleTexBoxQuestion($singletextboxquestion);
    $singletextboxanswer -> setAnswerText ($data['attributes']['answerText']);
   
    $em->persist($singletextboxanswer);
    $em->flush();
    return new Response('Success',200);
    }
    return new Response('invalid json');
 }

 public function getSingleTextBoxAnswersResultAction(Request $request,$questionId) {
        $em = $this->getDoctrine()->getEntityManager();
        $results = Array();
        $params = $this->getRequest()->query->all();
        $filter = new FilterResults();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
        $questionnaire = $question->getQuestionnaire();
        $results = $filter->filtreAnswersPerChoiceAction($em,$questionnaire->getId(),$params);
        $temps_results = $em->getRepository('QuizmooRespondentBundle:SingleTextBoxAnswer')->getAnswers($questionId);
        $singleTextBoxAnswers = array();
        if(count($params)==0 ){
          $singleTextBoxAnswers = $temps_results;
        }else{
          foreach ($temps_results as $key => $singleTextBoxAnswer) {
                 foreach ($results as $key => $value) {
                    if ($singleTextBoxAnswer->getAnswer()->getId() == $results[$key] ){
                    array_push($singleTextBoxAnswers, $singleTextBoxAnswer);
               }
             }
              
           }
        }
        return $this->render('QuizmooRespondentBundle:Default:single_text_box_answers.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'singleTextBoxAnswers' => $singleTextBoxAnswers,
            'question' => $question,
            'questionnaire' => $questionnaire
        ));  
        
    }

  public function getJsonSingleTextBoxAnswersResultAction($questionId) {

     $em = $this->getDoctrine()->getEntityManager();
     $singleTextBoxAnswers = $em->getRepository('QuizmooRespondentBundle:SingleTextBoxAnswer')->getJsonAnswers($questionId);
     $TextBoxAnswers_array = array();
     foreach ($singleTextBoxAnswers as $singleTextBoxAnswer) {
        $TextBoxAnswer_array = array('answer' => $singleTextBoxAnswer->getAnswerText());
        $TextBoxAnswers_array[] = $TextBoxAnswer_array;
     }
    return new Response(json_encode($TextBoxAnswers_array));
  }
 }