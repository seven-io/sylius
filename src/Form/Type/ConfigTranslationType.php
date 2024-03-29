<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigTranslationType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('from', TextType::class, [
                'attr' => ['placeholder' => 'seven.useDefault', 'required' => false],
            ])
            ->add('shippingText', TextareaType::class);
    }

    /** {@inheritdoc} */
    public function getBlockPrefix(): ?string {
        return 'seven_config_translation';
    }
}
