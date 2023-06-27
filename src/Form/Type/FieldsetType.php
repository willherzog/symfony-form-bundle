<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\{AbstractType,FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class FieldsetType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'inherit_data' => true,
				'collapsible' => false,
				'long_field_labels' => false,
				'help_text_position' => 'before'
			])
			->setAllowedValues('help_text_position', ['before','after'])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		if( isset($view->vars['row_attr']) ) {
			if( !isset($view->vars['row_attr']['id']) && isset($view->vars['class_name']) ) {
				$view->vars['row_attr']['id'] = $view->vars['class_name'];
			}

			if( $options['collapsible'] ) {
				if( isset($view->vars['row_attr']['class']) ) {
					$view->vars['row_attr']['class'] .= ' collapsible';
				} else {
					$view->vars['row_attr']['class'] = 'collapsible';
				}
			}

			if( $options['long_field_labels'] ) {
				if( isset($view->vars['row_attr']['class']) ) {
					$view->vars['row_attr']['class'] .= ' longer-labels';
				} else {
					$view->vars['row_attr']['class'] = 'longer-labels';
				}
			}
		} else {
			$name = $form->getName() ?: '';

			// Replace any underscore characters with dashes.
			if( str_contains($name, '_') ) {
				$name = str_replace('_', '-', $name);
			}

			$attr = ['id' => $name];

			if( $options['collapsible'] ) {
				$attr['class'] = 'collapsible';

				if( $options['long_field_labels'] ) {
					$attr['class'] .= ' longer-labels';
				}
			} elseif( $options['long_field_labels'] ) {
				$attr['class'] = 'longer-labels';
			}

			if( isset($options['row_attr']) ) {
				$view->vars['row_attr'] = array_merge_recursive($options['row_attr'], $attr);
			} else {
				$view->vars['row_attr'] = $attr;
			}
		}

		$view->vars['help_text_position'] = $options['help_text_position'];
	}
}
