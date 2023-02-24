<?php

namespace App\Form;

use App\Entity\Song;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class SongType extends AbstractType
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

            ->add('audioFileName', FileType::class, [
                'label'=> 'Song (MP3 file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '6000k',
                        'mimeTypes' => [
                            'audio/mpeg',

                        ],
                        'mimeTypesMessage' => 'Please upload a valid MP3 audio',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
