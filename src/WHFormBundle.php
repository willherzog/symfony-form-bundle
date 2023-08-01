<?php

namespace WHSymfony\WHFormBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use WHTwig\Twig\GenericExtension;

use WHSymfony\WHFormBundle\Form\Type\Extension\BaseTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CheckboxTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\ChoiceTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CollectionTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\EnumTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\FormTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\MoneyTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\NumberTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\InfoType;
use WHSymfony\WHFormBundle\Form\Type\ModalSelectType;
use WHSymfony\WHFormBundle\Form\Type\ModalImageSelectType;
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
			->set('whform.type_extension.base', BaseTypeExtension::class)
				->args([$config['form']['id_attributes_use_dashes']])
				->tag('form.type_extension', ['priority' => 99])
		;

		if( $config['form']['default_optional'] ) {
			$container->services()
				->set('whform.type_extension.form', FormTypesExtension::class)
					->tag('form.type_extension', ['priority' => 66])
			;
		}

		$container->services()
			->set('whform.type_extension.checkbox', CheckboxTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type_extension.choice', ChoiceTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type_extension.collection', CollectionTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type_extension.enum', EnumTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type_extension.money', MoneyTypeExtension::class)
				->tag('form.type_extension', ['priority' => 33])
			->set('whform.type_extension.number', NumberTypesExtension::class)
				->tag('form.type_extension', ['priority' => 33])

			->set('whform.type.info', InfoType::class)
				->args([service('form.property_accessor')->ignoreOnInvalid()])
				->tag('form.type')
			->set('whform.type.button_with_label', ModalSelectType::class)
				->args([service('form.property_accessor')->ignoreOnInvalid()])
				->tag('form.type')
			->set('whform.type.modal_image_select', ModalImageSelectType::class)
				->args([service('translator')])
				->tag('form.type')

			->set('whform.twig.extension', WHFormExtension::class)
				->args([$config['form']['default_indent'], $config['indent_spaces']])
				->tag('twig.extension')

			->set('whtwig.extension.generic', GenericExtension::class)
				->args([param('%kernel.project_dir%'), $config['indent_spaces']])
				->tag('twig.extension')
		;
	}
}
