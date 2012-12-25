<?php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


use mgate\PersonneBundle\Form;

class SuiviCcType extends DocTypeType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
	    
            DocTypeType::buildForm($builder,$options);
            
            
            
    }

    public function getName()
    {
        return 'alex_suivibundle_cctype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'mgate\SuiviBundle\Entity\Cc',
        );
    }
}

