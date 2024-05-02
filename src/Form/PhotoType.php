<?php

namespace OHMedia\PhotoBundle\Form;

use OHMedia\FileBundle\Form\Type\FileEntityType;
use OHMedia\PhotoBundle\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $photo = $options['data'];

        $builder->add('image', FileEntityType::class, [
            'image' => true,
            'data' => $photo->getImage(),
        ]);

        $builder->add('caption');

        $builder->add('credit', TextType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
