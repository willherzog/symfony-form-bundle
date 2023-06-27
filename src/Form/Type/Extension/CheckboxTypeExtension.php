<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for the checkbox type to add the option to move the field label after the widget.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class CheckboxTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [CheckboxType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefault('label_after_widget', false);
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['label_after_widget'] = $options['label_after_widget'];
	}
}
