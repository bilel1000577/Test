<?php

namespace Quizmoo\QuestionnaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Quizmoo\QuestionnaireBundle\Entity\MultipleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Form\MultipleTextBoxQuestionType;
use Quizmoo\QuestionnaireBundle\Form\MultipleTextBoxQuestionEditType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * MultipleTextBoxQuestion controller.
 *
 * @Route("/multipletextboxquestion")
 */
class MultipleTextBoxQuestionController extends Controller
{
    /**
     * Lists all MultipleTextBoxQuestion entities.
     *
     * @Route("/", name="multipletextboxquestion")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MultipleTextBoxQuestion entity.
     *
     * @Route("/{id}/show", name="multipletextboxquestion_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleTextBoxQuestion entity.');
        }

        
        return $this->render('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:multipleTextBoxLayout.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'question'      => $entity,
              'questionnaire_id'=>$entity->getQuestionnaire()->getId()));
    }

    /**
     * Displays a form to create a new MultipleTextBoxQuestion entity.
     *
     * @Route("/new", name="multipletextboxquestion_new")
     * @Template()
     */
    public function newAction($id)
    {
        $entity = new MultipleTextBoxQuestion();

      	
      	$em = $this->getDoctrine()->getManager();
      	$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Question à plusieurs réponses ouvertes");
      	$entity->setQuestionType($type);
      	
              $form   = $this->createForm(new MultipleTextBoxQuestionType(), $entity);

              return array(
                  'entity' => $entity,
                  'form'   => $form->createView(),
      			'id_questionnaire' =>$id
              );
    }

    /**
     * Creates a new MultipleTextBoxQuestion entity.
     *
     * @Route("/create", name="multipletextboxquestion_create")
     * @Method("POST")
     * @Template("QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:new.html.twig")
     */
    public function createAction(Request $request, $id)
    {
        $entity  = new MultipleTextBoxQuestion();
	
        $form = $this->createForm(new MultipleTextBoxQuestionType(), $entity);
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
              $em->persist($entity);
              }
                            

			      $questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);

            $questionnaire->setModified(new \Datetime());
            
            // when adding a question we set him as the last one 
            $entity->setQuestionOrder( sizeof($questionnaire->getQuestions())+1);

            $entity -> setQuestionnaire($questionnaire);
            $type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName("Question à plusieurs réponses ouvertes");
            $entity->setQuestionType($type);          
            $em->persist($entity);
            
            $em->flush();

            $content =  $this->render('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:show.'.$this->get('session')->get('whichTwig','').'html.twig', array(
              'question'      => $entity,
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
     * Displays a form to edit an existing MultipleTextBoxQuestion entity.
     *
     * @Route("/{id}/edit", name="multipletextboxquestion_edit")
     * @Template()
     */
    public function editAction($id,$questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleTextBoxQuestion entity.');
        }

        $editForm = $this->createForm(new MultipleTextBoxQuestionEditType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'id_questionnaire' =>$questionnaire
        );
    }

    /**
     * Edits an existing MultipleTextBoxQuestion entity.
     *
     * @Route("/{id}/update", name="multipletextboxquestion_update")
     * @Method("POST")
     * @Template("QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $questionnaire)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MultipleTextBoxQuestion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MultipleTextBoxQuestionEditType(), $entity);
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

            
            $content =  $this->render('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:multipleTextBoxLayout.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'question'      => $entity,
              'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent();
            
             return JsonResponse::create( array('isLast'=>false , 'content'=> $content));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MultipleTextBoxQuestion entity.
     *
     * @Route("/{id}/delete", name="multipletextboxquestion_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, MultipleTextBoxQuestion $multipleTexBoxQuestion , $questionnaire)
    {
	    $id = $multipleTexBoxQuestion->getId();
        $form = $this->createDeleteForm($id);
        if ($request->getMethod() == 'POST') {
     
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion')->find($id);
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
                throw $this->createNotFoundException('Unable to find MultipleTextBoxQuestion entity.');
            }

            $em->remove($entity);
            $questionnaire_entity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);
            $questionnaire_entity->setModified(new \Datetime());

            $em->flush();

            return JsonResponse::create(array('isLast'=>(count($questionnaire_entity->getQuestions()) == 0)));
        }
    }
        return $this->render('QuizmooQuestionnaireBundle:MultipleTextBoxQuestion:delete.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'delete_form' => $form->createView(),
            'id_questionnaire' =>$questionnaire,
	    'entity'=>$multipleTexBoxQuestion
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
