<?php

namespace Quizmoo\QuestionnaireBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
/**
 * QuestionType
 *
 * @ORM\Table(name="questiontype")
 * @ORM\Entity(repositoryClass="Quizmoo\QuestionnaireBundle\Entity\QuestionTypeRepository")
 */
class QuestionType implements Translatable
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
     * @var integer
     *
     * @ORM\Column(name="order", type="integer",nullable=true)
     * 
     */
    private $order;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="questionTypeName", type="string", length=255)
     */
    private $questionTypeName;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;


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
     * Set questionTypeName
     *
     * @param string $questionTypeName
     * @return QuestionType
     */
    public function setQuestionTypeName($questionTypeName)
    {
        $this->questionTypeName = $questionTypeName;
    
        return $this;
    }

    /**
     * Get questionTypeName
     *
     * @return string 
     */
    public function getQuestionTypeName()
    {
        return $this->questionTypeName;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(){
        return $this->locale;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return QuestionType
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}