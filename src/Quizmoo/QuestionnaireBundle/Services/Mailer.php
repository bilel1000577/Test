<?php 
namespace Quizmoo\QuestionnaireBundle\Services;

 
class Mailer
{
   
   
    private $mailer ;
    
    public function __construct($mailer) 
    {
     $this->mailer  = $mailer;
     
    }
    
    public function sendEmail($to,$body,$subject='',$from)
    {
       
        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setContentType('text/html')
                ->setBody($body);
        $this->mailer->send($message);
    }
}