<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\{PropertyPath,PropertyPathInterface};

/**
 * Abstract button type with a standard field label to fit more easily into generic form layouts.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class ButtonWithLabelType extends AbstractType implements TypeWithPropertyAccessorInterface, TypeWithTranslatorInterface
{
	use TypeWithPropertyAccessorTrait, TypeWithTranslatorTrait;

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefault('compound', false);

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

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$defaultLabel = $options['default_button_label'] ?? '';

		if( $defaultLabel !== '' ) {
			if( $options['translation_domain'] !== false ) {
				$defaultLabel = $this->translator->trans($defaultLabel, domain: $options['translation_domain']);
			}

			$view->vars['attr']['data-default-label'] = $defaultLabel;
		}

		$textSetter = $options['button_text'];
		$formData = $form->getData();

		if( is_callable($textSetter) ) {
			$buttonText = $textSetter($form);

			if( $buttonText === null && $defaultLabel !== '' ) {
				$buttonText = $defaultLabel;
			}
		} elseif( is_array($formData) || is_object($formData) ) {
			if( is_string($textSetter) ) {
				$textSetter = new PropertyPath($textSetter);
			}

			if( $textSetter instanceof PropertyPathInterface ) {
				$buttonText = $this->getPropertyAccessor()->getValue($formData, $textSetter);
			}
		}

		if( !isset($buttonText) ) {
			if( $defaultLabel !== '' && empty($formData) ) {
				$buttonText = $defaultLabel;
			}

			$buttonText = $formData;
		}

		$view->vars['button_text'] = (string) $buttonText;
	}
}
