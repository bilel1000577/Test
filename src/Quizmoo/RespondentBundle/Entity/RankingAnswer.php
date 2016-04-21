<?php

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy ;

/**
 * RankingAnswer
 *
 * @ORM\Table(name="rankinganswer")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Quizmoo\RespondentBundle\Entity\RankingAnswerRepository")
 */
class RankingAnswer
{
	 /**
	 * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\Rank", mappedBy="rankingAnswer", cascade={"persist","remove"})
     * @OrderBy({"rank" = "ASC"})
     */
	private $ranks;

	/**
	 * @ORM\ManyToOne(targetEntity="Quizmoo\QuestionnaireBundle\Entity\RankingQuestion", inversedBy="rankingQuestionAnswers") 
	 */
	protected $rankingQuestion;

	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Quizmoo\RespondentBundle\Entity\Answer", inversedBy="rankingAnsr", cascade={"persist"})
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ranks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add ranks
     *
     * @param \Quizmoo\RespondentBundle\Entity\Rank $ranks
     * @return RankingQuestionAnswer
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
     * Set rankingQuestion
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\RankingQuestion $rankingQuestion
     * @return RankingAnswer
     */
    public function setRankingQuestion(\Quizmoo\QuestionnaireBundle\Entity\RankingQuestion $rankingQuestion = null)
    {
        $this->rankingQuestion = $rankingQuestion;
    
        return $this;
    }

    /**
     * Get rankingQuestion
     *
     * @return \Quizmoo\QuestionnaireBundle\Entity\RankingQuestion 
     */
    public function getRankingQuestion()
    {
        return $this->rankingQuestion;
    }

    /**
     * Set answer
     *
     * @param \Quizmoo\RespondentBundle\Entity\Answer $answer
     * @return RankingAnswer
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