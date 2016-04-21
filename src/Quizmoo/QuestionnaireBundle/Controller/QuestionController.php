<?php

namespace Quizmoo\QuestionnaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Quizmoo\QuestionnaireBundle\Entity\Question;
use Quizmoo\QuestionnaireBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;

/**
 * Question controller.
 *
 */
class QuestionController extends Controller
{
    /**
     * Lists all Question entities.
     *
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QuizmooQuestionnaireBundle:Question')->findAll();

        return $this->render('QuizmooQuestionnaireBundle:Question:index.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Question entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('QuizmooQuestionnaireBundle:Question:show.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Question entity.
     *
     */
    public function newAction($id)
    {
        $entity = new Question();
        $form   = $this->createForm(new QuestionType(), $entity);

        return $this->render('QuizmooQuestionnaireBundle:Question:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'id_questionnaire' =>$id
        ));
    }

    /**
     * Creates a new Question entity.
     *
     */
    public function createAction(Request $request, $id)
    {
        $entity  = new Question();
        $form = $this->createForm(new QuestionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
             // On r�cup�re l'entit� questionnaire correspondant � l'id $id
            //$questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
            //$questionnaire->addQuestion($entity);
             $em->persist($entity);
                         $em->flush();

           // return $this->redirect($this->generateUrl('question_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('quizmoo_questionnaire_modifier', array('id' => $id)));
        }

        return $this->render('QuizmooQuestionnaireBundle:Question:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $editForm = $this->createForm(new QuestionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('QuizmooQuestionnaireBundle:Question:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Question entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new QuestionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('question_edit', array('id' => $id)));
        }

        return $this->render('QuizmooQuestionnaireBundle:Question:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Question entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Question entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('question'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function setOrderAction($id ){

        $order = $this->get('request')->get('order');
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);
        $question->setQuestionOrder($order);
        $em->persist($question);
        $em->flush();
        
        $response = array(
          "order"=>$order
      );
      
          return JsonResponse::create($response);
    }

    public function displayAnswerOptionsAction($id,$rank){
        $data = array();
        $options = array();
        $em = $this->getDoctrine()->getManager();

        $question = $em->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        if($question instanceof MultipleChoice){
            $answersOp = $question->getAnswerOptions();
            foreach ($answersOp as $key => $answerOp) {
                $data[$key] = array_merge($options, array("id"=>$answerOp->getId(),"answer"=>trim($answerOp->getAnswerText()),"ref"=>"choiceId","questionId"=>$question->getId()));
           }

        }else if ($question instanceof SelectField){
            $answersOp = $question->getAnswerOptions();
            foreach ($answersOp as $key => $answerOp) {
                $data[$key] = array_merge($options, array("id"=>$answerOp->getId(),"answer"=>trim($answerOp->getAnswerText()),"ref"=>"optionId","questionId"=>$question->getId()));
            }
        }else if ($question instanceof RankingQuestion){
            $answersOp = $question->getAnswerOptions();
            foreach ($answersOp as $key => $answerOp) {
                $data[$key] = array_merge($options, array("id"=>$rank,"answer"=>++$key,"ref"=>"rankId","questionId"=>$question->getId()));
            }

        }else if ($question instanceof RatingScale){
            $answersOp = $question->getAnswerOptions();
            foreach ($answersOp as $key => $answerOp) {
                if($answerOp->getAnswerText() == "level")
                $data[$key] = array_merge($options, array("id"=>$answerOp->getId(),"answer"=>trim($answerOp->getAnswerTitle()),"ref"=>"rateId","questionId"=>$question->getId()));
           
            }
        }
        return new Response(json_encode($data));
    }
}
