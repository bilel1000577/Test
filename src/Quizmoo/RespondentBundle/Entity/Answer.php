<?php

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="Quizmoo\RespondentBundle\Entity\AnswerRepository")
 */
class Answer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timespan", type="datetime")
     */
    private $timespan;

    /**
     * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer", mappedBy="answer", cascade={"persist","remove"})
    */
    private $multichoiceAnsr;

    /**
     * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\RankingAnswer", mappedBy="answer", cascade={"persist","remove"})
    */
    private $rankingAnsr;

     /**
     * @ORM\OneToMany(targetEntity="Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer", mappedBy="answer", cascade={"persist","remove"})
    */
    private $singletextBoxAnsr;
    
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
     * Set timespan
     *
     * @param \DateTime $timespan
     * @return Answer
     */
    public function setTimespan($timespan)
    {
        $this->timespan = $timespan;
    
        return $this;
    }

    /**
     * Get timespan
     *
     * @return \DateTime 
     */
    public function getTimespan()
    {
        return $this->timespan;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {

        $this->multichoiceAnsr = new \Doctrine\Common\Collections\ArrayCollection();


        $this->rankingAnsr = new \Doctrine\Common\Collections\ArrayCollection();


        $this->singletextBoxAnsr = new \Doctrine\Common\Collections\ArrayCollection();

    }
    
    
    /**
     * Add multichoiceAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multichoiceAnsr
     * @return Answer
     */
    public function addMultichoiceAnsr(\Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multichoiceAnsr)
    {
        $this->multichoiceAnsr[] = $multichoiceAnsr;
    
        return $this;
    }

    /**
     * Remove multichoiceAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multichoiceAnsr
     */
    public function removeMultichoiceAnsr(\Quizmoo\RespondentBundle\Entity\MultipleChoiceAnswer $multichoiceAnsr)
    {
        $this->multichoiceAnsr->removeElement($multichoiceAnsr);
    }

    /**
     * Get multichoiceAnsr
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMultichoiceAnsr()
    {
        return $this->multichoiceAnsr;
    }

    
    /**
     * Add rankingAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnsr
     * @return Answer
     */
    public function addRankingAnsr(\Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnsr)
    {
        $this->rankingAnsr[] = $rankingAnsr;
    
        return $this;
    }

    /**
     * Remove rankingAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnsr
     */
    public function removeRankingAnsr(\Quizmoo\RespondentBundle\Entity\RankingAnswer $rankingAnsr)
    {
        $this->rankingAnsr->removeElement($rankingAnsr);
    }

    /**
     * Get rankingAnsr
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRankingAnsr()
    {
        return $this->rankingAnsr;
    }

    /**
     * Add singletextBoxAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singletextBoxAnsr
     * @return Answer
     */
    public function addSingletextBoxAnsr(\Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singletextBoxAnsr)
    {
        $this->singletextBoxAnsr[] = $singletextBoxAnsr;
    
        return $this;
    }

    /**
     * Remove singletextBoxAnsr
     *
     * @param \Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singletextBoxAnsr
     */
    public function removeSingletextBoxAnsr(\Quizmoo\RespondentBundle\Entity\SingleTextBoxAnswer $singletextBoxAnsr)
    {
        $this->singletextBoxAnsr->removeElement($singletextBoxAnsr);
    }

    /**
     * Get singletextBoxAnsr
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSingletextBoxAnsr()
    {
        return $this->singletextBoxAnsr;
    }

}