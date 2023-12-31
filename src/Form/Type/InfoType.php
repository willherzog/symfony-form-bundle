<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\{PropertyAccess,PropertyAccessorInterface,PropertyPath,PropertyPathInterface};

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class InfoType extends AbstractType
{
	protected PropertyAccessorInterface $propertyAccessor;

	public function __construct(PropertyAccessorInterface $propertyAccessor = null)
	{
		$this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'compound' => false,
				'required' => false,
				'disabled' => true,
				'immutable' => true,
				'value_label_has_html' => false,
				'use_block_level_value_label' => false,
				'value_label' => null,
				'help_choices' => null
			])
			->setAllowedTypes('value_label_has_html', 'bool')
			->setAllowedTypes('use_block_level_value_label', 'bool')
			->setAllowedTypes('value_label', ['null', 'callable', 'string', PropertyPath::class])
			->setAllowedTypes('help_choices', ['null', 'array'])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$labelSetter = $options['value_label'];
		$formData = $form->getData();

		if( is_callable($labelSetter) ) {
			$labelValue = $labelSetter($form);
		} elseif( is_array($formData) || is_object($formData) ) {
			if( is_string($labelSetter) ) {
				$labelSetter = new PropertyPath($labelSetter);
			}

			if( $labelSetter instanceof PropertyPathInterface ) {
				$labelValue = $this->propertyAccessor->getValue($formData, $labelSetter);
			}
		}

		if( !isset($labelValue) ) {
			$labelValue = (string) $formData;
		}

		$view->vars['value_label'] = $labelValue;
		$view->vars['value_label_has_html'] = $options['value_label_has_html'];

		if( $options['use_block_level_value_label'] ) {
			$view->vars['value_label_element'] = 'div';
		}

		if( !isset($view->vars['help']) && isset($options['help_choices']) && key_exists($formData, $options['help_choices']) ) {
			$view->vars['help'] = $options['help_choices'][$formData];
		}
	}
}
