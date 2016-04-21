<?php

namespace Quizmoo\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RankingQuestion
 *
 * @ORM\Table(name="rankingquestion")
 * @ORM\Entity(repositoryClass="Quizmoo\QuestionnaireBundle\Entity\RankingQuestionRepository")
 */
class RankingQuestion extends Question
{
	 /**
	 * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\RankingAnswer", mappedBy="rankingQuestion",cascade={"persist","remove"})
	 */
	private $rankingQuestionAnswers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rankingQuestionAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add rankingQuestionAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\RankingQuestionAnswer $rankingQuestionAnswers
     * @return RankingQuestion
     */
    public function addRankingQuestionAnswer(\Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingQuestionAnswers)
    {
        $this->rankingQuestionAnswers[] = $rankingQuestionAnswers;
    
        return $this;
    }

    /**
     * Remove rankingQuestionAnswers
     *
     * @param \Quizmoo\RespondentBundle\Entity\RankingQuestionAnswer $rankingQuestionAnswers
     */
    public function removeRankingQuestionAnswer(\Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingQuestionAnswers)
    {
        $this->rankingQuestionAnswers->removeElement($rankingQuestionAnswers);
    }

    /**
     * Get rankingQuestionAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRankingQuestionAnswers()
    {
        return $this->rankingQuestionAnswers;
    }
    public function numberOfAnswers(){
        return count($this->rankingQuestionAnswers);
    }
}