<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RatingScaleEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		
        $builder
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
            ->add('questionAnswer', 'textarea', array('label' => 'ratingscalequestion.AddRowLabels'))
			->add('ratingScaleLevel', 'textarea', array('label' => 'ratingscalequestion.RatingScaleLevel'))
        ;
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_ratingscaletype';
    }
}
