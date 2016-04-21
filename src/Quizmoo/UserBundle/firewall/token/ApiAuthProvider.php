<?php
namespace Quizmoo\UserBundle\firewall\token;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Quizmoo\UserBundle\firewall\token\ApiToken;
Const PERIOD=3600;
class ApiAuthProvider implements AuthenticationProviderInterface
{
    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function authenticate(TokenInterface $token)
    {
        $em=$this->doctrine->getEntityManager();
        $tokens = $em->getRepository('QuizmooUserBundle:Token')->findby(array('token' => $token->getTokenDig() ));
        if(count($tokens)==0)
            return;

        $apitoken=$tokens[0];
        if($apitoken && $this->validToken($apitoken))
        {
            $authenticatedToken=new ApiToken();
            $authenticatedToken->setUser($apitoken->getClient());
            return $authenticatedToken;
        }
        throw new AuthenticationException('The API authentication failed.');
    }

    private function validToken($apitoken)
    {
        if($apitoken)
        {
            $expireDate=$apitoken->getCreationdate()->add(new \DateInterval('PT'.PERIOD.'S'));

            $today =new \DateTime("NOW",new \DateTimeZone('UTC'));
            if($today<$expireDate)
                return true;
            else
            {
                return false;
            }
        }
        else
            return false;
    }
    public function supports(TokenInterface $token)
    {
        return $token instanceof ApiToken;
    }
}