<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Quizmoo\StatisticBundle\DependencyInjection\QuestionImageCreator;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
class SelectFieldImageCreator extends QuestionImageCreator
{
	protected function createCahrtData($question,$params,$em)
	{
		
		$results=Array();

        $filter = new FilterResults();
        $tmp_results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);

        $sfanswers  = $question->getSelectFieldAnswers();
        $option_names = Array();
        $optionIds = Array();
        $nbre=0;
        if(count($params)==0 ){
            foreach ($sfanswers as $sfanswer) {
               foreach ($sfanswer->getAnswerOptions() as $answeroption){
                    $nbre++;
                    array_push($optionIds,$answeroption -> getId());
                    $answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                    array_push($option_names,$answerOptionentity->getAnswerTitle());
               }
           }
        }else{
            foreach ($sfanswers as $sfanswer) {
               foreach ($tmp_results as $key => $value) {
                   if ($sfanswer->getAnswer()->getId() == $tmp_results[$key] ){
                       foreach ($sfanswer->getAnswerOptions() as $answeroption){
                            $nbre++;
                            array_push($optionIds,$answeroption -> getId());
                            $answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                            array_push($option_names,$answerOptionentity->getAnswerTitle());
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
                $data[$i]=Array($key,$value);
                $pd = round(($value/$nbre)*100,2);
                $pdata[$i]=Array($key.' : '.$pd.' %',$pd);
                if (is_string($key)) 
                { $categories[$i]=Array($key); }                
                $indice++;
                $i++;
        }
  
        $series = array(
                    array("name" => "Data", "data" => $data )

        );   
        /* Pie Chart */
         $pie = new Highchart();
        $pie->chart->renderTo('pie_chart_select_field');
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
        $bar->chart->renderTo('bar_chart_select_field'); 
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Answer Options Title"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Nombre d'occurences"));
        $bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));
        $bar->series(array(array('type' => 'column','name' => $question->getQuestionText(), 'data' => $data)));
        array_push($results, $bar);
        return $results;   

	}
}