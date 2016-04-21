<?php
namespace Quizmoo\UserBundle\Controller;
use Quizmoo\QuestionnaireBundle\BusinessLogic\Utils;
use FOS\UserBundle\Controller\SecurityController as BseSecurityController ;


class SecurityController extends BseSecurityController {

	/**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {

        $isDevice = Utils::isMobile() ? 'mobile.':'' ;

        $template = sprintf('FOSUserBundle:Security:login.'.$isDevice.'html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }

}