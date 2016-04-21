<?php

namespace Quizmoo\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Ob\HighchartsBundle\Highcharts\Highchart;
class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StatisticBundle:Default:index.'.$this->get('session')->get('whichTwig','').'html.twig', array('name' => $name));
    }

    public function countNbreOfAnswersPerQuestionAction($questionnaireId){


        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('QuizmooQuestionnaireBundle:Question')->getQuestions($questionnaireId);
        $question_names = array();
        $occurences = array();
        
        foreach ($questions as $question) {

            if ($question instanceof SingleTextBoxQuestion) {
                $nb = $em->getRepository('QuizmooRespondentBundle:SingleTextBoxAnswer')->countByID($question->getId());     

            } else if ($question instanceof MultipleChoice) {
                $nb = $em->getRepository('QuizmooRespondentBundle:MultipleChoiceAnswer')->countByID($question->getId());  

            } else if ($question instanceof RankingQuestion) {   
                $nb = $em->getRepository('QuizmooRespondentBundle:RankingAnswer')->countByID($question->getId());  

            } 
            array_push($question_names,$question->getQuestionText());
            array_push($occurences,$nb);

        }
       
        $data = Array(); 
        $i=0;
        $indice=1;
        $categories = Array();
        foreach ($occurences as $key => $value) {
                $data[$i]=Array($question_names[$i],intval($occurences[$i]));
                $categories[$i]=Array('Q'.$indice);
                $i++;
                $indice++;
        }
        $series = array(
                    array("name" => "Data", "data" => $data )

        );


         /*bar chart */
        $bar = new Highchart();
        $bar->chart->renderTo('barChart'); 
        $bar->title->text("Number Of Answers Per Question");
        $bar->xAxis->title(array('text'  => "Questions"));
        $bar->xAxis->categories($categories);
        $bar->yAxis->title(array('text'  => "Numbre Of Answers"));
        $bar->series(array(array('type' => 'column','name' => 'Questions', 'data' => $data)));
   
       return $this->render('StatisticBundle:Default:nb_answers.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'bchart' => $bar
        ));   

    }
}
