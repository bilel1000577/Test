<?php

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SingleTextBoxAnswer
 * @ORM\Table(name="singletextboxanswer")
 * @ORM\Entity(repositoryClass="Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswerRepository")
 */
class SingleTextBoxAnswer
{
	
	
	/**
     * @ORM\ManyToOne(targetEntity="Quizmoo\RespondentBundle\Entity\Answer", inversedBy="singletextBoxAnsr", cascade={"persist"})
     * 
     */
    private $answer;
	/**
	 * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion", inversedBy="singleTextBoxAnswers")
	 */
	private $singleTexBoxQuestion;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="answerText", type="text")
     */
    private $answerText;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answerText
     *
     * @param string $answerText
     * @return SingleTextBoxAnswer
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;
    
        return $this;
    }

    /**
     * Get answerText
     *
     * @return string 
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

   

    /**
     * Set singleTexBoxQuestion
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion $singleTexBoxQuestion
     * @return SingleTextBoxAnswer
     */
    public function setSingleTexBoxQuestion(\Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion $singleTexBoxQuestion = null)
    {
        $this->singleTexBoxQuestion = $singleTexBoxQuestion;
    
        return $this;
    }

    /**
     * Get singleTexBoxQuestion
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion 
     */
    public function getSingleTexBoxQuestion()
    {
        return $this->singleTexBoxQuestion;
    }

    /**
     * Set answer
     *
     * @param \Quizmoo\RespondentBundle\Entity\Answer $answer
     * @return SingleTextBoxAnswer
     */
    public function setAnswer(\Quizmoo\RespondentBundle\Entity\Answer $answer = null)
    {
        $this->answer = $answer;
    
        return $this;
    }

    /**
     * Get answer
     *
     * @return \Quizmoo\RespondentBundle\Entity\Answer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}