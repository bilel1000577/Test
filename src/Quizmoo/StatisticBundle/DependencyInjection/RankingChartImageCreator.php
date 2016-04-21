<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Quizmoo\StatisticBundle\DependencyInjection\QuestionImageCreator;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
class RankingChartImageCreator extends QuestionImageCreator
{
	protected function createCahrtData($question,$params,$em)
	{
		$results=Array();
        $answers = $question->getRankingQuestionAnswers();
        
        $tmp_results = Array();
        $filter = new FilterResults();

        $tmp_results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);


        $rank_names = Array();
        $rankIds = Array();
        $nbre=0;
        $categories = Array();
        $data = Array();
        $temp = Array();
        $i = 0 ;
        if(count($params)==0 ){
            foreach ($question->getAnswerOptions() as $rankName) {
                $categories[] = $rankName->getAnswerTitle();
                $freq = array_fill(0, count($question->getAnswerOptions()), 0);

                foreach ($answers as $answer) {
                    foreach ($answer->getRanks() as $rank){
                        if ($rank->getAnswerOption()->getAnswerTitle() == $rankName->getAnswerTitle() ){
                            $freq[$rank->getRank()-1]++;
                        } 
                    }
                }
                $i++ ;
                array_push($temp , $freq);
            }
        }else{
            foreach ($question->getAnswerOptions() as $rankName) {
            $categories[] = $rankName->getAnswerTitle();
            $freq = array_fill(0, count($question->getAnswerOptions()), 0);

            foreach ($answers as $answer) {
                foreach ($tmp_results as $key => $value) {
                   if ($answer->getAnswer()->getId() == $tmp_results[$key] ){
                        foreach ($answer->getRanks() as $rank){
                            if ($rank->getAnswerOption()->getAnswerTitle() == $rankName->getAnswerTitle() ){
                                $freq[$rank->getRank()-1]++;
                            } 
                        }
                    }
                }
            }
            $i++ ;
            array_push($temp , $freq);
        }

        }
        // magic 
        array_unshift($temp, null);
        $temp = call_user_func_array("array_map", $temp);
        
        foreach ($temp as $indice => $line ) {
             $data[] = Array("name"=>"rank".($indice+1),"data"=>$line);
        }

        $series = array(
                    array("name" => "Data", "data" => $data )

        );

        
        $bar = new Highchart();
        $bar->chart->renderTo('bar_chart_ranking');
        $bar->chart->type("column");
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Rank Options"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Occurrence"));
        $bar->yAxis->stackLabels(array('enabled'=> true, "style"=>array('fontWeight' => 'bold','color'=>' gray ' )));
        /*$bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));*/
        $bar->plotOptions->column(array("stacking"=>"normal"));
        // $bar->plotOptions->datalabels(array('enabled' => 'true','color'=>'white' ));
                    
        $bar->series($data);
        array_push($results, $bar);
       
        
       return $results;   
	}
}