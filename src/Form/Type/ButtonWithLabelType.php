<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Abstract button type with a standard field label to fit more easily into generic form layouts.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class ButtonWithLabelType extends AbstractTypeWithDynamicLabel
{
	public function __construct(
		protected readonly bool $defaultOptional,
		protected readonly bool $useSeparateValueLabel
	) {}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'compound' => false,
			'required' => !$this->defaultOptional
		]);

		if( $this->useSeparateValueLabel ) {
			$resolver->define('button_label')
				->allowedTypes('string')
				->required()
			;

			$resolver->define('value_label')
				->allowedTypes('null', 'callable', 'string', PropertyPath::class)
				->default(null)
				->info('A callable (which will be called with the field as its only argument) or a property path to use for setting the text of the value label element. If NULL (or not set), the field data is casted to a string instead.')
			;

			$resolver->define('unset_value_label')
				->allowedTypes('string', 'null')
				->default(null)
				->info('The label to use when the field has no data (i.e. its value is NULL). This label will also be available in the rendered view at all times as an HTML data attribute on the value label element.')
			;
		} else {
			$resolver->define('button_text')
				->allowedTypes('null', 'callable', 'string', PropertyPath::class)
				->default(null)
				->info('A callable (which will be called with the field as its only argument) or a property path to use for setting the text of the action button. If NULL (or not set), the field data is casted to a string instead.')
			;

			$resolver->define('default_button_label')
				->allowedTypes('string', 'null')
				->default(null)
				->info('The button label to use when the field has no data (i.e. its value is NULL). This label will also be available in the rendered view at all times as an HTML data attribute on the button.')
			;
		}
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['use_separate_value_label'] = $this->useSeparateValueLabel;

		if( $this->useSeparateValueLabel ) {
			$view->vars['button_label'] = $options['button_label'];

			$options[parent::LABEL_SETTER_OPTION] = $options['value_label'];
			$options[parent::DEFAULT_LABEL_OPTION] = $options['unset_value_label'];

			unset($options['value_label'], $options['unset_value_label']);

			$view->vars['value_label'] = $this->getDynamicLabelValue($view, $form, $options);

			if( isset($view->vars['attr']['data-default-label']) ) {
				$view->vars['value_label_default'] = $view->vars['attr']['data-default-label'];

				unset($view->vars['attr']['data-default-label']);
			}
		} else {
			$options[parent::LABEL_SETTER_OPTION] = $options['button_text'];
			$options[parent::DEFAULT_LABEL_OPTION] = $options['default_button_label'];

			unset($options['button_text'], $options['default_button_label']);

			$view->vars['button_text'] = $this->getDynamicLabelValue($view, $form, $options);
		}
	}
}
