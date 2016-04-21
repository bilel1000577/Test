<?php

namespace Quizmoo\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstname', 'text', array('required' => true,'label' => 'register.firstname'));
        $builder->add('lastname', 'text', array('required' => true,'label' => 'register.lastname'));
        $builder->add('gender', 'choice', array(
                     'required' => true,
                     'label'=> 'register.gender',
                     'multiple' => false,
                     'empty_value' => 'register.anoption',
                     'choices' => array(
                            'homme' => 'register.homme',
                            'femme' => 'register.femme'
                    )));
        $builder->add('education', 'choice', array(
                     'required' => true,
                     'label'=> 'register.education',
                     'multiple' => false,
                     'empty_value' => 'register.anoption',
                     'choices' => array(
                            'ecole' => 'register.ecole',
                            'lycee' => 'register.lycee',
                            'universite' => 'register.universite',
                            'mba' => 'register.mba',
                            'doctorat' => 'register.doctorat'

                    )));
        $builder->add('position', 'text', array('required' => true,'label' => 'register.position'));
        $builder->add('user_location', 'text', array('required' => true,'label' => 'register.location'));
        $builder->add('user_birthday', 'birthday', array(
        	'label' => 'register.birthday',
            'format' => 'dd - MMMM - yyyy',
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y')-70)
        ));
    }

    public function getName()
    {
        return 'quizmoo_user_edit_profile';
    }
}