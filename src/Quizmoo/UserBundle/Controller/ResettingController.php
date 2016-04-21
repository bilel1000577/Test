<?php
namespace Quizmoo\UserBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\ResettingController as BaseController;

class ResettingController extends BaseController
{
    const SESSION_EMAIL = 'fos_user_send_resetting_email/email';

     public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail_modified.html.'.$this->getEngine(), array(
            'email' => $email,
            
        ));
    }

}
