<?php

namespace Quizmoo\StatisticBundle\BusinessLogic;

class Statistic {

  private $question ;

  public function __construct($question){
    $this->question = $question ;
  }

  public function calculateRecapTable($em,$results){
        $scalesPerOption = Array();
        $recapArray = Array();
          $i = 0 ;
          foreach ($this->question->getAnswerOptions() as $answerOption) {
            if ($answerOption->getAnswerText() == 'ligne'){
              $scalesPerOption = $this->scalesForSingleAnswerOption($em,$answerOption,$this->question,$results);
              if(count($scalesPerOption)>0){
              $min = min($scalesPerOption);
              $max = max($scalesPerOption);
              $mean = round(array_sum($scalesPerOption)/count($scalesPerOption) , 2) ;
              $standarDeviation = round($this->standard_deviation($scalesPerOption),2);
              $nomberOfanswers = count($scalesPerOption);
              $m = ($nomberOfanswers+1)/2;
              if($m>1){
              if($m % 2 !=0){
                  sort($scalesPerOption);
                 $median = round(($scalesPerOption[$m-1]+$scalesPerOption[$m])/2);
              } else{
                  sort($scalesPerOption);
                  $median = $scalesPerOption[$m-1];   
              }
              }else{
                $median = $scalesPerOption[0];  
              }
              $recapArray[]= Array(trim($answerOption->getAnswerTitle()),$mean,$standarDeviation,$min,$max,$median,$nomberOfanswers);
         
            }
            }
          }
          array_push($recapArray, $this->question->getId());
         return $recapArray;
     }

     
     private function scalesForSingleAnswerOption($em,$answerOption,$question,$results){
      if(count($results)==0){
      foreach( $question->getRatingScaleAnswers() as $answer){
            
            foreach ($answer->getRates() as $rate) {

               //echo "anwerOptionRow() ".$rate->getAnswerOptionRow()->getAnswerTitle()." , title ".$answerOption->getAnswerTitle()."<br>";
              if ($rate->getAnswerOptionRow()->getAnswerTitle() == $answerOption->getAnswerTitle()){
                // echo $rate->getAnswerOptionCol()->getScale()."<br>";
                $scales[]= $rate->getAnswerOptionCol()->getScale();
              }
            }
      }
      }else{

      foreach( $question->getRatingScaleAnswers() as $answer){
        foreach ($results as $key => $value) {
                   if ($answer->getAnswer()->getId() == $results[$key] ){
            
                      foreach ($answer->getRates() as $rate) {

                         //echo "anwerOptionRow() ".$rate->getAnswerOptionRow()->getAnswerTitle()." , title ".$answerOption->getAnswerTitle()."<br>";
                        if ($rate->getAnswerOptionRow()->getAnswerTitle() == $answerOption->getAnswerTitle()){
                          // echo $rate->getAnswerOptionCol()->getScale()."<br>";
                          $scales[]= $rate->getAnswerOptionCol()->getScale();
                        }
                      }
                    }
                  }
      }

      }
      return $scales ;
     }


     public function mean($aValues ){
      return array_sum($aValues) / count($aValues);
     }
     public function standard_deviation($aValues, $bSample = false)
      {
        $fMean = array_sum($aValues) / count($aValues);
        $fVariance = 0.0;
        foreach ($aValues as $i)
        {
            $fVariance += pow($i - $fMean, 2);
        }
        $fVariance /= ( $bSample ? count($aValues) - 1 : count($aValues) );
        return (float) sqrt($fVariance);
      }

}