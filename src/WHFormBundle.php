<?php

namespace WHSymfony\WHFormBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use WHSymfony\WHFormBundle\Form\Type\Extension\BaseTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CheckboxTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\ChoiceTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CollectionTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\EnumTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\FormTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\MoneyTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\NumberTypesExtension;
use WHSymfony\WHFormBundle\Twig\WHFormExtension;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHFormBundle extends AbstractBundle
{
	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->extension('twig', [
			'form_themes' => ['@WHForm/wh_form_layout.html.twig']
		]);
	}

	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->services()

			->set('whform.type.extension.base', BaseTypeExtension::class)
				->tag('form.type_extension', ['priority' => 99])
			->set('whform.type.extension.form', FormTypesExtension::class)
				->tag('form.type_extension', ['priority' => 66])

			->set('whform.type.extension.checkbox', CheckboxTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type.extension.choice', ChoiceTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type.extension.collection', CollectionTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type.extension.enum', EnumTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type.extension.money', MoneyTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type.extension.number', NumberTypesExtension::class)
				->tag('form.type_extension', ['priority' => 33])

			->set('whform.twig.extension', WHFormExtension::class)
				->tag('twig.extension')
		;
	}
}
