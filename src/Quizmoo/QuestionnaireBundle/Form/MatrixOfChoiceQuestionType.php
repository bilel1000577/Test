<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatrixOfChoiceQuestionType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
       
	    $builder
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
			->add('multipleAnswer','checkbox', array('required' => false, 'label'=> 'matrixofchoicequestion.multiple_answers'))
			->add('questionAnswer', 'textarea', array('label' => 'matrixofchoicequestion.answer_options_rows'))
			->add('questionAnswerColumn', 'textarea', array('label' => 'matrixofchoicequestion.answer_options_cols'))
		
	;
    }

   
    public function getName()
    {
        return 'quizmoo_questionnairebundle_matrixofchoicequestiontype';
    }
}
