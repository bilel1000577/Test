<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultipleChoiceType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         parent::buildForm($builder,$options);
         
	     $builder 
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
			//isSingle test une ou plusieurs réponse
			->add('isSingle', 'checkbox', array('required' => false, 'label'=> 'multiplechoicequestion.check_single_answers'))
			->add('questionAnswer', 'textarea',array('label' => 'multiplechoicequestion.answers_choices'))
			;
    }
   
    public function getName()
    {
        return 'quizmoo_questionnairebundle_multiplechoicetype';
    }
}
