<?php

/**
 * Description of MultipleChoiceAnswer
 *
 * @author zizoujab
 */

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MultipleChoiceAnswer
 *
 * @ORM\Table(name="multiplechoiceanswer")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswerRepository")
 */
class MultipleChoiceAnswer {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToMany(targetEntity="Quizmoo\QuestionnaireBundle\Entity\AnswerOption" , inversedBy="multipleChoiceAnswers" )
	 *  
	 */
	private $answerOptions;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\MultipleChoice", inversedBy="multipleChoiceAnswers")
     * @ORM\JoinColumn(nullable=false)
     */
	private $multipleChoiceQuestion;

     /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\RespondentBundle\Entity\Answer", inversedBy="multichoiceAnsr", cascade={"persist"})
     * 
     */
    private $answer;    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    
    public function setMultipleChoiceQuestion(\Quizmoo\QuestionnaireBundle\Entity\MultipleChoice $multipleChoiceQuestion)
    {
        $this->multipleChoiceQuestion = $multipleChoiceQuestion;
    
        return $this;
    }

    /**
     * Get multipleChoiceQuestion
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\MultipleChoice 
     */
    public function getMultipleChoiceQuestion()
    {
        return $this->multipleChoiceQuestion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answerOptions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    

    /**
     * Add answerOptions
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOptions
     * @return MultipleChoiceAnswer
     */
    public function addAnswerOption(\Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOptions)
    {
        $this->answerOptions[] = $answerOptions;
    
        return $this;
    }

    /**
     * Remove answerOptions
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOptions
     */
    public function removeAnswerOption(\Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOptions)
    {
        $this->answerOptions->removeElement($answerOptions);
    }

    /**
     * Get answerOptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswerOptions()
    {
        return $this->answerOptions;
    }

    /**
     * Set answer
     *
     * @param \Quizmoo\RespondentBundle\Entity\Answer $answer
     * @return MultipleChoiceAnswer
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