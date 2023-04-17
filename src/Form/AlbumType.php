<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Rap' => 'rap',
                    'Pop' => 'pop',
                    'Metal' => 'metal',
                    'Rock' => 'rock',
                    'Classical Music' => 'classical music',
                    'Jazz' => 'jazz',
                ]])
            ->add('artist')
            ->add('pictureFileName',FileType::class,[
                'label' => 'Image',
                'mapped'=> false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2024k',
                        'mimeTypes' => [
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ])
            ->add('songs',FileType::class,[
                'label'=> 'Song',
                'multiple'=> true,
                'mapped'=> false,
                'required'=> true,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '6000k',
                            'mimeTypes' => [
                                'audio/mpeg',

                            ],
                            'mimeTypesMessage' => 'Please upload a valid MP3 audio',
                        ])
                    ])
                ],

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
