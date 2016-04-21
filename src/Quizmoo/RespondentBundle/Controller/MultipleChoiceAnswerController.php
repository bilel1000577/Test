<?php

namespace Quizmoo\RespondentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;

/**
 * MultipleChoiceAnswer controller.
 *
 */
class MultipleChoiceAnswerController extends Controller
{
public function putMultipleChoiceAnswerAction(Request $request) {
    //http://localhost/quizmoo/trunk/web/app_dev.php/respondent/v1/put_multiplechoiceanswer
    //$string = '{"question_id": 118, "attributes" : {"answerOptionIds" : ["163","164"]}}';
    if (is_object(json_decode($request->getContent()))) {
    $data = json_decode($request->getContent(),true);
   
    $em = $this->getDoctrine()->getManager();
    $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($data['question_id']);
    if ($question instanceof MultipleChoice) {
  
    $multiplechoiceanswer  = new MultipleChoiceAnswer();
    $multiplechoiceanswer -> setMultipleChoiceQuestion($question);
            foreach ($data['attributes']['answerOptionIds'] as $answerOptionId) {
                
                   // $answerOptionId = $data['attributes'] ['answerOptionIds'] ;
                    $answerOption = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answerOptionId);
                    $multiplechoiceanswer->addAnswerOption($answerOption);
                
            }
            $em->persist($multiplechoiceanswer);
            $em->flush();
   
    return new Response('Success',200);
    
    }
   }
    return new Response('invalid json');
 }


 }