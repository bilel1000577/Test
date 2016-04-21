<?php

namespace Quizmoo\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Httpfoundation\Response;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
use Symfony\Component\HttpFoundation\Request;
class MultipleChoiceAnswerChartController extends Controller
{
    public function indexAction()
    {
        return $this->render('StatisticBundle:Default:index.html.twig');
    }
    public function chartAction()
    {   
        $series = array(
                    array("name" => "Data Serie Name", 
                    "data" => array(array('Firefox', 45.0),
                            array('IE', 26.8),
                            array('Chrome', 12.8),
                            array('Safari', 8.5),
                            array('Opera', 6.2),
                            array('Others', 0.7)))

        );
        // Chart
        /*$series = array(
            array("name" => "Data Serie Name",    "data" => array(1,2,4,5,6,3,8))
        );*/

        $ob = new Highchart();
        $ob->chart->renderTo('line_chart_multiple_choice');  // The #id of the div where to render the chart
        $ob->title->text('Chart Title');
        $ob->xAxis->title(array('text'  => "Horizontal axis title"));
        $ob->yAxis->title(array('text'  => "Vertical axis title"));
        $ob->series($series);

        return $this->render('StatisticBundle:Default:chart.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'chart' => $ob
        ));
    }

    public function testAction(){
        $ob = new Highchart();
        $ob->chart->renderTo('container');
        return $this->render('StatisticBundle:Default:test.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'chart' => $ob
        ));
    }

    public function getMCAResultAction(Request $request,$questionId) {
        $em = $this->getDoctrine()->getEntityManager();
        $mcanswers  = $em->getRepository('QuizmooRespondentBundle:MultipleChoiceAnswer:')->getMultipleChoiceAnswers($questionId);
        $option_names = Array();
        $optionIds = Array();
        $nbre=0;
        $params = $this->getRequest()->query->all();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
        $questionnaire = $question->getQuestionnaire();
        //var_dump($this->getRequest()->query->all());
        //var_dump($params);
        if(count($params)==0 ){
            foreach ($mcanswers as $mcanswer) {
               foreach ($mcanswer->getAnswerOptions() as $answeroption){
                    $nbre++;
                    array_push($optionIds,$answeroption -> getId());
                    $answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                    array_push($option_names,$answerOptionentity->getAnswerTitle());
               }
            }
        }else{
        $filter = new FilterResults();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
        $results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);
            foreach ($mcanswers as $mcanswer) {
                foreach ($results as $key => $value) {
                   if ($mcanswer->getAnswer()->getId() == $results[$key] ){
                   foreach ($mcanswer->getAnswerOptions() as $answeroption){
                        $answerOptionentity = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answeroption -> getId());
                       
                        array_push($optionIds,$answeroption -> getId());
                        array_push($option_names,$answerOptionentity->getAnswerTitle());
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

        /*bar chart */
        $bar = new Highchart();
        $bar->chart->renderTo('bar_chart_multiple_choice'); 
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Answer Options Title"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Numbre Of Answers"));
        $bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));
        $bar->series(array(array('type' => 'column','name' => $question->getQuestionText(), 'data' => $data)));

        return $this->render('StatisticBundle:Default:chart_multiple_choice_answer.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'pchart' => $pie,
            'bchart' => $bar,
            'questionnaire'=> $questionnaire,
            'question' => $question
        ));    

    }

public function getJsonMCAResultAction($questionId) {
        $em = $this->getDoctrine()->getEntityManager();
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
        
        return new Response(json_encode($pdata));

}


}
