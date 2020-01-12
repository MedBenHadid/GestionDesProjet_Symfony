<?php

namespace BalProjetBundle\Form;

use BalProjetBundle\Entity\Projet;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom') ->add('nom', TextType::class, ['required'=> false])

            ->add('Projet',EntityType::class,['class'=>Projet::class,'query_builder'=>function(EntityRepository $em){
                return $em->createQueryBuilder('u')->orderBy('u.libelle');
            },'choice_label'=>'libelle'])

            ->add('Classe', ChoiceType::class, [
                'choices'  => [
                    '3A24' => "3A24",
                    '3A25' => "3A25",
                    '3A26' => "3a27",
                ],
            ])
            ->add('send', SubmitType::class)
            ->getForm();

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BalProjetBundle\Entity\Equipe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balprojetbundle_equipe';
    }


}
