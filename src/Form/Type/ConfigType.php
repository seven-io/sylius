<?php
declare(strict_types=1);

namespace Seven\SyliusPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('apiKey', TextType::class)
            ->add('flash', CheckboxType::class, ['required' => false])
            ->add('performanceTracking', CheckboxType::class, ['required' => false])
            ->add('unicode', CheckboxType::class, ['required' => false])
            ->add('label', TextType::class,
                ['required' => false, 'attr' => ['maxlength' => 100]])
            ->add('foreignId', TextType::class,
                ['required' => false, 'attr' => ['maxlength' => 64]])
            ->add('delay', TextType::class, ['required' => false])
            ->add('udh', TextType::class, ['required' => false])
            ->add('ttl', IntegerType::class,
                ['required' => false, 'attr' => ['min' => 1]])
            ->add('enabled', CheckboxType::class)
            ->add('name', TextType::class)
            ->add('onShipping', CheckboxType::class)
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ConfigTranslationType::class,
            ]);
    }

    /** {@inheritdoc} */
    public function getBlockPrefix(): ?string {
        return 'seven_config';
    }
}
