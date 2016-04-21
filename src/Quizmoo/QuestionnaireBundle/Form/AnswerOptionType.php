<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnswerOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answerTitle', 'text')
            ->add('answerText', 'textarea')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Quizmoo\QuestionnaireBundle\Entity\AnswerOption'
        ));
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_answeroptiontype';
    }
}
