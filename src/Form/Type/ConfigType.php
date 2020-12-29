<?php
declare(strict_types=1);

namespace Sms77\SyliusPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('apiKey', TextType::class)
            ->add('debug', CheckboxType::class)
            ->add('flash', CheckboxType::class)
            ->add('noReload', CheckboxType::class)
            ->add('performanceTracking', CheckboxType::class)
            ->add('unicode', CheckboxType::class)
            ->add('utf8', CheckboxType::class)
            ->add('enabled', CheckboxType::class)
            ->add('label', TextType::class)
            ->add('onShipping', CheckboxType::class)
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ConfigTranslationType::class,
            ]);
    }

    /** {@inheritdoc} */
    public function getBlockPrefix() {
        return 'sms77_config';
    }
}