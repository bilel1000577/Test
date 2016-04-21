<?php

namespace Quizmoo\QuestionnaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Form\SingleTextBoxQuestionType;
use Quizmoo\QuestionnaireBundle\Form\SingleTextBoxQuestionEditType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * SingleTextBoxQuestion controller.
 *
 */
class SingleTextBoxQuestionController extends Controller
{
    /**
     * Lists all SingleTextBoxQuestion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QuizmooQuestionnaireBundle:SingleTextBoxQuestion')->findAll();

        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:index.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a SingleTextBoxQuestion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:SingleTextBoxQuestion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SingleTextBoxQuestion entity.');
        }

        

        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:singleTextBoxLayout.'.$this->get('session')->get('whichTwig','').'html.twig',
                array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()));
    }

    /**
     * Displays a form to create a new SingleTextBoxQuestion entity.
     *
     */
    public function newAction($id)
    {
	
        $entity = new SingleTextBoxQuestion();
           
    	$em = $this->getDoctrine()->getManager();
    	//$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName('Question ouverte');
    	$type="Single TextBox Question";
        $entity->setQuestionType($type);
    	$form   = $this->createForm(new SingleTextBoxQuestionType($em), $entity);

        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'id_questionnaire' =>$id
        ));
    }

    /**
     * Creates a new SingleTextBoxQuestion entity.
     *
     */
    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new SingleTextBoxQuestion();
        $form = $this->createForm(new SingleTextBoxQuestionType($em), $entity);
        $form->bind($request);

        if ($form->isValid()) {

      			$questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);

                $questionnaire->setModified(new \Datetime());
 
                $entity->setQuestionOrder( sizeof($questionnaire->getQuestions())+1);

                $entity -> setQuestionnaire($questionnaire);

                //$type =  $em->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName('Question ouverte');
                $type="Single TextBox Question";
                $entity->setQuestionType($type);

                $em->persist($entity);
                $em->flush();

            /*return $this->redirect($this->generateUrl('quizmoo_questionnaire_modifier', array('id' => $id,'pos'=>'BOTTOM')));*/
            $content  =  $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:show.'.$this->get('session')->get('whichTwig','').'html.twig',
                    array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent() ;
            $response = array('Content'=> $content,200);
            return new Response(json_encode($response));
        }

        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:new.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SingleTextBoxQuestion entity.
     *
     */
    public function editAction($id,$questionnaire)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:SingleTextBoxQuestion')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SingleTextBoxQuestion entity.');
        }

        $editForm = $this->createForm(new SingleTextBoxQuestionEditType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'id_questionnaire' =>$questionnaire
        ));
    }

    /**
     * Edits an existing SingleTextBoxQuestion entity.
     *
     */
    public function updateAction(Request $request, $id, $questionnaire)
    { 
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QuizmooQuestionnaireBundle:SingleTextBoxQuestion')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SingleTextBoxQuestion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SingleTextBoxQuestionEditType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);

            $questionnaire_entity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);
            $questionnaire_entity->setModified(new \Datetime());

            $em->flush();
  
			      // return $this->redirect($this->generateUrl('singletextboxquestion'));
            $content  =  $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:singleTextBoxLayout.'.$this->get('session')->get('whichTwig','').'html.twig',
                array(
                    'question'=>$entity,
                    'questionnaire_id'=>$entity->getQuestionnaire()->getId()))->getContent() ;
            return JsonResponse::create( array('isLast'=>false , 'content'=> $content));
        }
        else {
            print_r($editForm->getErrors());
            return new Response('form is invalid ');
        }

        return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:edit.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SingleTextBoxQuestion entity.
     *
     */
    public function deleteAction(Request $request, SingleTextBoxQuestion $singleTextBoxQuestion , $questionnaire)
    {
	    $id = $singleTextBoxQuestion->getId();
        $form = $this->createDeleteForm($id);
        if ($request->getMethod() == 'POST') {
     
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('QuizmooQuestionnaireBundle:SingleTextBoxQuestion')->find($id);
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
                    throw $this->createNotFoundException('Unable to find SingleTextBoxQuestion entity.');
                }
                $em->remove($entity);

                $questionnaire_entity = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire);
                $questionnaire_entity->setModified(new \Datetime());
                
                $em->flush();
                
                return JsonResponse::create(array('isLast'=>(count($questionnaire_entity->getQuestions()) == 0)));
            }   
        }
            return $this->render('QuizmooQuestionnaireBundle:SingleTextBoxQuestion:delete.'.$this->get('session')->get('whichTwig','').'html.twig', array(
            'delete_form' => $form->createView(),
            'id_questionnaire' =>$questionnaire,
            'entity' => $singleTextBoxQuestion
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
