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
        $builder->add('image', FileEntityType::class, [
            'image' => true,
        ]);

        $builder->add('caption', TextType::class, [
            'required' => false,
        ]);

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
