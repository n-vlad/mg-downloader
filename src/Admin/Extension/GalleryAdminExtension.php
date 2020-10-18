<?php

namespace App\Admin\Extension;

use App\Form\Type\JsonViewingWindowType;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollection;

/**
 * Gallery Admin extension used to include all additional custom fields.
 *
 * @see Gallery
 */
class GalleryAdminExtension extends AbstractAdminExtension
{
    /**
     * Construct the basic form fields for display.
     *
     * @param FormMapper $formMapper
     *   The targeted form.
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Options')
            ->add('year')
            ->add('quote')
            ->end();

        $formMapper
            ->with('Description')
            ->add('body', TextareaType::class, ['attr' => ['rows' => 10]])
            ->add('synopsis', TextareaType::class, ['attr' => ['rows' => 3]])
            ->add('reviewAuthor')
            ->add('rating')
            ->add('url')
            ->end();

        $formMapper
            ->with('Extra Information')
            ->add('rid')
            ->add('sum')
            ->add('lastUpdated')
            ->add('classType')
            ->add('duration')
            ->add('cert')
            ->add('genre', CollectionType::class,
                [
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->add('cast', CollectionType::class,
                [
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->add('directors', CollectionType::class,
                [
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

        $formMapper
            ->with('SkyGo')
            ->add('skyGoId')
            ->add('skyGoUrl')
            ->end();

        $formMapper
            ->with('Videos')
            ->add('videos', CollectionType::class,
                [
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

        $formMapper
            ->with('Viewing Window')
            ->add('viewingWindow', SymfonyCollection::class,
                [
                    'entry_type' => JsonViewingWindowType::class,
                    'label' => ' ',
                    'required' => false
                ])
            ->end();
    }
}
