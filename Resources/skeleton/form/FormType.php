<?php

namespace {{ namespace }}\Form\Type\{{ document_namespace ? '\\' ~ document_namespace : '' }};

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use {{ namespace }}\Listener\Suscriber\PatchSubscriber;

class {{ form_class }} extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event_subscriber = new PatchSubscriber();
        $builder->addEventSubscriber($event_subscriber); 

        $builder
        {%- for field in fields %}

            ->add('{{ field.fieldName }}')

        {%- endfor %}

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ source_namespace }}\Document{{ document_namespace ? '\\' ~ document_namespace : '' }}\{{ document_class }}',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return '{{ form_type_name }}';
    }
}
