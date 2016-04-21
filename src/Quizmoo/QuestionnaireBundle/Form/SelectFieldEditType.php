<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SelectFieldEditType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         
         $builder 
            ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
            ->add('isSingleField', 'checkbox', array('required' => false, 'label'=> 'select_field.multiple'))
            ->add('preferedChoice', 'choice', array(
                'required' => true,
                'label'=> 'select_field.liste',
                'multiple' => false,
                'empty_value' => 'select_field.empty',
                'choices' => array(
                    0 => 'select_field.nombre',
                    1 => 'select_field.text'
                )))
            ->add('questionAnswer', 'textarea',array('label' => 'select_field.options'))
            ->add('increment', 'integer',array('label' => 'select_field.increment'))
            ->add('firstValue', 'integer',array('label' => 'select_field.de'))
            ->add('lastValue', 'integer',array('label' => 'select_field.a'))
            ;
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_selectfieldtypeedit';
    }
}
