<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Admin handler for Genre entity used in form construction and management.
 *
 * @see Genre
 */
class GenreAdmin extends AbstractAdmin
{
    /**
     * Construct the basic form fields for display.
     *
     * @param FormMapper $form
     *   The targeted form.
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('name');
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
