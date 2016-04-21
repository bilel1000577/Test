<?php

namespace Quizmoo\QuestionnaireBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection ;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy ;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

define('ONGOING_QUESTIONNAIRE',0);
define('DRAFT_QUESTIONNAIRE',1);
define('CLOSED_QUESTIONNAIRE',2);
define('NO_STATE',3);
define('RECEIVED_QUESTIONNAIRE',4);
/**
 * Questionnaire
 *
 * @ORM\Table(name="questionnaire")
 * @ORM\Entity(repositoryClass="Quizmoo\QuestionnaireBundle\Entity\QuestionnaireRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Questionnaire {

	/**
	 * @ORM\OneToMany(targetEntity="Quizmoo\QuestionnaireBundle\Entity\Question", mappedBy="questionnaire", cascade={"persist","remove"})
	 * @OrderBy({"questionOrder" = "ASC"})
     */
     private $questions;
	
	  /**
	 * @ORM\ManyToOne(targetEntity="Quizmoo\UserBundle\Entity\User", inversedBy="questionnaires")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	  private $user;

       
    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255, unique=true)
     * 
     */
    private $hash;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer",nullable=true)
     */
    private $state = ONGOING_QUESTIONNAIRE ;


    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfAnswers", type="integer",nullable=true)
     */
    private $numberOfAnswers ;
    
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
	 * @ORM\Column(name="questionnaireName", type="string", length=255,nullable=true)
	 */
	private $questionnaireName;

	/**
	 * @var text
	 *
	 * @ORM\Column(name="description", type="text",nullable=true)
	 */
	private $description;
        
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created", type="datetime")
	 * @Assert\DateTime()
	 */
	private $created;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="modified", type="datetime")
	 * @Assert\DateTime()
	 */
	private $modified;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isTemplate", type="boolean",nullable=true)
     * 
     */
    private $isTemplate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displaySingleQuestion", type="boolean",nullable=true)
     * 
     */
    private $displaySingleQuestion;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isPredefined", type="boolean",nullable=true)
     *
     */
    private $isPredefined;

	public function __construct() 
    {
		$this->created = new \Datetime();
		$this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->displaySingleQuestion = 0;
        $this->numberOfAnswers = 0;
        //$this->setIsTemplate(false);
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
	 * Set questionnaireName
	 *
	 * @param string $questionnaireName
	 * @return Questionnaire
	 */
	public function setQuestionnaireName($questionnaireName) {
		$this->questionnaireName = $questionnaireName;

		return $this;
	}

	/**
	 * Get questionnaireName
	 *
	 * @return string 
	 */
	public function getQuestionnaireName() {
		return stripslashes($this->questionnaireName);
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Questionnaire
	 */
	public function setCreated($created) {
		$this->created = $created;

		return $this;
	}

	/**
	 * Get created
	 *
	 * @return \DateTime 
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * Set modified
	 *
	 * @param \DateTime $modified
	 * @return Questionnaire
	 */
	public function setModified($modified) {
		$this->modified = $modified;

		return $this;
	}

	/**
	 * Get modified
	 *
	 * @return \DateTime 
	 */
	public function getModified() {
		return $this->modified;
	}

	//à adapter
	//Cette méthode doit définir l'attribut $modified à la date actuelle, afin de mettre à jour automatiquement la date d'edition d'un questionnaire
	/**
	 * @ORM\PrePersist
	 */
	public function updateDate() {
		$this->setModified(new \Datetime());
                
	}
         
	/**
	 * @ORM\PrePersist
	 */
    public function hashQuestion()
    {

        $this->hash= md5($this->questionnaireName.time());
    }
	
	public function cloneQuestions()
	{
		$questions = $this->questions;
		
		$this->questions = new ArrayCollection();
		foreach ($questions as $question )
		{
			
			$cloneQuestion = clone $question;
			$cloneQuestion-> setQuestionnaire($this);
			$cloneQuestion->cloneAnswerOptions();
			$this->questions->add($cloneQuestion);
			
		}
	}

   
    /**
     * Set hash
     *
     * @param string $hash
     * @return Questionnaire
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set user
     *
     * @param \Quizmoo\UserBundle\Entity\User $user
     * @return Questionnaire
     */
    public function setUser(\Quizmoo\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Quizmoo\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
   

    /**
     * Add questions
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Question $questions
     * @return Questionnaire
     */
    public function addQuestion(\Quizmoo\QuestionnaireBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;
    
        return $this;
    }

    /**
     * Remove questions
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Question $questions
     */
    public function removeQuestion(\Quizmoo\QuestionnaireBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    

    /**
     * Set active
     *
     * @param boolean $active
     * @return Questionnaire
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Questionnaire
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set numberOfAnswers
     *
     * @param integer $numberOfAnswers
     * @return Questionnaire
     */
    public function setNumberOfAnswers($numberOfAnswers)
    {
        $this->numberOfAnswers = $numberOfAnswers;
    
        return $this;
    }

    /**
     * Get numberOfAnswers
     *
     * @return integer 
     */
    public function getNumberOfAnswers()
    {
        return $this->numberOfAnswers;
    }


    /**
     * Set isTemplate
     *
     * @param boolean $isTemplate
     * @return Questionnaire
     */

    public function setIsTemplate($isTemplate)
    {
        $this->isTemplate = $isTemplate;
    
        return $this;
    }

     /**
     * Get isTemplate
     *
     * @return boolean 
     */   

    public function getIsTemplate() {
        return $this->isTemplate;
    }
    
    /**
     * Set isPredefined
     *
     * @param boolean $isPredefined
     * @return Questionnaire
     */
    
    public function setIsPredefined($isPredefined)
    {
    	$this->isPredefined = $isPredefined;
    
    	return $this;
    }
    
    /**
     * Get isPredefined
     *
     * @return boolean
     */
    
    public function getIsPredefined() {
    	return $this->isPredefined;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Questionnaire
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * Set displaySingleQuestion
     *
     * @param boolean $displaySingleQuestion
     * @return Questionnaire
     */
    public function setDisplaySingleQuestion($displaySingleQuestion)
    {
        $this->displaySingleQuestion = $displaySingleQuestion;
    
        return $this;
    }

    /**
     * Get displaySingleQuestion
     *
     * @return boolean 
     */
    public function getDisplaySingleQuestion()
    {
        return $this->displaySingleQuestion;
    }
}