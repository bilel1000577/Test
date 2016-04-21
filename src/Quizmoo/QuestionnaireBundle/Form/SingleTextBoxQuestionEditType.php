<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SingleTextBoxQuestionEditType extends AbstractType
{
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('questionText', 'textarea', array('label' => 'question.QuestionText'));
		
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion'
        ));
    }
    public function getName()
    {
        return 'quizmoo_questionnairebundle_singletextboxquestion_edittype';
    }
}
