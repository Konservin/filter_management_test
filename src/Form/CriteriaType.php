<?php

namespace App\Form;

use App\Entity\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\FilterTypes;
use App\Entity\FilterSubtypes;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => FilterTypes::class,
                'choice_label' => 'name',
                'label' => false,
                'placeholder' => 'Select a type',
                'required' => true,
                'attr' => ['class' => 'js-type-select'], // JavaScript identifier
            ])
            ->add('subtype', EntityType::class, [
                'class' => FilterSubtypes::class,
                'choice_label' => 'name',
                'label' => false,
                'placeholder' => 'Select a subtype',
                'required' => true,
                'attr' => ['class' => 'js-subtype-select'], // JavaScript identifier
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                }
            ])
            ->add('value', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => ['class' => 'js-value-input'], // JavaScript will modify this field
            ]);
        
        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $type = $event->getForm()->getData();

                $this->updateSubtypeChoices($form->getParent(), $type);
            }
        );
    }

    private function updateSubtypeChoices(FormInterface $form, ?FilterTypes $type): void
    {
        $form->add('subtype', EntityType::class, [
            'class' => FilterSubtypes::class,
            'choice_label' => 'name',
            'label' => 'Filter Subtype',
            'placeholder' => 'Select a subtype',
            'required' => true,
            'attr' => ['class' => 'js-subtype-select'], // JavaScript identifier
            'query_builder' => function (EntityRepository $er) use ($type) {
                $qb = $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                if ($type) {
                    $qb->where('s.type = :type')->setParameter('type', $type);
                }
                return $qb;
            }
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Criteria::class,
        ]);
    }
}
