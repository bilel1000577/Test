<?php

namespace Quizmoo\QuestionnaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Form\MultipleChoiceType;
use Quizmoo\QuestionnaireBundle\Form\MultipleChoiceEditType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * MultipleChoice controller.
 *
 * @Route("/multiplechoice")
 */
class MultipleChoiceController extends Controller
{
    /**
     * Lists all MultipleChoice entities.
     *
     * @Route("/", name="multiplechoice")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QuizmooQuestionnaireBundle:MultipleChoice')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MultipleChoice entity.
     *
     * @Route("/{id}/show", name="multiplechoice_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleChoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleChoice entity.');
        }

        return $this->render('QuizmooQuestionnaireBundle:MultipleChoice:multipleChoiceLayout.'.$this->get('session')->get('whichTwig','').'html.twig',
                array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()));
    }
    

    /**
     * Displays a form to create a new MultipleChoice entity.
     *
     * @Route("/new", name="multiplechoice_new")
     * @Template()
     */
    public function newAction($id)
    {	
		
        $entity = new MultipleChoice();
      	/*
      	 * setting 'Multiple Choice Question '  choice as default 
      	 */
      	
      	$em = $this->getDoctrine()->getManager();
      	//$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Question à choix multiple");
      	$type="Multiple Choice Question";
        $entity->setQuestionType($type);
      	
              $form   = $this->createForm(new MultipleChoiceType(), $entity);

              return array(
                  'entity' => $entity,
                  'form'   => $form->createView(),
      			'id_questionnaire' =>$id
              );
          }

    /**
     * Creates a new MultipleChoice entity.
     *
     * @Route("/create", name="multiplechoice_create")
     * @Method("POST")
     * @Template("QuizmooQuestionnaireBundle:MultipleChoice:new.html.twig")
     */
    public function createAction(Request $request, $id)
    {	
        $entity  = new MultipleChoice();
        $form = $this->createForm(new MultipleChoiceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
              /*
              * recover the question answer
              */
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

            $entity->setQuestionOrder( sizeof($questionnaire->getQuestions())+1);

            $questionnaire->setModified(new \Datetime());
            $entity -> setQuestionnaire($questionnaire); 
            //$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Question à choix multiple");
            $type="Multiple Choice Question";
            $entity->setQuestionType($type);          
                    $em->persist($entity);
                    $em->flush();
          
            $content  =  $this->render('QuizmooQuestionnaireBundle:MultipleChoice:show.'.$this->get('session')->get('whichTwig','').'html.twig',
                array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent();
            $response = array('Content'=> $content,200);
            return new Response(json_encode($response));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MultipleChoice entity.
     *
     * @Route("/{id}/edit", name="multiplechoice_edit")
     * @Template()
     */
    public function editAction($id,$questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleChoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleChoice entity.');
        }

        $editForm = $this->createForm(new MultipleChoiceEditType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'id_questionnaire' =>$questionnaire
        );
    }

    /**
     * Edits an existing MultipleChoice entity.
     *
     * @Route("/{id}/update", name="multiplechoice_update")
     * @Method("POST")
     * @Template("QuizmooQuestionnaireBundle:MultipleChoice:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleChoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleChoice entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MultipleChoiceEditType(), $entity);
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
            $questionnaire_entity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);
            $questionnaire_entity->setModified(new \Datetime());
            $em->flush();

            $content  =  $this->render('QuizmooQuestionnaireBundle:MultipleChoice:multipleChoiceLayout.'.$this->get('session')->get('whichTwig','').'html.twig',
                array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent();

             return JsonResponse::create( array('isLast'=>false , 'content'=> $content));
        } else {
          print_r('form is invalid');
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MultipleChoice entity.
     *
     * @Route("/{id}/delete", name="multiplechoice_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, MultipleChoice $multipleChoiceQuestion, $questionnaire)
    {
	    $id = $multipleChoiceQuestion->getId();
        $form = $this->createDeleteForm($id);
        if ($request->getMethod() == 'POST') {
        
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleChoice')->find($id);
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
                throw $this->createNotFoundException('Unable to find MultipleChoice entity.');
            }

            $em->remove($entity);
            $questionnaire_entity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);
            $questionnaire_entity->setModified(new \Datetime());
            $em->flush();

            return JsonResponse::create(array('isLast'=>(count($questionnaire_entity->getQuestions()) == 0)));
        }
    }

        return $this->render('QuizmooQuestionnaireBundle:MultipleChoice:delete.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'delete_form' => $form->createView(),
            'id_questionnaire' =>$questionnaire,
            'entity'=>$multipleChoiceQuestion 
            
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
