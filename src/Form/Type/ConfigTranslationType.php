<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigTranslationType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('sender', TextType::class, [
                'attr' => ['placeholder' => 'sms77_api.useDefault'],
            ])
            ->add('shippingText', TextareaType::class);
    }

    /** {@inheritdoc} */
    public function getBlockPrefix() {
        return 'sms77_config_translation';
    }
}