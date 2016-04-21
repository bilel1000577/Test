<?php
namespace Quizmoo\UserBundle\Listeners;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;
class RessourceCheckListener
{

	private $container;
	private $doctrine;
	private $twig;
	public function __construct(ContainerInterface $container,$doctrine,$twig)
	{
	    $this->container = $container;
	    $this->doctrine=$doctrine;
	    $this->twig=$twig;
	}
	public function onRequest(GetResponseEvent $event)
	{
		$this->checkIfUserCanAccessSurvey($event);
		
		
	}
	
	private function checkIfUserCanAccessSurvey(GetResponseEvent $event)
	{
		$securityContext = $this->container->get('security.context');
		try{
			if($securityContext)
			{
				if($securityContext->getToken())
				{
					if(gettype($securityContext->getToken()->getUser())!="string")
					{
						$User=$securityContext->getToken()->getUser()->getId();
						
						$request   = $event->getRequest();
						//check one questionnaire 
						$questionnaire_id=$request->get("id");
						//check multiple questionnaies
						$ids=$request->get("questionnaires_ids");
						if($ids)
						{
							$questionnaire_ids=explode(",", $ids);
							$em=$this->doctrine->getEntityManager();
							if(count($questionnaire_ids)>0)
							{
								foreach ($questionnaire_ids as $key) {
									if($User!=$em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($key)->getUser()->getId())
									{
										$tmpl=$this->twig->render("QuizmooUserBundle:Error:forbidden.html.twig");
										$response=new Response($tmpl);
										$response->setStatusCode(403);
										$event->setResponse($response);
										break;
									}
								}
							}
						}
						if($questionnaire_id)
						{
							$em=$this->doctrine->getEntityManager();
							$questionnnaire  =  $em->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($questionnaire_id);
							if($questionnnaire)
							{
								if($User!=$questionnnaire->getUser()->getId())
								{
									$tmpl=$this->twig->render("QuizmooUserBundle:Error:forbidden.html.twig");
									$response=new Response($tmpl);
									$response->setStatusCode(403);
									$event->setResponse($response);
								}
							}
						}
					}
				}
			}
		}
		catch(Exception $e)
		{

		}
	}
}