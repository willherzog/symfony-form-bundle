<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\{MoneyType,NumberType,PercentType};

/**
 * Extension for the native Symfony non-compound number types to always add the HTML class "faux-number-widget" when their "html5" option is FALSE.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class NumberTypesExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [NumberType::class,MoneyType::class,PercentType::class];
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		if( !$options['html5'] ) {
			if( isset($view->vars['attr']['class']) ) {
				$view->vars['attr']['class'] .= ' faux-number-widget';
			} else {
				$view->vars['attr']['class'] = 'faux-number-widget';
			}
		}
	}
}
