<?php

namespace Quizmoo\StatisticBundle\BusinessLogic;

class Statistic {

	private $question ;

	public function __construct($question){
		
		$this->question = $question ;
		
	}



	public function calculateRecapTable( ){
        $scalesPerOption = Array();
        $recapArray = Array();
          $i = 0 ;
          foreach ($this->question->getAnswerOptions() as $answerOption) {
            if ($answerOption->getAnswerText() == 'ligne'){
              $scalesPerOption = $this->scalesForSingleAnswerOption($answerOption,$this->question) ;
              $mean = array_sum($scalesPerOption)/count($scalesPerOption) ;
              $standarDeviation = round($this->standard_deviation($scalesPerOption),2);
              
              $recapArray[]= Array($answerOption->getAnswerTitle(),$mean,$standarDeviation);
            }

          }
          return $recapArray ;
     }

     
     private function scalesForSingleAnswerOption($answerOption,$question){
      $scales = Array();
      foreach( $question->getRatingScaleAnswers() as $answer){
            
            foreach ($answer->getRates() as $rate) {
               //echo "anwerOptionRow() ".$rate->getAnswerOptionRow()->getAnswerTitle()." , title ".$answerOption->getAnswerTitle()."<br>";
              if ($rate->getAnswerOptionRow()->getAnswerTitle() == $answerOption->getAnswerTitle()){
                // echo $rate->getAnswerOptionCol()->getScale()."<br>";
                $scales[]= $rate->getAnswerOptionCol()->getScale();
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