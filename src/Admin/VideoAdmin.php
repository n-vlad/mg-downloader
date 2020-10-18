<?php

namespace App\Admin;

use App\Form\Type\JsonVideoType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Admin handler for Video entity used in form construction and management.
 *
 * @see Video
 */
class VideoAdmin extends AbstractAdmin
{
    /**
     * Construct the basic form fields for display.
     *
     * @param FormMapper $form
     *   The targeted form.
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name')
            ->add('type')
            ->add('url')
            ->add('alternatives', CollectionType::class, ['entry_type' => JsonVideoType::class]);
    }

    /**
     * Manage all routes within the current admin.
     *
     * @param RouteCollection $collection
     *   The set of collection routes.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }
}
