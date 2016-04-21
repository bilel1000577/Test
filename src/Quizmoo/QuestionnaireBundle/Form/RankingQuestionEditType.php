<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RankingQuestionEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
    
        $builder
        ->add('questionText', 'textarea', array('label' => 'question.QuestionText'))
        ->add('questionAnswer', 'textarea', array('label' => 'rankingquestion.answer_choices'))
        ;
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_rankingquestionedittype';
    }
}
