<?php

namespace Quizmoo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Table(name="token")
 * @ORM\Entity(repositoryClass="Quizmoo\UserBundle\Entity\TokenRepository")
 */
class Token
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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255,unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="datetime")
     */
    private $creationdate;
    /**
    * @ORM\OneToOne(targetEntity="User")
    * @ORM\JoinColumn(name="userID", referencedColumnName="id")
    */
    private $client;

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
     * Set token
     *
     * @param string $token
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return Token
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;
    
        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime 
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Gets the value of client.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets the value of client.
     *
     * @param mixed $client the client
     *
     * @return self
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }
}
