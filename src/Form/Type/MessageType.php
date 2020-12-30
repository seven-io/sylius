<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Form\Type;

use Sms77\SyliusPlugin\Entity\Message;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Customer\Model\CustomerGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageType extends AbstractResourceType {
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        /* @var Message $message */
        $message = $builder->getData();

        if (null !== $message->getId()) {
            $builder->add('response',
                TextareaType::class, ['attr' => ['readonly' => true],]);
        }

        $builder
            ->add('config', ConfigType::class, ['label' => false])
            ->add('customerGroups', EntityType::class, [
                'class' => CustomerGroup::class,
                'multiple' => true,
            ])
            ->add('from',
                TextType::class, ['data' => $message->getConfig()->getFrom()])
            ->add('msg', TextareaType::class);

        $builder->get('config')
            ->remove('apiKey')
            ->remove('translations')
            ->remove('onShipping')
            ->remove('name')
            ->remove('enabled');
    }

    /** {@inheritdoc} */
    public function getBlockPrefix() {
        return 'sms77_message';
    }
}