<?php

namespace Quizmoo\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SingleTextBoxQuestion
 *
 * @ORM\Table(name="singletextboxquestion")
 * @ORM\Entity
 */
class SingleTextBoxQuestion extends Question
{
	/**
	 * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer", mappedBy="singleTexBoxQuestion", cascade={"persist","remove"})
	*/
	private $singleTextBoxAnswers;
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->singleTextBoxAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get singleTextBoxAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSingleTextBoxAnswers()
    {
        return $this->singleTextBoxAnswers;
    }

    public function numberOfAnswers(){
        return sizeof($this->singleTextBoxAnswers);
    }

    /**
     * Add singleTextBoxAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singleTextBoxAnswers
     * @return SingleTextBoxQuestion
     */
    public function addSingleTextBoxAnswer(\Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singleTextBoxAnswers)
    {
        $this->singleTextBoxAnswers[] = $singleTextBoxAnswers;
    
        return $this;
    }

    /**
     * Remove singleTextBoxAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singleTextBoxAnswers
     */
    public function removeSingleTextBoxAnswer(\Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singleTextBoxAnswers)
    {
        $this->singleTextBoxAnswers->removeElement($singleTextBoxAnswers);
    }
}