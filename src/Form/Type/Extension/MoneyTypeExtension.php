<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

/**
 * Extension for the money type to directly set a data attribute for the resultant currency symbol based on the original "money_pattern" one.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class MoneyTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [MoneyType::class];
	}

	public function finishView(FormView $view, FormInterface $form, array $options): void
	{
		$pattern = $view->vars['money_pattern'];

		$view->vars['symbol'] = false;
		$view->vars['symbol_placement'] = str_ends_with($pattern, '}}') ? 'before' : 'after';

		if( str_contains($pattern, '{{ widget }}') ) {
			$pattern = trim(str_replace('{{ widget }}', '', $pattern));

			if( strlen($pattern) > 0 ) {
				$view->vars['symbol'] = $pattern;
			}
		}
	}
}
