<?php

namespace Quizmoo\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Httpfoundation\Response;
use Quizmoo\StatisticBundle\BusinessLogic\FilterResults;
use Symfony\Component\HttpFoundation\Request;
class RankingAnswerChartController extends Controller
{

	 public function getRankingAnswerResultAction(Request $request,$questionId) {
        $em = $this->getDoctrine()->getEntityManager();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
        $answers = $question->getRankingQuestionAnswers();
        $questionnaire = $question->getQuestionnaire();
        $rank_names = Array();
        $rankIds = Array();
        $nbre=0;
        $categories = Array();
        $data = Array();
        $temp = Array();
        $i = 0 ;
        $params = $this->getRequest()->query->all();
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
            $filter = new FilterResults();
            $results = $filter->filtreAnswersPerChoiceAction($em,$question->getQuestionnaire()->getId(),$params);
            foreach ($question->getAnswerOptions() as $rankName) {
            $categories[] = $rankName->getAnswerTitle();
            $freq = array_fill(0, count($question->getAnswerOptions()), 0);

            foreach ($answers as $answer) {
                foreach ($results as $key => $value) {
                   if ($answer->getAnswer()->getId() == $results[$key] ){
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

        
       return $this->render('StatisticBundle:Default:chart_ranking_answer.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'bchart' => $bar,
            'questionnaire'=> $questionnaire,
            'question' => $question
        ));   
      }



       public function getJsonRankingAnswerResultAction($questionId) {
        $em = $this->getDoctrine()->getEntityManager();
        $answers  = $em->getRepository('QuizmooRespondentBundle:RankingAnswer')->getRankingChoices($questionId);
        $rank_names = Array();
        $rankIds = Array();
        $nbre=0;
        foreach ($answers as $answer) {
           foreach ($answer->getRanks() as $rank){
                $nbre++;
                array_push($rankIds,$rank -> getId());
                $rankentity = $em->getRepository('QuizmooRespondentBundle:Rank')->find($rank -> getId());
                array_push($rank_names,$rankentity->getAnswerOption()->getAnswerTitle().','.$rank->getRank());
           }
        }
   
        $names = array_unique($rank_names);
   
        $countValues = array_count_values($rank_names);
       
        arsort($countValues);
                 
        $pdata = Array();
        $i=0;
       
        foreach ($countValues as $key => $value) {
                $pd = ($value/$nbre)*100;
                $pdata[$i]=array('name' =>$key,'value'=>$pd);
                $i++;
               
        }
        
        return new Response(json_encode($pdata));
       }

       // testing the result of : getRankingAnswerResultAction
    public function getRankAction($questionId) {
        $em = $this->getDoctrine()->getEntityManager();
        $answers  = $em->getRepository('QuizmooRespondentBundle:RankingAnswer')->getRankingChoices($questionId);
        $rank_names = Array();
        $rankIds = Array();
        $nbre=0;
        foreach ($answers as $answer) {
           foreach ($answer->getRanks() as $rank){
                $nbre++;
                array_push($rankIds,$rank -> getId());
                $rankentity = $em->getRepository('QuizmooRespondentBundle:Rank')->find($rank -> getId());
                array_push($rank_names,$rankentity->getAnswerOption()->getAnswerTitle().$rank->getRank());
           }
        }
   
        $names = array_unique($rank_names);
        $countValues = array_count_values($rank_names);
      
        $data = Array(); 
        $i=0;
        $indice=1;
        $categories = Array();
        foreach ($countValues as $key => $value) {
                $data[$i]=Array($key,$value);
                $categories[$i]=Array($key);
                $indice++;
                $i++;
        }
       
      
 
        $series = array(
                    array("name" => "Data", "data" => $data )

        );

        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($questionId);
     
        /*bar chart */
        $bar = new Highchart();
        $bar->chart->renderTo('barChart'); 
        $bar->chart->type("column");
        $bar->title->text($question->getQuestionText());
        $bar->xAxis->title(array('text'  => "Rank Options"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Occurence"));
        $bar->yAxis->stackLabels(array('enabled'=> true));
        $bar->plotOptions->column(array("stacking"=>"normal", "dataLabels"=>array('enabled' => 'true','color'=>'white' )));
        $bar->series(array(array('type' => 'column','name' => 'occurence', 'data' => $data)));
   
       return $this->render('StatisticBundle:Default:chart_rank_result.html.twig', array(
              'bchart' => $bar
        ));   
      }

}