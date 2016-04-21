<?php

namespace Quizmoo\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\MultipleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\PictorialQuestion;
use Quizmoo\QuestionnaireBundle\Entity\RatingScale;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\QuestionnaireBundle\Entity\SelectField;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpFoundation\Request;
class FilterController extends Controller
{

    public function filtreAnswersPerChoiceAction(Request $request,$questionnaireId){
        $em = $this->getDoctrine()->getManager();
        $questions = $em->getRepository('QuizmooQuestionnaireBundle:Question')->getQuestions($questionnaireId);
        $results=array();
        $params = $this->getRequest()->query->all();
        if(count($params)>0){
        $char = ":";
        foreach ($questions as $question) {
            foreach ($params as $key=> $value) {   
                $first_pos = strpos($key,$char);
                $strKey = substr($key,$first_pos+1,strlen($key));
                $id_pos = strpos($strKey,$char);
                $id = substr($strKey,0,$id_pos);

                $last_pos = strrpos($key,$char);
                $order = substr($key,$last_pos+1,strlen($key));

                $pos = strpos($value,$char);
                $ansOptionId = substr($value,0,$pos);
                $scale = substr($value,$pos+1,strlen($value));
                
            if ($question instanceof MultipleChoice and substr($key,0,$first_pos)=="choiceId" and $question->getId()==$id) {
                $tmp_result = array();
                foreach( $question->getMultipleChoiceAnswers() as $answer){
                         foreach ($answer->getAnswerOptions() as $answerOption) {
                            
                            if($answerOption->getId()== $ansOptionId) { 
                 
                            array_push($tmp_result,$answer->getAnswer()->getId());
                            } 
                            
                        }
                        
                }  
                array_push($results, $tmp_result);
            }else if ($question instanceof SelectField and substr($key,0,$first_pos)=="optionId" and $question->getId()==$id) {
          
                $tmp_result = array();
                foreach( $question->getSelectFieldAnswers() as $answer){
                         foreach ($answer->getAnswerOptions() as $answerOption) {
                            
                            if($answerOption->getId()== $ansOptionId) { 
                 
                            array_push($tmp_result,$answer->getAnswer()->getId());
                            } 
                            
                        }
                        
                }  
                array_push($results, $tmp_result);
            }else  if($question instanceof RankingQuestion and substr($key,0,$first_pos)=="rankId" and $question->getId()==$id) {
         
                $tmp_result = array();
                foreach( $question->getRankingQuestionAnswers() as $answer){
                        foreach ($answer->getRanks() as $rank) {
                            
                            if($rank->getAnswerOption()->getId()== $ansOptionId) { 
                 
                            if($rank->getRank() == $order)
                                array_push($tmp_result,$answer->getAnswer()->getId());

                            } 
                            
                        }
                        
                } 
                array_push($results, $tmp_result);
            }else if ($question instanceof RatingScale and substr($key,0,$first_pos)=="rateId" and $question->getId()==$id) {
      
                $tmp_result = array();
                foreach( $question->getRatingScaleAnswers() as $answer){
                        foreach ($answer->getRates() as $rate) {
                          if($rate->getAnswerOptionCol()->getId()== $ansOptionId and $rate->getAnswerOptionRow()->getId()== $scale) { 
                            array_push($tmp_result,$answer->getAnswer()->getId());
                          }
                        }
                }
                array_push($results, $tmp_result);
            }

            }
        }

        $res_arr = array_shift($results);
        foreach($results as $filter){
            if($filter != null)
             $res_arr = array_intersect($res_arr, $filter);
        }
        $response = array("code" => 200, "success" => true,"Number" => count($res_arr));
        }else{
            $questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaireId);
                 
            $numberOfAnswers = $questionnaire->getNumberOfAnswers();
            $response = array("code" => 200, "success" => true,"Number" => $numberOfAnswers);
        }
        return new Response(json_encode($response));
        //return new Response(count($res_arr));
       

    }
}
