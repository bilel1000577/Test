<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Quizmoo\StatisticBundle\DependencyInjection\QuestionImageCreator;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Quizmoo\StatisticBundle\BusinessLogic\Statistic;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
class RatingScaleImageCreator extends QuestionImageCreator
{
	protected function createCahrtData($question,$params,$em)
	{
    $result=Array();


        $allAnswers =  Array();
        $statistiTool = new Statistic($question);
        $recapArray = Array();
        // setting the scales  
        $k = 0 ;
        $p = 0;

        foreach ($question->getAnswerOptions() as $answeroption) {
          if ($answeroption->getAnswerText() == 'level'){

            $answeroption->setScale($k+1);
              $k++;
          } else {
              $p ++ ;
          }
        }
        // finding the max of scales 
        // min and max of satisfaction
        $minScale  = $p *  1  ; 
        $maxScale  = $p * $k  ;
      
        if(count($params)==0 ){
          $tmp_results = Array();
            foreach( $question->getRatingScaleAnswers() as $answer){

            $summPerUser = 0 ;
            $i = 1 ;
            foreach ($answer->getRates() as $rate) {
              
              $summPerUser+= $rate->getAnswerOptionCol()->getScale();

            }
            $allAnswers[] = $summPerUser ; //tab de somme par user
            // creating the table of mean and SD 

            $recapArray = $statistiTool->calculateRecapTable($em,$tmp_results);
          }
      }else{
        $tmp_results = Array();
        $filter = new FilterResults();
        $tmp_results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);
          foreach( $question->getRatingScaleAnswers() as $answer){
             foreach ($tmp_results as $key => $value) {
                   if ($answer->getAnswer()->getId() == $tmp_results[$key] ){

                    $summPerUser = 0 ;
                    $i = 1 ;
                    foreach ($answer->getRates() as $rate) {
                      $summPerUser+= $rate->getAnswerOptionCol()->getScale();
                    }
                    $allAnswers[] = $summPerUser ; 
                  }
             }
            
          }

            $recapArray = $statistiTool->calculateRecapTable($em,$tmp_results);

      }
          
          //sort($allAnswers);
          asort($allAnswers);
          $freq = array_count_values($allAnswers); //freq de somme de scale de chaque rep
          
          if (! array_key_exists($minScale, $freq)){
            $freq[$minScale]= 0 ;
          }

          
         if (! array_key_exists($maxScale, $freq)){
            $freq[$maxScale]= 0 ;
          }
          ksort($freq);
           
          $xdata = array_keys($freq);
          
          $pdata = Array();

          $i =  0 ;
          foreach ($freq as $key => $value) {
            if ($key== $minScale) {
              $pdata[$i] = Array(''.$key,$value);
              
            } else if ($key == $maxScale) {
              $pdata[$i] = Array(''.$key,$value);
            } else {
              $pdata[$i] = Array(''.$key,$value);
            }
            
            $i++ ;
          }
 
           // creating the table of mean and SD 
         /* $statistiTool = new Statistic($question);
          $recapArray = Array();
          $recapArray = $statistiTool->calculateRecapTable($em,$choiceId);*/

          // get Scale Names
        $test = Array();


        $i=0;
        while($i < count($recapArray)-1){

          foreach ($recapArray[$i] as $key => $value){

            if($key == 0) {
                        array_push($test,$value);
            }
          }
          $i++;
        }
        $series = array(
                    array("name" => "Data", "data" => $pdata )

        );

