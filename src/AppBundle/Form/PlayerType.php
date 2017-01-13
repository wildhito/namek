<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class PlayerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('login', TextType::class)
            ->add('plainPassword', RepeatedType::class, array(
                 'type' => PasswordType::class,
                 'first_options' => array('label' => 'Password'),
                 'second_options' => array('label' => 'Repeat Password')
            ))
            ->add('recaptcha', EWZRecaptchaType::class,  array(
                'attr'        => array(
                    'options' => array(
                    'theme' => 'light',
                    'type'  => 'image',
                    'size'  => 'normal'
                    )
                ),
                'mapped'      => false,
                'constraints' => array(
                     new RecaptchaTrue()
                )
            ));
   }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Player'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_player';
    }


}
