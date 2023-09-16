<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'data' => $options['user_fullname'] ?: '',
                'attr' => [
                    'class' => 'form-control ',
                    'minlength' => 2,
                    'maxlength' => 50
                ],
                'label' => 'Nom Prénom',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min'=> 2, 'max'=>50])
                ],
                'required' => false
            ])
            ->add('email', EmailType::class,  [
                'data' => $options['user_email'] ?: '',
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 180
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Email,
                    new Assert\Length(['min'=> 2, 'max'=>180])
                ],
                'required' => true
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control ',
                    'minlength' => 2,
                    'maxlength' => 150
                ],
                'label' => 'Sujet',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min'=> 2, 'max'=>150])
                ],
                'required' => false
            ])
            ->add('message',TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Message',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                    'title' =>'Envoyer'
                ],
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'user_fullname' => null,
            'user_email' => null
        ]);
    }
}
