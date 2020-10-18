<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Custom form type to handle display of expected keyed Viewing Window JSON data.
 */
class JsonViewingWindowType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('wayToWatch')
            ->add('startDate')
            ->add('endDate');
    }
}
