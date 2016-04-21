<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatrixOfDropDownQuestionType extends QuestionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        parent::buildForm($builder,$options);

        $builder->add('questionText', 'textarea', array('label' => 'Question Text'));

        $builder->add('questionAnswer', 'textarea', array('label' => 'Row Choices: Enter each choice on a separate line'));

        $builder->add('menus', 'collection', array(
        'type' => new MenuType(),
        'allow_add' => true,
        'allow_delete' => true,
        //'by_reference' => false,
         ));

    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_matrixofdropdownquestiontype';
    }
}
