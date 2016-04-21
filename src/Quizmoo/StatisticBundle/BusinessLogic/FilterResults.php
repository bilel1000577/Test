<?php

namespace Quizmoo\StatisticBundle\BusinessLogic;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\MultipleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\PictorialQuestion;
use Quizmoo\QuestionnaireBundle\Entity\RatingScale;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\QuestionnaireBundle\Entity\SelectField;
class FilterResults {
  public function filtreAnswersPerChoiceAction($em,$questionnaireId,$params){
        $questions = $em->getRepository('QuizmooQuestionnaireBundle:Question')->getQuestions($questionnaireId);
        $results=array();
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
            }
        
            }
        }
        $res_arr = array_shift($results);
        foreach($results as $filter){
            if($filter != null)
             $res_arr = array_intersect($res_arr, $filter);
        }
        return $res_arr ;
    }
}