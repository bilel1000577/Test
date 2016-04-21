<?php

namespace Quizmoo\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MultipleChoice
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity()
 */
class MultipleChoice extends Question
{
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isSingle", type="boolean")
     */
    private $isSingle;

    
    /**
	 * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer", mappedBy="multipleChoiceQuestion", cascade={"persist","remove"})
	 */
    private $multipleChoiceAnswers;

    /**
     * Set isSingle
     *
     * @param boolean $isSingle
     * @return MultipleChoice
     */
    public function setIsSingle($isSingle)
    {
        $this->isSingle = $isSingle;
    
        return $this;
    }

    /**
     * Get isSingle
     *
     * @return boolean 
     */
    public function getIsSingle()
    {
        return $this->isSingle;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->multipleChoiceAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add multipleChoiceAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multipleChoiceAnswers
     * @return MultipleChoice
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

    /**
     * Get multipleChoiceAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMultipleChoiceAnswers()
    {
        return $this->multipleChoiceAnswers;
    }
    public function numberOfAnswers(){
        return count($this->multipleChoiceAnswers);
    }
}