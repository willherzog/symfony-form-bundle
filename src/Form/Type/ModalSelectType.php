<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This button type is rendered with a hidden input to hold the current value.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalSelectType extends ButtonWithLabelType
{
	public function configureOptions(OptionsResolver $resolver): void
	{
		parent::configureOptions($resolver);

		if( $this->useSeparateValueLabel ) {
			$resolver->setDefault('button_label', 'wh_form.label.select_value_alt');
			$resolver->setDefault('unset_value_label', 'wh_form.value.not_set');
		} else {
			$resolver->setDefault('default_button_label', 'wh_form.label.select_value');

			$resolver->define('button_tooltip')
				->allowedTypes('null', 'string')
				->default('wh_form.label.click_to_change')
			;
		}
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		parent::buildView($view, $form, $options);

		$view->vars['attr']['aria-haspopup'] = 'dialog';

		if( !isset($view->vars['attr']['class']) ) {
			$view->vars['attr']['class'] = 'select-item';
		}

		if(
			!$this->useSeparateValueLabel
			&& !$form->isDisabled()
			&& $options['button_tooltip'] !== null
			&& $options['button_tooltip'] !== ''
		) {
			$view->vars['attr']['title'] = $options['button_tooltip'];
		}
	}
}
