<?php

namespace Quizmoo\QuestionnaireBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Quizmoo\QuestionnaireBundle\Entity\QuestionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"MultipleChoice" = "MultipleChoice",  
 * "SingleTextBoxQuestion" = "SingleTextBoxQuestion",
 * "RankingQuestion" = "RankingQuestion" })
 */
class Question 
{	 
	 /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\Questionnaire", inversedBy="questions" ,cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
     private $questionnaire;

	 /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\QuestionType", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    //private $questionType;
	  /**
     * @var string
     *
     * @ORM\Column(name="questionType", type="text")
     */
     private $questionType;
     
	 /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="questionOrder", type="integer",nullable=true)
     * 
     */
    private $questionOrder;
    
     
    /**
     * @var string
     *
     * @ORM\Column(name="questionText", type="text")
     */
    private $questionText;
     /**
     * @var text
     *
     * @ORM\Column(name="questionAnwser", type="text", nullable=true)
     */
    private $questionAnswer;
    //questionAnswer refer to questionAnswerRow
	
	/**
     * @var text $type
     *
     */
     private $type;
	  
	 /**
	 * @ORM\OneToMany(targetEntity="Quizmoo\QuestionnaireBundle\Entity\AnswerOption", mappedBy="question", cascade={"persist","remove"})
     */
     private $answerOptions;
	 
	 public function __construct()
	{
		$this->answerOptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->questionOrder = 1 ;
		
	}
	
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
     * Set questionText
     *
     * @param string $questionText
     * @return Question
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;
    
        return $this;
    }

    /**
     * Get questionText
     *
     * @return string 
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }

    /**
     * Set questionType
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\QuestionType $questionType
     * @return Question
     */
    /*public function setQuestionType(\Quizmoo\QuestionnaireBundle\Entity\QuestionType $questionType)
    {
        $this->questionType = $questionType;
    
        return $this;
    }*/

    /**
     * Get questionType
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\QuestionType 
     */
    /*public function getQuestionType()
    {
        return $this->questionType;
    }*/
	
	 /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add answerOptions
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOptions
     * @return Question
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
     * Set questionAnswer
     *
     * @param string $questionAnswer
     * @return Question
     */
    public function setQuestionAnswer($questionAnswer)
    {
        $this->questionAnswer = $questionAnswer;
    
        return $this;
    }

    /**
     * Get questionAnswer
     *
     * @return string 
     */
    public function getQuestionAnswer()
    {
        return $this->questionAnswer;
    }
    
    public function cloneAnswerOptions()
    {
	    $cloneAnswerOptions = $this->answerOptions;
	    $this->answerOptions = new ArrayCollection();
	    
	    foreach($cloneAnswerOptions as $answerOp)
	    {
		    $cloneAnserOption = clone $answerOp ; 
		    $cloneAnserOption->setQuestion($this);
		    $this->answerOptions->add($cloneAnserOption );
	    }
	    
    }

    /**
     * Set questionnaire
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return Question
     */
    public function setQuestionnaire(\Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaire)
    {
        $this->questionnaire = $questionnaire;
    
        return $this;
    }

    /**
     * Get questionnaire
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\Questionnaire 
     */
    public function getQuestionnaire()
    {
        return $this->questionnaire;
    }

    /**
     * Set questionOrder
     *
     * @param integer $questionOrder
     * @return Question
     */
    public function setQuestionOrder($questionOrder)
    {
        $this->questionOrder = $questionOrder;
    
        return $this;
    }

    /**
     * Get questionOrder
     *
     * @return integer 
     */
    public function getQuestionOrder()
    {
        return $this->questionOrder;
    }

    /**
     * Set questionType
     *
     * @param string $questionType
     * @return Question
     */
    public function setQuestionType($questionType)
    {
        $this->questionType = $questionType;
    
        return $this;
    }

    /**
     * Get questionType
     *
     * @return string 
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }
}