        $bar = new Highchart();
        $bar->chart->renderTo('bar_chart_rating_scale'); 
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Sum_of_Items"));
        $bar->xAxis->categories($xdata);
        $bar->yAxis->title(array('text'  => "Frequency"));
        $bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));
        $bar->series(array(array('type' => 'column','name' =>"Frequency: ", 'data' => $pdata)));

        array_push($result, $bar);

         //scales


        $scales=Array();
        $couple = Array();
        $nbre=0;
        $RateAnswers = $em->getRepository('QuizmooRespondentBundle:RatingScaleAnswer')->getRatingChoices($question->getId());

        if(count($params)==0 ){
            foreach ($RateAnswers as $RateAnswer) {
              $rateChoices = $RateAnswer->getRates();

              foreach ($rateChoices as $rateChoice) {
                 
                  $answeroptionRow = $rateChoice->getAnswerOptionRow();
                  $answeroptionCol = $rateChoice->getAnswerOptionCol();
                  $scales=$this->putscale($scales,$answeroptionRow->getAnswerTitle());
                  array_push($couple,$answeroptionRow->getAnswerTitle().'#'.$answeroptionCol->getAnswerTitle());  
              }$nbre++;
           }
         }else{
          $results_tmp = Array();
          $filter = new FilterResults();
          $results_tmp = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);
             foreach ($RateAnswers as $RateAnswer) {
                foreach ($results_tmp as $key => $value) {
                   if ($RateAnswer->getAnswer()->getId() == $results_tmp[$key] ){
                      $rateChoices = $RateAnswer->getRates();

                      foreach ($rateChoices as $rateChoice) {
                         
                          $answeroptionRow = $rateChoice->getAnswerOptionRow();
                          $answeroptionCol = $rateChoice->getAnswerOptionCol();
                          $scales=$this->putscale($scales,$answeroptionRow->getAnswerTitle());
                          array_push($couple,$answeroptionRow->getAnswerTitle().'#'.$answeroptionCol->getAnswerTitle());  
                      }
                      $nbre++;
                    }
                }
            }
         }

        $names = array_unique($couple);
        $countValues = array_count_values($couple);
        foreach ($scales as $scale) {
          arsort($countValues);
          $data = Array(); 
          $pdata = Array();
          $i=0;
          $categories = Array();
          $char ="#";
        
          $pos =  0;
          
         foreach ($countValues as $key => $value) {

          if(strpos($key,$scale)!== false){
      
                  $data[$i]=Array($key,$value);
                
                  //$categories[$i]=Array($key);
                    $pos = strpos($key,$char);
                    $length = strlen($key);
                    $categories[$i]=substr($key,$pos+1,$length);
                    $pd = round(($value/$nbre)*100,2);
                    $ch= substr($key,$pos+1,$length);
                    $pdata[$i]=Array($ch.' : '.$pd.' %',$pd);

                    $i++;
           }
          }

           $series = array(
                      array("name" => "Data", "data" => $data )

          );
         
          /*bar chart */
          $bar = new Highchart();
          $bar_id = 'barChart'.uniqid(mt_rand(), true);
          $bar->chart->renderTo($bar_id); 
          $bar->title->text($scale);
          $bar->xAxis->title(array('text'  => "Answers"));
          $bar->xAxis->categories($categories);
          $bar->yAxis->title(array('text'  => "Numbre of answers"));
          $bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));
          $bar->series(array(array('type' => 'column','name' => $scale, 'data' => $data)));
          array_push($result, $bar);
          /* Pie Chart */
          $pie = new Highchart();
          $p_id = 'pieChart'.uniqid(mt_rand(), true);
          $pie->chart->renderTo($p_id);
          $pie->title->text($scale);
          $pie->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => true),
            'showInLegend'  => true,
            'stacking' =>'normal',
            'tooltip' => array('valueSuffix' => '%'),
            'dataLabels'=>array('enabled' => 'true')
          ));
          $pie->series(array(array('type' => 'pie','name' => $scale, 'data' => $pdata)));
          array_push($result, $pie);
        }
        
        return $result;
        
	}
  private function putscale($scales,$scale)
  {
    $add=true;
    foreach ($scales as $key) {
      if($key==$scale)
      {
        $add=false;
        break;
      }
    }
    if($add)
      array_push($scales, $scale);
    return $scales;
  }
	
}