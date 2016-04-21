<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PictorialQuestionType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
		parent::buildForm($builder,$options);
        
        $builder
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
            ->add('file', 'file', array('required'  => true,'label' => 'pictorialquestion.upload'))
        ;
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_pictorialquestiontype';
    }
}
