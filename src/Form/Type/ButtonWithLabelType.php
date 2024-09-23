<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\{PropertyPath,PropertyPathInterface};

/**
 * Abstract button type with a standard field label to fit more easily into generic form layouts.
 * Adds a new option, "button_text", which can be a string, callable, PropertyPath or NULL (the default).
 * If it is a callable, the function will be called with the field as its only argument.
 * If NULL, the field value will be casted to a string and used as the button's text.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class ButtonWithLabelType extends AbstractType implements TypeWithPropertyAccessorInterface
{
	use TypeWithPropertyAccessorTrait;

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'compound' => false,
				'button_text' => null
			])
			->setAllowedTypes('button_text', ['null', 'callable', 'string', PropertyPath::class])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$textSetter = $options['button_text'];
		$formData = $form->getData();

		if( is_callable($textSetter) ) {
			$optionsFiltered = $options;
			unset($optionsFiltered['button_text']);
			$buttonText = $textSetter($form, $optionsFiltered);
		} elseif( is_array($formData) || is_object($formData) ) {
			if( is_string($textSetter) ) {
				$textSetter = new PropertyPath($textSetter);
			}

			if( $textSetter instanceof PropertyPathInterface ) {
				$buttonText = $this->getPropertyAccessor()->getValue($formData, $textSetter);
			}
		}

		if( !isset($buttonText) ) {
			$buttonText = (string) $formData;
		}

		$view->vars['button_text'] = $buttonText;
	}
}
