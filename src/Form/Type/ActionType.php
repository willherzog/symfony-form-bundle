<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type is not mapped to the underlying data object as it is only intended to allow an action button to be placed within the form.
 * Also, the "button_label" option is required and can only be a string, which will be translated if possible.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ActionType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'compound' => false,
				'mapped' => false,
				'required' => false
			])
			->define('button_label')
				->required()
				->allowedTypes('string')
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['button_label'] = $options['button_label'];
	}
}
