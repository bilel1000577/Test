<?php

namespace Quizmoo\QuestionnaireBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class QuestionnaireControllerTest extends WebTestCase 
{
	private $client = null ;

	public function setUp(){
		$this->client = static::createClient();
	}


	public function testEditQuestion(){
		$this->logIn();

		
		 $crawler = $this->client->request('GET', '/questionnaires/ongoing/1/All');
			var_dump($crawler);
			die();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

	}



	public function logIn(){

		$session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('zizoujab', 'zied8jab', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
	}
}
?>
