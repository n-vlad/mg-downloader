<?php

namespace App\Admin\Extension;

use App\Form\Type\JsonViewingWindowType;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->end();

        $formMapper
            ->with('Description', ['class' => 'col-md-9'])
            ->add('body', TextareaType::class, ['label' => 'Body', 'attr' => ['rows' => 10]])
            ->add('synopsis', TextareaType::class, ['label' => 'Synopsis', 'attr' => ['rows' => 3]])
            ->add('quote', TextType::class, ['label' => 'Quote'])
            ->add('reviewAuthor', TextType::class, ['label' => 'Review Author'])
            ->add('rating', TextType::class, ['label' => 'Rating'])
            ->add('url', TextType::class, ['label' => 'Url'])
            ->end();

        $formMapper
            ->with('Extra Information', ['class' => 'col-md-3'])
            ->add('rid', TextType::class, ['label' => 'Response ID'])
            ->add('sum', TextType::class, ['label' => 'Sum'])
            ->add('lastUpdated', DateType::class, ['label' => 'Last Updated On'])
            ->add('classType', TextType::class, ['label' => 'Class'])
            ->add('year', IntegerType::class, ['label' => 'Year'])
            ->add('duration', IntegerType::class, ['label' => 'Duration'])
            ->add('cert', TextType::class, ['label' => 'Cert'])
            ->add('skyGoId', TextType::class, ['label' => 'SkyGo ID'])
            ->add('skyGoUrl', TextType::class, ['label' => 'SkyGo Url'])
            ->end();

        $formMapper
            ->with('Genre', ['class' => 'col-md-3'])
            ->add('genre', CollectionType::class,
                [
                    'label' => ' ',
                    'required' => false,
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

        $formMapper
            ->with('Cast', ['class' => 'col-md-3'])
            ->add('cast', CollectionType::class,
                [
                    'label' => ' ',
                    'required' => false,
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

        $formMapper
            ->with('Directors', ['class' => 'col-md-3'])
            ->add('directors', CollectionType::class,
                [
                    'label' => ' ',
                    'required' => false,
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

        $formMapper
            ->with('Viewing Window', ['class' => 'col-md-3'])
            ->add('viewingWindow', SymfonyCollection::class,
                [
                    'entry_type' => JsonViewingWindowType::class,
                    'label' => ' ',
                    'required' => false
                ])
            ->end();

        $formMapper
            ->with('Videos')
            ->add('videos', CollectionType::class,
                [
                    'label' => ' ',
                    'required' => false,
                    'type_options' => ['delete' => false, 'required' => false],
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end();

    }
}
