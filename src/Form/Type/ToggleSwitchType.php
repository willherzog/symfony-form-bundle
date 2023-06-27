<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * A "toggle switch" visual mod for single-checkbox fields: backend functionality is identical.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ToggleSwitchType extends AbstractType
{
	public function getParent(): string
	{
		return CheckboxType::class;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$htmlClass = 'hidden-widget';

		if( !isset($view->vars['attr']['class']) ) {
			$view->vars['attr']['class'] = $htmlClass;
		} elseif( $view->vars['attr']['class'] !== $htmlClass && is_string($view->vars['attr']['class']) && !str_contains($view->vars['attr']['class'], $htmlClass) ) {
			$view->vars['attr']['class'] .= ' '. $htmlClass;
		}
	}
}
