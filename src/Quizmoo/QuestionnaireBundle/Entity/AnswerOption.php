<?php

namespace Quizmoo\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnswerOption
 *
 * @ORM\Table(name="answeroption")
 * @ORM\Entity(repositoryClass="Quizmoo\QuestionnaireBundle\Entity\AnswerOptionRepository")
 */
class AnswerOption
{   

     /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\Question", inversedBy="answerOptions")
     * @ORM\JoinColumn(nullable=false)
     */
     private $question;
     
     
     /**
     * @ORM\ManyToMany(targetEntity="Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer" , mappedBy="answerOptions" )
     *  
     */
    private $multipleChoiceAnswers;

    
     /**
     * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\Rank", mappedBy="answerOption", cascade={"persist","remove"})
     */
    private $ranks;
     
    
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
     * @ORM\Column(name="answerText", type="text", nullable=true)
     */
    private $answerText;
    
    /**
     * @var string
     *
     * @ORM\Column(name="answerTitle", type="string", length=255, nullable=true)
     */
    private $answerTitle;

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
     * @return AnswerOption
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
     * Set answerTitle
     *
     * @param string $answerTitle
     * @return AnswerOption
     */
    public function setAnswerTitle($answerTitle)
    {
        $this->answerTitle = $answerTitle;
    
        return $this;
    }

    /**
     * Get answerTitle
     *
     * @return string 
     */
    public function getAnswerTitle()
    {
        return $this->answerTitle;
    }

    /**
     * Set question
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Question $question
     * @return AnswerOption
     */
    public function setQuestion(\Quizmoo\QuestionnaireBundle\Entity\Question $question)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->multipleChoiceAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * Get multipleChoiceAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMultipleChoiceAnswers()
    {
        return $this->multipleChoiceAnswers;
    }

    /**
     * Add ranks
     *
     * @param \Quizmoo\RespondentBundle\Entity\Rank $ranks
     * @return AnswerOption
     */
    public function addRank(\Quizmoo\RespondentBundle\Entity\Rank $ranks)
    {
        $this->ranks[] = $ranks;
    
        return $this;
    }

    /**
     * Remove ranks
     *
     * @param \Quizmoo\RespondentBundle\Entity\Rank $ranks
     */
    public function removeRank(\Quizmoo\RespondentBundle\Entity\Rank $ranks)
    {
        $this->ranks->removeElement($ranks);
    }

    /**
     * Get ranks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Add multipleChoiceAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multipleChoiceAnswers
     * @return AnswerOption
     */
    public function addMultipleChoiceAnswer(\Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multipleChoiceAnswers)
    {
        $this->multipleChoiceAnswers[] = $multipleChoiceAnswers;
    
        return $this;
    }

    /**
     * Remove multipleChoiceAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multipleChoiceAnswers
     */
    public function removeMultipleChoiceAnswer(\Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multipleChoiceAnswers)
    {
        $this->multipleChoiceAnswers->removeElement($multipleChoiceAnswers);
    }

    public function setScale( $scale ){
        $this->scale = $scale ;
    }

    public function getScale(){
        return $this->scale ;
    }
}