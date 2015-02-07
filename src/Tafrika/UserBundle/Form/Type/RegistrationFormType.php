<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 29/01/15
 * Time: 15:09
 */

namespace Tafrika\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->remove('plainPassword')
            ->add('plainPassword', 'password' );
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'tafrika_user_registration';
    }
}