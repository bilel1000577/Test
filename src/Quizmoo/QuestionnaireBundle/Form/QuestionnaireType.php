<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('questionnaireName','text', array('label' => 'questionnaire.s_title'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Quizmoo\QuestionnaireBundle\Entity\Questionnaire'
        ));
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_questionnairetype';
    }
}
