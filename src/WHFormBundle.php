<?php

namespace WHSymfony\WHFormBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use WHTwig\Twig\GenericExtension;

use WHSymfony\WHFormBundle\Form\Type\Extension\BaseTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\ButtonTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CheckboxTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\ChoiceTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\CollectionTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\EnumTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\FormTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\ImmutableTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\MakeTypesOptionalExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\MoneyTypeExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\MutableTextTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\Extension\NumberTypesExtension;
use WHSymfony\WHFormBundle\Form\Type\InfoType;
use WHSymfony\WHFormBundle\Form\Type\ModalSelectType;
use WHSymfony\WHFormBundle\Form\Type\ModalEnumSelectType;
use WHSymfony\WHFormBundle\Form\Type\ModalImageSelectType;
use WHSymfony\WHFormBundle\Twig\WHFormExtension;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHFormBundle extends AbstractBundle
{
	protected string $extensionAlias = 'wh_form';

	public function configure(DefinitionConfigurator $definition): void
	{
		$definition->rootNode()
			->children()
				->integerNode('indent_spaces')
					->defaultValue(0)
					->info('Amount of spaces to use for each level of indentation (positive integer = that number of spaces is used instead of a tab character).')
				->end()
				->arrayNode('form')
					->addDefaultsIfNotSet()
					->children()
						->integerNode('default_indent')
							->defaultValue(2)
							->info('Indentation level to use by default for root forms. Note the indentation levels of child forms are automatically incremented based on the indentation level of their parent form(s).')
						->end()
						->booleanNode('default_optional')
							->defaultTrue()
							->info('Whether to make the "required" option default to FALSE.')
						->end()
						->booleanNode('id_attributes_use_dashes')
							->defaultTrue()
							->info('Whether to convert underscores to dashes for form widget "id" attributes (this will also prevent having a leading dash character).')
						->end()
					->end()
				->end()
			->end()
		;
	}

	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->extension('twig', [
			'form_themes' => ['@WHForm/wh_form_layout.html.twig']
		]);
	}

	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->services()
			->set('whform.type_extension.button', ButtonTypeExtension::class)
				->tag('form.type_extension', ['priority' => 99])
			->set('whform.type_extension.base', BaseTypeExtension::class)
				->args([$config['form']['id_attributes_use_dashes']])
				->tag('form.type_extension', ['priority' => 97])
			->set('whform.type_extension.form', FormTypeExtension::class)
				->tag('form.type_extension', ['priority' => 95])
			->set('whform.type_extension.immutable', ImmutableTypesExtension::class)
				->tag('form.type_extension', ['priority' => 93])
			->set('whform.type_extension.mutable', MutableTextTypesExtension::class)
				->tag('form.type_extension', ['priority' => 77])
		;

		if( $config['form']['default_optional'] ) {
			$container->services()
				->set('whform.type_extension.make_types_optional', MakeTypesOptionalExtension::class)
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
				->call('setPropertyAccessor', [service('form.property_accessor')->ignoreOnInvalid()])
				->tag('form.type')
			->set('whform.type.button_with_label', ModalSelectType::class)
				->call('setPropertyAccessor', [service('form.property_accessor')->ignoreOnInvalid()])
				->call('setTranslator', [service('translator')])
				->tag('form.type')
			->set('whform.type.modal_image_select', ModalImageSelectType::class)
				->tag('form.type')
			->set('whform.type.modal_enum_select', ModalEnumSelectType::class)
				->call('setTranslator', [service('translator')])
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
