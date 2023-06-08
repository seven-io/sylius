<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Form\Type;

use Seven\SyliusPlugin\Entity\AbstractMessage;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Customer\Model\CustomerGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractMessageType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        /* @var AbstractMessage $message */
        $message = $builder->getData();
        $cfg = $message->getConfig();

        if (null !== $message->getId()) $builder->add('response',
            TextareaType::class, ['attr' => ['readonly' => true],]);

        $builder
            ->add('config', ConfigType::class, ['label' => false])
            ->add('customerGroups', EntityType::class, [
                'class' => CustomerGroup::class,
                'multiple' => true,
            ])
            ->add('from',
                TextType::class, ['data' => null === $cfg ? '' : $cfg->getFrom()])
            ->add('msg', TextareaType::class);

        $builder->get('config')
            ->remove('apiKey')
            ->remove('enabled')
            ->remove('name')
            ->remove('onShipping')
            ->remove('translations');
    }
}
