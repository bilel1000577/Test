<?php
// src/Quizmoo/QuestionnaireBundle/DataFixtures/ORM/QuestionTypes.php
 
namespace Quizmoo\QuestionnaireBundle\DataFixtures\ORM;
 
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quizmoo\QuestionnaireBundle\Entity\QuestionType;
 
class QuestionTypes implements FixtureInterface
{

  public function load(ObjectManager $manager)
  {
     $names_en = array('Single TextBox Question', 'Multiple TextBox Question','Ranking Question');

     $noms_fr = array("Question ouverte","Question à plusieurs réponses ouvertes","Mettre dans l'ordre");

    foreach($noms_fr as $i => $nom)
    {
      
      $liste_QuestionTypes[$i] = new QuestionType();
      $liste_QuestionTypes[$i]->setTranslatableLocale('fr');
      $liste_QuestionTypes[$i]->setQuestionTypeName($nom);
 
    
      $manager->persist($liste_QuestionTypes[$i]);
      
    }
    $manager->flush();

    foreach($names_en as $i => $name)
    {
      $liste_QuestionTypes[$i]->setTranslatableLocale('en');
      $liste_QuestionTypes[$i]->setQuestionTypeName($name);
    }
    $manager->flush();
  }

}