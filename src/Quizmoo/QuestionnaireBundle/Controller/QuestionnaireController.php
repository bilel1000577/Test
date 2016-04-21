<?php
namespace Quizmoo\QuestionnaireBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Quizmoo\QuestionnaireBundle\Entity\Questionnaire;
use Quizmoo\QuestionnaireBundle\Entity\Question;
use Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion;
use Quizmoo\QuestionnaireBundle\Entity\MultipleChoice;
use Quizmoo\QuestionnaireBundle\Entity\RankingQuestion;
use Quizmoo\QuestionnaireBundle\Form\QuestionnaireType;
use Quizmoo\QuestionnaireBundle\Form\QuestionnaireEditType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Quizmoo\QuestionnaireBundle\Services\Mailer;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Quizmoo\QuestionnaireBundle\BusinessLogic\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class QuestionnaireController extends Controller
{

  private function controlPageValue($page){
        // fallback to 1 if a strange value is given
        if (! ((int)$page == $page && (int)$page > 0 ) ){
            return 1 ;
        }
        // else just return the same value
        return $page ;
  }
  private function controlTabValue($tabValue){
      $tabs = array ('ongoing','closed','drafts','received') ;
      
      if ( ! in_array($tabValue, $tabs)){
          return 'ongoing';
      } 
      return $tabValue ;
  }  

  public function mySurveysAction(Request $request,$categorieId){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.context')->getToken()->getUser();
      if (is_object($user)) {
          $field = $request->get('query');
          $page= $request->get('current_page');
          $tab=$request->get('tab');
          $page_ongoing=$request->get('page_ongoing');
          $page_closed=$request->get('page_closed');
          $page_drafts=$request->get('page_drafts');
          $page_received=$request->get('page_received');
          $tab = $this->controlTabValue($tab);
          $page = $this->controlPageValue($page);
          $page_ongoing = $this->controlPageValue($page_ongoing);
          $page_closed = $this->controlPageValue($page_closed);
          $page_drafts = $this->controlPageValue($page_drafts);
          $page_received = $this->controlPageValue($page_received);
          //ongoing
          $entities_ongoing = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->numberOfSurveysOngoing($user,$categorieId);
          $total_entities_ongoing = count($entities_ongoing); 
          //partie pagination ongoing
          $total_surveys    = $total_entities_ongoing; 
          $surveys_per_page = $this->container->getParameter('max_surveys_on_listepage');
          $last_page         = ceil($total_surveys / $surveys_per_page);
          $range_entities_ongoing = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->findRangeOfSurveysOngoing($page_ongoing,$surveys_per_page,$user,$categorieId);
          //closed
          $entities_closed = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->numberOfSurveysClosed($user,$categorieId);
          $total_entities_closed = count($entities_closed);
          //partie pagination closed
          $total_surveys_closed    = $total_entities_closed; 
          $surveys_per_page_closed = $this->container->getParameter('max_surveys_on_listepage');
          $last_page_closed         = ceil($total_surveys_closed / $surveys_per_page_closed);
          $range_entities_closed = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->findRangeOfSurveysClosed($page_closed,$surveys_per_page_closed,$user,$categorieId);
          //drafts
          $entities_drafts = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->numberOfSurveysInDrafts($user,$categorieId);
          $total_entities_drafts = count($entities_drafts);
          //partie pagination closed
          $total_surveys_drafts   = $total_entities_drafts; 
          $surveys_per_page_drafts = $this->container->getParameter('max_surveys_on_listepage');
          $last_page_drafts        = ceil($total_surveys_drafts / $surveys_per_page_drafts);
          $range_entities_drafts = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->findRangeOfSurveysDrafts($page_drafts,$surveys_per_page_drafts,$user,$categorieId);

          return $this->render('QuizmooQuestionnaireBundle:Questionnaire:newindex.html.twig', array(
            'tab'=>$tab,
            'query'=>$field,
            'current_page' => $page,
            'numberOfPage' => $surveys_per_page,
            'entities_ongoing' => $range_entities_ongoing,
            'total_entities_ongoing' => $total_entities_ongoing,
            'page'=>$page,
            'page_ongoing' => $page_ongoing,
            'last_page' => $last_page,
 
            'entities_closed' => $range_entities_closed,
            'total_entities_closed' => $total_entities_closed,
            'page_closed' => $page_closed,
            'last_page_closed' => $last_page_closed,

            'entities_drafts' => $range_entities_drafts,
            'total_entities_drafts' => $total_entities_drafts,
            'page_drafts' => $page_drafts,
            'last_page_drafts' => $last_page_drafts,

   
            'page_received' => $page_received

    
            ));
      }else{
            throw new AccessDeniedException('This user does not have access to this section.');
    }
  }

  public function describeAction(Request $request,$id){
    $em = $this->getDoctrine()->getManager();
    $questionnnaire  =  $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);            
    $newDescription =$this->get('request')->request->get('newDescription');
    $questionnnaire->setDescription(trim($newDescription));
    $em->persist($questionnnaire);
    $em->flush();
    $return=array("responseCode"=>200, "message"=>"newDescription sent with succes ", "newDescription"=>$newDescription);
    $return=json_encode($return);
    return new Response($return,200,array('Content-Type'=>'application/json'));     
  }

  public function renameAction($id){
    $request = $this->get('request');
    $newName = $request->get('newName');
    try {
      $em = $this->getDoctrine()->getManager();
      $questionnnaire  =  $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
      $questionnnaire->setQuestionnaireName($newName);
      $em->persist($questionnnaire);
      $em->flush(); 
      }catch (\Doctrine\DBAL\DBALException $e){
          
      }
     
    $return=array("responseCode"=>200, "message"=>"newName sent with succes ", "newName"=>$newName); 
    $questionnnaire  =  $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
    $return=json_encode($return);
    return new Response($return,200,array('Content-Type'=>'application/json',
        'id' => $id,
        'newName'=> $newName
    ));    
  }


  public function ajouterAction()
  {
   $currentUser = $this->getUser();
    if (is_object($currentUser)){
          $em = $this->getDoctrine()->getManager();

          $questionnaire = new Questionnaire;
          $questionnaire->setUser($currentUser);
          $questionnaire->setState(DRAFT_QUESTIONNAIRE);
          $questionnaire->setQuestionnaireName('Default Name');
          $em->persist($questionnaire);
          $em->flush();
         
       
       return $this->redirect($this->generateUrl('quizmoo_questionnaire_modifier', array('id' => $questionnaire->getId())));
    }else{
      return $this->redirect($this->generateUrl('fos_user_security_logout'));
    }
  }

  public function modifierAction(Request $request,Questionnaire $questionnaire)
  {
    if ($questionnaire ){
    $form = $this->createForm(new QuestionnaireType(), $questionnaire);

    $request = $this->getRequest();
    //$questionnaire->setQuestionnaireName(stripslashes($questionnaire->getQuestionnaireName()));
    $nbOfQuestions = count($questionnaire->getQuestions());
    return $this->render('QuizmooQuestionnaireBundle:Questionnaire:modifier.'.$this->get('session')->get('whichTwig','').'html.twig', array(
      'form'    => $form->createView(),
      'questionnaire' => $questionnaire,
      'nbOfQuestions' => $nbOfQuestions,
      'pos'=>$request->get('pos')
    ));
    }
    
  }


  public function menuAction($nombre) // Ici, nouvel argument $nombre, on l'a transmis via le render() depuis la vue
  {
    $liste = $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->findBy(
                    array(),          // Pas de critère
                    array('id' => 'desc'), // On trie par date décroissante
                    $nombre,         // On sélectionne $nombre questionnaires
                    0                // À partir du premier
                  );
     
    return $this->render('QuizmooQuestionnaireBundle:Questionnaire:menu.'.$this->get('session')->get('whichTwig','').'html.twig', array(
      'liste_questionnaires' => $liste // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaires au template !
    ));
  }
  
  /**
   * Gets JSON of the autocmpleate field of questionnaires
   *  @return proposition when searching questionnaires
   */

  
  public function PreviewAction(Questionnaire $questionnaire)
  {
    $request = $this->getRequest();
      if($questionnaire->getDisplaySingleQuestion()== 0){
        return $this->render('QuizmooQuestionnaireBundle:Questionnaire:preview.'.$this->get('session')->get('whichTwig','').'html.twig', array(
          'questionnaire' => $questionnaire
        ));
      }else if($questionnaire->getDisplaySingleQuestion()== 1){
          return $this->render('QuizmooQuestionnaireBundle:Questionnaire:previewInSequence.'.$this->get('session')->get('whichTwig','').'html.twig', array(
          'questionnaire' => $questionnaire
        ));
      }
    
  }
  
  public function generateQuestionnaireUrlAction(Request $request,$id)
  {
      $em = $this->getDoctrine()->getManager(); 
      $questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
      //check if the questionnaire is closed
      if ($questionnaire->getState() === CLOSED_QUESTIONNAIRE ){
        $copy = clone $questionnaire ;

        $copy->setState(ONGOING_QUESTIONNAIRE);
        //$copy->setQuestionnaireName($questionnaire->getQuestionnaireName().' clone ');
        $copy->setCreated(new \Datetime());
        $copy->setNumberOfAnswers(0);
        $copy->setHash($questionnaire->getHash().'_clone');
        $copy->setUser($this->getUser());
        $copy->cloneQuestions($em);
        $em->persist($copy);
        $url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$copy->getHash())); 
        $em->flush();
       }else{
      
        $url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$questionnaire->getHash()));   
        }
        $url = "http://".$_SERVER['SERVER_NAME'].$url;
        $path_parts = \pathinfo($url);
        $url_fixe = $path_parts['dirname'];
        $url_fixe = $url_fixe."/";
        if ($questionnaire->getState() === DRAFT_QUESTIONNAIRE ){
          $questionnaire->setState(ONGOING_QUESTIONNAIRE);
        }
        $this->getDoctrine()->getEntityManager()->persist($questionnaire);
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->render('QuizmooQuestionnaireBundle:Questionnaire:send.'.$this->get('session')->get('whichTwig','').'html.twig', array(
        'questionnaire' => $questionnaire ,
        'url' => $url,
        'url_fixe' => $url_fixe,
        'hash' =>$questionnaire->getHash(),
        'id'=>$id,
        'titre' =>$questionnaire->getQuestionnaireName()
    ));
     
  }
  
 
 public  function getUser()
 {
   return $this->get('security.context')->getToken()->getUser();
 }

 public function sendAction(Request $request)
 {
     $id =  $request->get('id');

     $questionnaire= $this->getDoctrine()
                  ->getManager()
                  ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
                  ->find($id);
     $url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$questionnaire->getHash()));
     $url = "http://".$_SERVER['SERVER_NAME'].$url;
     $user = $this->getUser();
     $mails=$request->request->get('mails');
     $description=$request->get('description');
     if (! isset($description) || $description==''){
      //$description = "You have received a survey from ".$this->getUser()->getUsername();
      $description = "Vous avez reçu un lien de sondage de la part de ".$this->getUser()->getUsername();
     }
     $mail=  \trim($mails);
     $myArray = \explode(',', $mail);
     
     //return $this->render( 'QuizmooQuestionnaireBundle:SendMailer:viewsendmailer.html.twig',array('url' => $url,'description' =>$description, 'user' => $user));
    
    for($i = 0, $size = count($myArray); $i < $size; ++$i) {
    $this->get('mail_helper')->sendEmail($myArray[$i], $this->renderView(
                'QuizmooQuestionnaireBundle:SendMailer:viewsendmailer.html.twig',array('url' => $url,'description' =>$description, 'user' => $user))
             ,$questionnaire->getQuestionnaireName(),array('noreply@quizmoo.com' => 'Quizmoo'));
    }
    $return=array("responseCode"=>200, "message"=>"The survey has been sent successfully");
    $return=json_encode($return);
    return new Response($return,200,array('Content-Type'=>'application/json'

       ));
      
 }
    public function getquestionnaireJsonAction($page, $count,$userId) {
    $offset = ($page - 1) * $count;

    $em = $this->getDoctrine()->getEntityManager();

    $total = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->getCount();

    $mquestionnaires  = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->getMQuestionnaires($order_by = array(), $offset, $limit = 0,$userId);
    $r_array = $this->questionnaires2Array($mquestionnaires);
    $r = array('page' => $page, 'count' => $count, 'total' => $total, 'questionnaires' => $r_array);
    return new Response(json_encode($r));
  }

   private function questionnaires2Array($questionnaires){
    $questionnaires_array = array();

    foreach ($questionnaires as $questionnaire) {
        $questions_array = array();
       foreach ($questionnaire->getQuestions() as $question){
           $question_array = array('id' => $question->getId(), 'questionText' => $question->getQuestionText(),
               'questionAnswer' => $question->getQuestionAnswer(),'questionType' => $question->getQuestionType()->getQuestionTypeName());
           $questions_array[] = $question_array;
       }
       $r_array = array('id' => $questionnaire->getId(),'username' =>$questionnaire->getUser()->getUsername(),'categorie' =>$questionnaire->getCategorie()->getName(), 'questionnaireName' => $questionnaire->getQuestionnaireName(), 'questions' => $questions_array);
       $questionnaires_array[] = $r_array;
    }
    return $questionnaires_array;
  }

  public function sharAction(Request $request ){
           $url =  $request->get('url');
  $url = "http://".$_SERVER['SERVER_NAME'].$url;

  return $this->render('QuizmooQuestionnaireBundle:ShareFacebook:share.'.$this->get('session')->get('whichTwig','').'html.twig', array('url' => $url));
  }
       
  public function personaliserHashAction($id,Request $request){
      $questionnaire= $this->getDoctrine()
            ->getManager()
            ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
            ->find($id);
       
       $input_hash=$request->request->get('personaliser_hash');
       $questionnaire->setHash($input_hash);
       $em = $this->getDoctrine()
               ->getManager();
       
              $em->persist($questionnaire);
             $em->flush();
       $url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$input_hash));   
       $url = "http://".$_SERVER['SERVER_NAME'].$url;
       $path_parts = \pathinfo($url);
       $url_fixe = $path_parts['dirname'];
       
       return new Response("$url");
  }
        
  public function greetingAction($id){
    $message='You Questionnaire Hash is updated';
    $statusCode=200;
    $questionnaire= $this->getDoctrine()
            ->getManager()
            ->getRepository('QuizmooQuestionnaireBundle:Questionnaire')
            ->find($id);
    $titre = $questionnaire-> getQuestionnaireName();
    $request = $this->get('request');
    $new_hash=$request->request->get('formName');
    $new_hash = Utils::slugify($new_hash);
    $url="";
    try{
    $questionnaire->setHash($new_hash);
    $new_hash = $questionnaire->getHash();
    
    $em = $this->getDoctrine()
            ->getManager();
    $em->persist($questionnaire);
    $em->flush();
    $url =  $this->generateUrl('quizmoo_respondent',array('hash' =>$new_hash));   
    $url = "http://".$_SERVER['SERVER_NAME'].$url;
    }
    catch (\Doctrine\DBAL\DBALException $e){
    $message='this hash code already exist';
    $statusCode=400;
    }
    if($new_hash!=""){//if the user has written his name
    $return=array("responseCode"=>$statusCode,  "message"=>$message,'id' => $id,
     'hash' =>$questionnaire->getHash(),
     'url' =>$url,
        'titre' =>$titre
    );
    }
    else{
       $statusCode=400;
      $return=array("responseCode"=>$statusCode, "message"=>"You have to write your New Hash!");
    }
    $return=json_encode($return);//jscon encode the array
    return new Response($return,200,array('Content-Type'=>'application/json',
       'id' => $id,
       'hash' =>$questionnaire->getHash(),
       'url' =>$url,
       'titre' =>$titre
       ));
    }



  public function listeAction($page)
  {
    $em                = $this->getDoctrine()->getEntityManager();
    $total             = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Question')->createQueryBuilder('q')->getQuery()->getResult();
    /* total of résultat */
    $total_questions    = count($total);
    $questions_per_page = $this->container->getParameter('max_questions_on_listepage');
    $last_page         = ceil($total_questions / $questions_per_page);
    $previous_page     = $page > 1 ? $page - 1 : 1;
    $next_page         = $page < $last_page ? $page + 1 : $last_page;
    /* résultat  à afficher*/
    $entities          = $this->getDoctrine()->getRepository('QuizmooQuestionnaireBundle:Question')->createQueryBuilder('q')->setFirstResult(($page * $questions_per_page) - $questions_per_page)->setMaxResults($this->container->getParameter('max_questions_on_listepage'))->getQuery()->getResult();
    return $this->render('QuizmooQuestionnaireBundle:Questionnaire:questionnaire.'.$this->get('session')->get('whichTwig','').'html.twig', array(
        'entities' => $entities,
        'last_page' => $last_page,
        'previous_page' => $previous_page,
        'current_page' => $page,
        'next_page' => $next_page,
        'total_questions' => $total_questions,
    ));
    return $this->render('QuizmooQuestionnaireBundle:Questionnaire:questionnaire.'.$this->get('session')->get('whichTwig','').'html.twig');
  }

 
  public function analyseAction(Questionnaire $questionnaire,$choice=null)
  {
    $em = $this->getDoctrine()->getEntityManager();
    $user = $this->getUser();
    //$share = $em->getRepository('QuizmooQuestionnaireBundle:Share')->findShareBySurveyAndAgent($questionnaire,$user);
    if ($questionnaire ){
        if($questionnaire->getUser() === $user){
        $form = $this->createForm(new QuestionnaireType(), $questionnaire);
     
        $request = $this->getRequest();

        $questions = $questionnaire->getQuestions();
       
        return $this->render('QuizmooQuestionnaireBundle:Questionnaire:analyse.'.$this->get('session')->get('whichTwig','').'html.twig', array(
          'form'    => $form->createView(),
          'questionnaire' => $questionnaire,
        ));
      }else{
        throw new AccessDeniedException('This user does not have access to this section.');
      }
    }

  }
  public function displaySingleQuestionAction($id){
    $em = $this->getDoctrine()->getManager();
    $questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
    if (!$questionnaire) {
        throw $this->createNotFoundException('Unable to find Questionnaire entity.');
    }
    $questionnaire->setDisplaySingleQuestion(1);
    $em->persist($questionnaire);
    $em->flush();
    $response = array("code" => 200, "success" => true);
    return new Response(json_encode($response));

  }
  public function displayMultipleQuestionAction($id){
    $em = $this->getDoctrine()->getManager();
    $questionnaire = $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
    if (!$questionnaire) {
            throw $this->createNotFoundException('Unable to find Questionnaire entity.');
    }
    $questionnaire->setDisplaySingleQuestion(0);
    $em->persist($questionnaire);
    $em->flush();
    $response = array("code" => 200, "success" => true);
    return new Response(json_encode($response));
  }


}