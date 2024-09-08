<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for the native Symfony FormType to add the "label_tooltip_help" option.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class FormTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [FormType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->define('label_tooltip_help')
			->allowedTypes('string', 'null')
			->default(null)
			->info('Help text to use with the "title" attribute of the field\'s label element; if this is set, the label element will also have the HTML class "tooltip-help" added to it.')
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		if( $options['label_tooltip_help'] ) {
			$view->vars['label_attr']['title'] = $options['label_tooltip_help'];
			$htmlClass = 'tooltip-help';

			if(
				!isset($view->vars['label_attr']['class'])
				|| $view->vars['label_attr']['class'] === ''
			) {
				$view->vars['label_attr']['class'] = $htmlClass;
			} elseif(
				$view->vars['label_attr']['class'] !== $htmlClass
				&& is_string($view->vars['label_attr']['class'])
				&& !str_contains($view->vars['label_attr']['class'], $htmlClass)
			) {
				$view->vars['label_attr']['class'] .= ' '. $htmlClass;
			}
		}
	}
}
