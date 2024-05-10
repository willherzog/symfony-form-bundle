<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for the collection type to add support for including an "Add Item" button in the form template (to be activated via client-side scripting).
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class CollectionTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [CollectionType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'add_button_class' => 'add-item',
				'add_button_label' => 'wh_form.label.add_item',
				'add_button_opens_dialog' => false,
				'add_button_position' => null,
				'help_text_position' => null
			])
			->setAllowedTypes('add_button_class', 'string')
			->setAllowedTypes('add_button_label', 'string')
			->setAllowedValues('add_button_position', [null,'before','after','inside'])
			->setAllowedValues('help_text_position', [null,'before','after'])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['add_button_class'] = $options['add_button_class'];
		$view->vars['add_button_label'] = $options['add_button_label'];
		$view->vars['add_button_opens_dialog'] = $options['add_button_opens_dialog'];
		$view->vars['add_button_position'] = $options['add_button_position'];

		if( in_array($options['help_text_position'], ['before','after'], true) ) {
			$view->vars['help_text_position'] = $options['help_text_position'];
		} else {
			// Default behavior: Automate help text position based on whether the label is displayed
			$view->vars['help_text_position'] = $options['label'] === false ? 'after' : 'before';
		}
	}
}
