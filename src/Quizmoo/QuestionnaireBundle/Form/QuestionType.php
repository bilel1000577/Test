<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('questionType', 'entity', array(
                  'class'    => 'QuizmooQuestionnaireBundle:QuestionType',
                  'property' => 'questionTypeName',
                  'multiple' => false,
                  'empty_value' => 'select.anoption',
                  'label' => 'question.label',
                  'query_builder' => function (\Quizmoo\QuestionnaireBundle\Entity\QuestionTypeRepository $repository)
                     {
                         return $repository->createQueryBuilder('s')
                                ->Where('s.order < 7')
                                ->add('orderBy', 's.order ASC');
                     }
                  )) */
            ->add('type', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Quizmoo\QuestionnaireBundle\Entity\Question',
        ));
    }

    public function getName()
    {
        return 'quizmoo_questionnairebundle_questiontype';
    }
}
