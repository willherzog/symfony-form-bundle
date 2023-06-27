<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for the choice type to add support for placeholder translation parameters.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ChoiceTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [ChoiceType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'expanded_wrapping_strategy' => 'wrap_all',
				'placeholder_translation_parameters' => []
			])
			->setAllowedValues('expanded_wrapping_strategy', ['wrap_all','wrap_each','wrap_none'])
			->setAllowedTypes('placeholder_translation_parameters', 'array')
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['expanded_wrapping_strategy'] = $options['expanded_wrapping_strategy'];
		$view->vars['placeholder_translation_parameters'] = $options['placeholder_translation_parameters'];
	}
}
