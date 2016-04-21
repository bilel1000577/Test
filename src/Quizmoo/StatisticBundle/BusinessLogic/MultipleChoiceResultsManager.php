<?php

namespace Quizmoo\StatisticBundle\BusinessLogic;

class MultipleChoiceResultsManager {


static public function getReuslts($em ){
  $mcanswers  = $em->getRepository('QuizmooRespondentBundle:MultipleChoiceAnswer:')->getMultipleChoiceAnswers($questionId);
  $option_names = Array();
  $optionIds = Array();
  $nbre=0;
  foreach ($mcanswers as $mcanswer) {
     foreach ($mcanswer->getAnswerOptions() as $answeroption){
          $nbre++;
          array_push($optionIds,$answeroption -> getId());
          $answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
          array_push($option_names,$answerOptionentity->getAnswerTitle());
     }
 }

  $names = array_unique($option_names);
  $countValues = array_count_values($option_names);
  $pdata = Array();
  $i=0;
 
  foreach ($countValues as $key => $value) {
      $pd = ($value/$nbre)*100;
      $pdata[$i]=array('name' =>$key,'value'=>$pd);
      $i++;
         
  }
return $pdata ;
}

 
}
