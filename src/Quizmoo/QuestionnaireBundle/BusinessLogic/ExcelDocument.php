<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcelDocument
 *
 * @author Bilel
 */

namespace Quizmoo\QuestionnaireBundle\BusinessLogic;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\StatisticBundle\BusinessLogic\Statistic;
class ExcelDocument {
    private $questionnaire ;
    private $title ;
    private $excelService ;

    function __construct($questionnaire , $excelService , $title=null ) {

        $this->questionnaire = $questionnaire ;
        if (null == $title){
            $this->title = $questionnaire->getQuestionnaireName() ;
        } else {
            $this->title = $title ;
        }

        $this->excelService = $excelService ;
    }

    public function export (){
        ini_set('memory_limit', '-1');
        $this->initiateDocument($this->title);

        //fill question titles in the spreadsheet

        $this->fillTitles();

        //create and return  the response
        return $this->prepareResponse($this->title);

    }

    public function getExcelDoc(){
        $path="temp/".$this->title.".xls";
        $this->initiateDocument($this->title);

        //fill question titles in the spreadsheet

        $this->fillTitles();

        $this->excelService->getStreamWriter()->write($path);
        return $path;


    }

    private function fillTitles(){
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $array = str_split($string);
        $styleArray = array(
            'font' => array(
                'bold' => true,
            )
        );
        $times=array();
        $lon= array();
        $lat = array();
        $questions = $this->questionnaire->getQuestions();
        $answernumber=$questions[0]->numberOfAnswers();

        $objWorksheet = $this->excelService->excelObj->getActiveSheet();
        $row = $this->excelService->excelObj->setActiveSheetIndex(0);


        $this->numberAnswers($this->questionnaire);

        $i=1;
        $j=0;
        foreach ($questions as $question ){

                if ($question instanceof  MultipleChoice ){
                    $j = 2 ;
                    foreach( $question->getMultipleChoiceAnswers() as $answer){
                        if($answer->getAnswer()!=null && count($times)<$answernumber)
                        {
                            array_push($times, $answer->getAnswer()->getTimespan()->format('Y-m-d H:i:s'));
    
                        }
                        $arrayofAnswers = array();
                        foreach ($answer->getAnswerOptions() as $answerOption) {
                            $arrayofAnswers[] = trim( $answerOption->getanswerText());
                        }
                        $celValue =  implode(',', $arrayofAnswers);
                        $row->setCellValueByColumnAndRow($i,$j,$celValue);
                        $j++;
                    }

                }else if ($question instanceof  SingleTextBoxQuestion){
                    $j=2 ;
                    foreach( $question->getSingleTextBoxAnswers() as $answer ){
                        //echo "STBA : ".$answer->getAnswerText()."-> ".$cellName.$j."<br> " ;
                        if($answer->getAnswer()!=null && count($times)<$answernumber)
                        {
                            array_push($times, $answer->getAnswer()->getTimespan()->format('Y-m-d H:i:s'));
                            array_push($lat, $answer->getAnswer()->getLatitude());
                            array_push($lon, $answer->getAnswer()->getLongitude());
                        }
                        $row->setCellValueByColumnAndRow($i,$j, $answer->getAnswerText());
                        $j++;
                    }
                }else if ($question instanceof  RankingQuestion ){
                    $j = 2 ;
                    foreach( $question->getRankingQuestionAnswers() as $answer){
                        $arrayofAnswers = array();
                        if($answer->getAnswer()!=null && count($times)<$answernumber)
                        {
                            array_push($times, $answer->getAnswer()->getTimespan()->format('Y-m-d H:i:s'));
                            array_push($lat, $answer->getAnswer()->getLatitude());
                            array_push($lon, $answer->getAnswer()->getLongitude());
                        }
                        foreach ($answer->getRanks() as $rank) {
                            $arrayofAnswers[] = trim( $rank->getAnswerOption()->getanswerText());
                        }

                        $celValue =  implode(',', $arrayofAnswers);
                        $row->setCellValueByColumnAndRow($i,$j,$celValue);
                        $j++;
                    }}


                $i++;

        }
        $row->setCellValueByColumnAndRow($i,1,"Time")
                ->getStyleByColumnAndRow($i,1)->applyFromArray($styleArray);
        $j=2;
        foreach ($times as $time) {
            $row->setCellValueByColumnAndRow($i,$j,$time);
            $j++;
        }
        $i++;
    }

    private function numberAnswers($questionnaire  ){

        $numberOfQuestions = sizeof($questionnaire->getQuestions());
        if ($numberOfQuestions != 0){
            $questions = $questionnaire->getQuestions();
            $objWorksheet = $this->excelService->excelObj->getActiveSheet();
            $row = $this->excelService->excelObj->setActiveSheetIndex(0);
            $row->setCellValueByColumnAndRow(0,1,"User ID");
            $userId = 2;
            for ($userId = 2; $userId< $questions[0]->numberOfAnswers()+2 ; $userId++){
                    $i = $userId  -1 ;
                    $row->setCellValueByColumnAndRow(0,$userId,"".$i);

            }
        }

    }

    private function initiateDocument($title ){
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color'=>array(
                    'rgb'=>'000000FF')
            ), 'alignment'=> array (
                'horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );

        $numberOfColunnsToMerge = sizeof($this->questionnaire->getQuestions());
        $charFrom = 'A1';
        $charTo = $this->guessExcelCharacter($numberOfColunnsToMerge+1).'1';

        $this->excelService->excelObj->getProperties()->setCreator($this->questionnaire->getUser()->getUsername())
                            ->setLastModifiedBy($this->questionnaire->getUser()->getUsername())
                            ->setTitle("Office 2005 XLSX Test Document")
                            ->setSubject("Office 2005 XLSX Test Document")
                            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                            ->setKeywords("office 2005 openxml php")
                            ->setCategory("Test result file");
        $this->excelService->excelObj->setActiveSheetIndex(0);//->mergeCells($charFrom.':'.$charTo);
        //  ->setCellValueByColumnAndRow(0,1, $title)->getStyle('A1')->applyFromArray($styleArray);

    }

    private function guessExcelCharacter($colNumbers){
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $array = str_split($string);
        return $array[$colNumbers-1];

    }

    private function  prepareResponse($fileName){
        $arr = explode(" ",$fileName);
        $newfileName = implode("_",$arr);
        $response = $this->excelService->getResponse();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$newfileName.'.xls');

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        return $response;
    }

}
?>