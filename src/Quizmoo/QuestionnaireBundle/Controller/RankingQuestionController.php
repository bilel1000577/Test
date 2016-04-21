<?php

namespace Quizmoo\QuestionnaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\QuestionnaireBundle\Form\RankingQuestionType;
use Quizmoo\QuestionnaireBundle\Form\RankingQuestionEditType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * RankingQuestion controller.
 *
 */
class RankingQuestionController extends Controller
{
    /**
     * Lists all RankingQuestion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QuizmooQuestionnaireBundle:RankingQuestion')->findAll();

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:index.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a RankingQuestion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:RankingQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingQuestion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:rankingLayout.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'question'      => $entity,
              'questionnaire_id'=>$entity->getQuestionnaire()->getId()));
    }

    /**
     * Displays a form to create a new RankingQuestion entity.
     *
     */
    public function newAction($id)
    {
        $entity = new RankingQuestion();

      	$em = $this->getDoctrine()->getManager();
      	//$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Mettre dans l'ordre");
      	$type="Ranking Question";
        $entity->setQuestionType($type);
	
        $form   = $this->createForm(new RankingQuestionType(), $entity);

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
			      'id_questionnaire' =>$id
        ));
    }

    /**
     * Creates a new RankingQuestion entity.
     *
     */
    public function createAction(Request $request, $id)
    {
        $entity  = new RankingQuestion();
        $form = $this->createForm(new RankingQuestionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			      $texte = $entity->getQuestionAnswer();
              $ligne = preg_split("/[\n]+/", $texte);
              foreach( $ligne as $row => $value ) 
              {
              $answerOption = new \Quizmoo\QuestionnaireBundle\Entity\AnswerOption();
              $answerOption->setAnswerTitle($value);
              $answerOption->setAnswerText($value);
              $answerOption->setQuestion($entity);
              $entity->addAnswerOption($answerOption);
              $em->persist($answerOption);
              }
                            
   
      			$questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);

            $questionnaire->setModified(new \Datetime());

  
            $entity->setQuestionOrder( sizeof($questionnaire->getQuestions())+1);
      			
     
            $entity -> setQuestionnaire($questionnaire);
            //$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Mettre dans l'ordre");
            $type="Ranking Question";
            $entity->setQuestionType($type);
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('quizmoo_questionnaire_modifier', array('id' => $id,'pos'=>'BOTTOM')));
            $content  = $this->render('QuizmooQuestionnaireBundle:RankingQuestion:show.'.$this->get('session')->get('whichTwig','').'html.twig', array(
              'question'      => $entity,
              'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent();
             $response = array('Content'=> $content,200);
             return new Response(json_encode($response));

        }

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RankingQuestion entity.
     *
     */
    public function editAction($id, $questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:RankingQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingQuestion entity.');
        }

        $editForm = $this->createForm(new RankingQuestionEditType(), $entity);
        

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'id_questionnaire' =>$questionnaire
        ));
    }

    /**
     * Edits an existing RankingQuestion entity.
     *
     */
    public function updateAction(Request $request, $id, $questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:RankingQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingQuestion entity.');
        }

        $editForm = $this->createForm(new RankingQuestionEditType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
             foreach( $entity->getAnswerOptions() as $answerOption)
            {

                $entity->removeAnswerOption($answerOption);
                $em->remove($answerOption); 

            }

             $texte = $entity->getQuestionAnswer();
              $ligne = preg_split("/[\n]+/", $texte);
              foreach( $ligne as $row => $value ) 
              {
              $answerOption = new \Quizmoo\QuestionnaireBundle\Entity\AnswerOption();
              $answerOption->setAnswerTitle($value);
              $answerOption->setAnswerText($value);
              $answerOption->setQuestion($entity);
              $entity->addAnswerOption($answerOption);
              $em->persist($answerOption);
              } 

            $em->persist($entity);

            $entity->getQuestionnaire()->setModified(new \Datetime());

            $em->flush();

             //return $this->redirect($this->generateUrl('quizmoo_questionnaire_modifier', array('id' => $questionnaire )));
        }
        $content  = $this->render('QuizmooQuestionnaireBundle:RankingQuestion:rankingLayout.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'question'      => $entity,
              'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent();

        return JsonResponse::create( array('isLast'=>false , 'content'=> $content));

        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Deletes a RankingQuestion entity.
     *
     */
    public function deleteAction(Request $request, RankingQuestion $rankingQuestion, $questionnaire)
    {
	    $id = $rankingQuestion->getId();
        $form = $this->createDeleteForm($id);
        if ($request->getMethod() == 'POST') {
     
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QuizmooQuestionnaireBundle:RankingQuestion')->find($id);
            $entityQuestion = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);
            $orderQuestion = $entityQuestion->getQuestionOrder();
            
            $questionnaireEntity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);

            $questions = $questionnaireEntity->getQuestions();
            foreach ($questions as $key => $questionObject) {
              if ($questionObject->getQuestionOrder() > $orderQuestion) {
                $questionObject->setQuestionOrder(($questionObject->getQuestionOrder()-1));
                $em->persist($questionObject);
              }
            }
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RankingQuestion entity.');
            }
           
            $em->remove($entity);
            $questionnaire_entity = $entity->getQuestionnaire();
            $questionnaire_entity->setModified(new \Datetime());

            $em->flush();

            return JsonResponse::create(array('isLast'=>(count($questionnaire_entity->getQuestions()) == 0)));
        }
      }
        return $this->render('QuizmooQuestionnaireBundle:RankingQuestion:delete.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'delete_form' => $form->createView(),
            'id_questionnaire' =>$questionnaire,
            'entity' => $rankingQuestion
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
