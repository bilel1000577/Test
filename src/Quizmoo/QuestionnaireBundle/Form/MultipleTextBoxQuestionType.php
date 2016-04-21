<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultipleTextBoxQuestionType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
		parent::buildForm($builder,$options);
        
        $builder
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
			->add('questionAnswer', 'textarea', array('label' => 'multipletextboxquestion.box_choices'));
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_multipletextboxquestiontype';
    }
}
