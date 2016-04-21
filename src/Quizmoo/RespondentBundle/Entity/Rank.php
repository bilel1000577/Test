<?php

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rank
 *
 * @ORM\Table(name="rank")
 * @ORM\Entity
 * @ORM\Entity()
 */
class Rank
{
	 /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\AnswerOption", inversedBy="ranks")
     */
     private $answerOption;
     
     	 /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\RespondentBundle\Entity\RankingAnswer", inversedBy="ranks")
     */
     private $rankingAnswer;
     

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
     * @ORM\Column(name="rank", type="smallint")
     */
    private $rank;


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
     * Set rank
     *
     * @param integer $rank
     * @return Rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set answerOption
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOption
     * @return Rank
     */
    public function setAnswerOption(\Quizmoo\QuestionnaireBundle\Entity\AnswerOption $answerOption = null)
    {
        $this->answerOption = $answerOption;
    
        return $this;
    }

    /**
     * Get answerOption
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\AnswerOption 
     */
    public function getAnswerOption()
    {
        return $this->answerOption;
    }

    

   

    /**
     * Set rankingAnswer
     *
     * @param \Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnswer
     * @return Rank
     */
    public function setRankingAnswer(\Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnswer = null)
    {
        $this->rankingAnswer = $rankingAnswer;
    
        return $this;
    }

    /**
     * Get rankingAnswer
     *
     * @return \Quizmoo\RespondentBundle\Entity\RankingAnswer 
     */
    public function getRankingAnswer()
    {
        return $this->rankingAnswer;
    }
}