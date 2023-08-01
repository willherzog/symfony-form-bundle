<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Extension for the native Symfony ButtonType to add the "help", "help_attr", "help_html" and "help_translation_parameters" options.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ButtonTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [ButtonType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->define('help')
			->allowedTypes('string', 'null', TranslatableInterface::class)
			->default(null)
		;

		$resolver
			->define('help_attr')
			->allowedTypes('array')
			->default([])
		;

		$resolver
			->define('help_html')
			->allowedTypes('bool')
			->default(false)
		;

		$resolver
			->define('help_translation_parameters')
			->default([])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$helpTranslationParameters = $options['help_translation_parameters'];

		if( $view->parent ) {
			$helpTranslationParameters = array_merge($view->parent->vars['help_translation_parameters'], $helpTranslationParameters);
		}

		$view->vars = array_replace($view->vars, [
			'help' => $options['help'],
			'help_attr' => $options['help_attr'],
			'help_html' => $options['help_html'],
			'help_translation_parameters' => $helpTranslationParameters
		]);
	}
}
