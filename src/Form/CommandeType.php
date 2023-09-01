<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('quantite')
            // ->add('montant')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En cours de traitement' => 'En cours de traitement',
                    'Commande validée' => 'Commande validée',
                    'Commande envoyée' => 'Commande envoyée',
                ]
            ])
            // ->add('date_enregistrement')
            // ->add('membre')
            // ->add('produit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
