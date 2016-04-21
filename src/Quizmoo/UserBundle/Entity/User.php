<?php
// src/Quizmoo/UserBundle/Entity/User.php
 
namespace Quizmoo\UserBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
  /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Quizmoo\QuestionnaireBundle\Entity\Questionnaire", mappedBy="user")
     */
    protected $questionnaires;
    
    
    /**
   * @ORM\Column(type="string", length=40, nullable=true)
   */
   protected $googleID;
 
 
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     *
     */
    protected $firstname;
 
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     *
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     *
     */
    protected $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    protected $email_confirmation;

    /**
     * @var string
     *
     * @ORM\Column(name="education", type="string", length=255, nullable=true)
     *
     */
    protected $education;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     *
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(name="user_location", type="string", length=255, nullable=true)
     *
     */
    protected $user_location;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_birthday", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    protected $user_birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;
 
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
 
    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }
 
    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
 
    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
 
    /**
     * Get the full name of the user (first + last name)
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastname();
    }
 
    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        $this->setUsername($facebookId);
        $this->salt = '';
    }
 
    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }
 
    /**
     * @param Array
     */
    public function setFBData($fbdata) 
    {

        if (isset($fbdata['id'])) {
            //$this->setFacebookId($fbdata['id']);
            $this->setFacebookId($fbdata['username']);
            $this->addRole('ROLE_FACEBOOK');
        }
        if (isset($fbdata['first_name'])) {
            $this->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $this->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
        if (isset($fbdata['birthday'])) {
            $time = strtotime($fbdata['birthday']);
            $newformat = date('Y-m-d H:i:s',$time);
            $this->setUserBirthday(new \DateTime($newformat));

        }
        if (isset($fbdata['location'])) {
            $this->setUserLocation($fbdata['location']['name']);
        }
        if (isset($fbdata['gender'])) {
            $this->setGender($fbdata['gender']);
        }
        if (isset($fbdata['education'])) {
            $this->setEducation($fbdata['education'][0]['school']['name']);
        }
        if (isset($fbdata['work'])) {
            $this->setPosition($fbdata['work'][0]['position']['name']);
        }
    }
    
     public function setGoogleID( $googleID )
    {
    $this->googleID = $googleID;
    }

    public function getGoogleID( )
   {
    return $this->googleID;
   }
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {   
        parent::__construct();
        $this->questionnaires = new \Doctrine\Common\Collections\ArrayCollection();

        if($this->facebookId !="")
        $this->roles = array('ROLE_FACEBOOK');
    }
    
    /**
     * Add questionnaires
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaires
     * @return User
     */
    public function addQuestionnaire(\Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaires)
    {
        $this->questionnaires[] = $questionnaires;
    
        return $this;
    }

    /**
     * Remove questionnaires
     *
     * @param \Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaires
     */
    public function removeQuestionnaire(\Quizmoo\QuestionnaireBundle\Entity\Questionnaire $questionnaires)
    {
        $this->questionnaires->removeElement($questionnaires);
    }

    /**
     * Get questionnaires
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaires()
    {
        return $this->questionnaires;
    }

    /**
     * Set user_location
     *
     * @param string $userLocation
     * @return User
     */
    public function setUserLocation($userLocation)
    {
        $this->user_location = $userLocation;
    
        return $this;
    }

    /**
     * Get user_location
     *
     * @return string 
     */
    public function getUserLocation()
    {
        return $this->user_location;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set education
     *
     * @param string $education
     * @return User
     */
    public function setEducation($education)
    {
        $this->education = $education;
    
        return $this;
    }

    /**
     * Get education
     *
     * @return string 
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set user_birthday
     *
     * @param \DateTime $userBirthday
     * @return User
     */
    public function setUserBirthday($userBirthday)
    {
        $this->user_birthday = $userBirthday;
    
        return $this;
    }

    /**
     * Get user_birthday
     *
     * @return \DateTime 
     */
    public function getUserBirthday()
    {
        return $this->user_birthday;
    }
    

    /**
     * Set email_confirmation
     *
     * @param string $emailConfirmation
     * @return User
     */
    public function setEmailConfirmation($emailConfirmation)
    {
        $this->email_confirmation = $emailConfirmation;
    
        return $this;
    }

    /**
     * Get email_confirmation
     *
     * @return string 
     */
    public function getEmailConfirmation()
    {
        return $this->email_confirmation;
    }
}