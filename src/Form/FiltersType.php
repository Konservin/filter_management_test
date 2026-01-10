<?php

namespace App\Form;

use App\Entity\Criteria;
use App\Entity\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Filter Name',
                'required' => true,
            ])
            ->add('criteria', CollectionType::class, [
                'entry_type' => CriteriaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'prototype' => true,
                'entry_options' => [
                    'label' => false
                ],
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (!$data) {
                return;
            }

            foreach ($data->getCriteria() as $criteria) {
                if (!$criteria->getType()) { // If no type is set, default to "Amount"
                    $criteria->setType(1); // Assuming "Amount" has value 1 in DB
                }
            }
        });

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();
                /* @var Criteria $criteria */
                if ($data->getCriteria()->count() === 0) {
                    $form->addError(new FormError("You must add at least one criteria before saving."));
                }
                foreach ($data->getCriteria() as $criteria) {
                    if (!$criteria->getFilter()) {
                        $criteria->setFilter($data);
                    }
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
        ]);
    }
}
