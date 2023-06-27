<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This version includes a "settings" action button in its widget template block.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ToggleSwitchWithSettingsType extends AbstractType
{
	public function getParent(): string
	{
		return ToggleSwitchType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'button_label' => 'wh_form.label.settings', // Label for action button; defaults to 'Settings'
				'button_attr' => [], // Additional attributes for action button; defaults to 'class="action open-settings"'
				'use_parent_row_type' => true
			])
			->setAllowedTypes('button_label', 'string')
			->setAllowedTypes('button_attr', 'array')
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['button_label'] = $options['button_label'];
		$view->vars['button_attr'] = $options['button_attr'];

		// Prepend HTML class attribute with "action open-settings" (used for styling/scripting); try to merge with existing value if there is one
		if( !isset($view->vars['button_attr']['class']) ) {
			$view->vars['button_attr']['class'] = 'action open-settings';
		} elseif( $view->vars['button_attr']['class'] !== 'action open-settings' && is_string($view->vars['button_attr']['class']) ) {
			if( !str_contains($view->vars['button_attr']['class'], 'action') ) {
				if( !str_contains($view->vars['button_attr']['class'], 'open-settings') ) {
					$view->vars['button_attr']['class'] = 'action open-settings ' . ltrim($view->vars['button_attr']['class']);
				} else {
					$view->vars['button_attr']['class'] = 'action ' . ltrim($view->vars['button_attr']['class']);
				}
			}
		}
	}
}
