<?php

namespace Quizmoo\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionnaireEditType extends QuestionnaireType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On fait appel � la m�thode buildForm du parent, qui va ajouter tous les champs � $builder
		parent::buildForm($builder, $options);
    }
    public function getName()
    {
        return 'quizmoo_questionnairebundle_questionnaireedittype';
    }
}
