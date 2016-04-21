<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Quizmoo\StatisticBundle\DependencyInjection\QuestionImageCreator;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
class MultipleChoiceImageCreator extends QuestionImageCreator
{
	protected function createCahrtData($question,$params,$em)
	{
		
		$results=Array();
        $mcanswers  = $question->getMultipleChoiceAnswers();
        $option_names = Array();
        $optionIds = Array();
        $nbre=0;
        /*foreach ($mcanswers as $mcanswer) {
           foreach ($mcanswer->getAnswerOptions() as $answeroption){
                $nbre++;
                array_push($optionIds,$answeroption -> getId());
                
                array_push($option_names,$answeroption->getAnswerTitle());
           }
       }*/

        if(count($params)==0 ){
            foreach ($mcanswers as $mcanswer) {
               foreach ($mcanswer->getAnswerOptions() as $answeroption){
                    $nbre++;
                    array_push($optionIds,$answeroption -> getId());
                    //$answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                    array_push($option_names,$answeroption->getAnswerTitle());
               }
            }
        }else{
        $tmp_results = Array();
        $filter = new FilterResults();
        $tmp_results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);
            foreach ($mcanswers as $mcanswer) {
                foreach ($tmp_results as $key => $value) {
                   if ($mcanswer->getAnswer()->getId() == $tmp_results[$key] ){
                   foreach ($mcanswer->getAnswerOptions() as $answeroption){
                        //$answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                        array_push($optionIds,$answeroption -> getId());
                        array_push($option_names,$answeroption->getAnswerTitle());
                        $nbre++;
            
                   }
                 }
               }
           }
        }
        $names = array_unique($option_names);
        $countValues = array_count_values($option_names);
        $data = Array(); 
        $pdata = Array();
        $i=0;
        $indice=1;
        $categories = Array();
        foreach ($countValues as $key => $value) {
                //echo "$key - <strong>$value</strong> <br />"; 
                $data[$i]=Array($key,$value);
                $pd = round(($value/$nbre)*100,2);
                $pdata[$i]=Array($key.' : '.$pd.' %',$pd);
                //$categories[$i]=Array($key);
                $categories[$i]=Array($key);
                $indice++;
                $i++;
        }

        $series = array(
                    array("name" => "Data", "data" => $data )

        );

        /* Pie Chart */
        $pie = new Highchart();
        $pie->chart->renderTo('pie_chart_multiple_choice');
        $pie->title->text($question->getQuestionText());
        $pie->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'showInLegend'  => true,
            'stacking' =>'normal',
            'tooltip' => array('valueSuffix' => '%'),
            /*'dataLabels'=>array('enabled' => 'true','distance'=> -30,'color'=>'white')*/
            'dataLabels'=>array('enabled' => 'true')
        ));
        $pie->series(array(array('type' => 'pie','name' => $question->getQuestionText(), 'data' => $pdata)));
        array_push($results, $pie);
        /*bar chart */
        $bar = new Highchart();
        $bar->chart->renderTo('bar_chart_multiple_choice'); 
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Answer Options Title"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Numbre Of Answers"));
        $bar->series(array(array('type' => 'column','name' => $question->getQuestionText(), 'data' => $data)));
        array_push($results, $bar);
        return $results;   

	}
}