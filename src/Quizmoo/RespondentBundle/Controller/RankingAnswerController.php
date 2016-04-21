<?php

namespace Quizmoo\RespondentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\RespondentBundle\Entity\RankingAnswer;
use Quizmoo\RespondentBundle\Entity\Rank;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;


/**
 * RankingAnswer controller.
 *
 */
class RankingAnswerController extends Controller
{

public function putRankingAnswerAction(Request $request) {
    //http://localhost/quizmoo/trunk/web/app_dev.php/respondent/v1/put_rankinganswer
    //$string = '{"question_id": 113, "attributes" : {"answerOptionIds" : ["144","145","146"]}}';
    if (is_object(json_decode($request->getContent()))) {
    $data = json_decode($request->getContent(),true);
   
    $em = $this->getDoctrine()->getManager();
    $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($data['question_id']);
    if ($question instanceof RankingQuestion) {
         $rankinganswer  = new RankingAnswer();
         $rankinganswer -> setRankingQuestion($question);
         $em->persist($rankinganswer);
         $rank = 0 ;
            foreach ($data['attributes']['answerOptionIds'] as $answerOptionId) {
                    $rank++;
                    $answerOption = $em->getRepository('QuizmooQuestionnaireBundle:AnswerOption')->find($answerOptionId);
                    $rankEntity  = new Rank();
                    $rankEntity->setRank($rank);
                    $rankEntity->setAnswerOption($answerOption);
                    $rankEntity->setRankingAnswer($rankinganswer);
                    $em->persist($rankEntity);
                        
            }
            $em->flush();
    return new Response('Success',200);
    }
   }
    return new Response('invalid json');
 }


}