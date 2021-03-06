<?php
namespace Quizmoo\UserBundle\firewall\token;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Quizmoo\UserBundle\firewall\token\ApiToken;

class ApiTokenListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext,AuthenticationManagerInterface $authenticationManager)
    {
         $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if($request->headers->has('x-quizmoo-access-token'))
        {
        	$tokenstring=$request->headers->get('x-quizmoo-access-token');
        	$apitoken=new ApiToken();
        	$apitoken->setTokenDig($tokenstring);

        	try {
            	$authToken = $this->authenticationManager->authenticate($apitoken);
            	$this->securityContext->setToken($authToken);
                $event->stopPropagation();
	        } catch (AuthenticationException $failed) {
	            // ... you might log something here

	            // To deny the authentication clear the token. This will redirect to the login page.
	            // $this->securityContext->setToken(null);
	            // return;

	            // Deny authentication with a '403 Forbidden' HTTP response
	            $response = new Response();
	            $response->setStatusCode(403);
	            $event->setResponse($response);

	        }

        }
        else
            {
                $response = new Response();
                $response->setStatusCode(403);
                $event->setResponse($response);
            }
   
        
       
    }
}