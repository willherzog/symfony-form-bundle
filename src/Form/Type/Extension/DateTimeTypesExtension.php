<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extension for native Symfony form types that can optionally render as an HTML tag for which the 'immutable' attribute is supported.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class DateTimeTypesExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [
			Type\DateIntervalType::class,
			Type\DateTimeType::class,
			Type\DateType::class,
			Type\TimeType::class,
			Type\WeekType::class
		];
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$usingTextInputsOnly = true;

		foreach( ['date_widget' => false, 'time_widget' => false, 'widget' => true] as $option => $required ) {
			if( $required && !key_exists($option, $options) ) {
				$usingTextInputsOnly = false;
				break;
			} elseif( key_exists($option, $options) && !in_array($options[$option], ['single_text','text'], true) ) {
				$usingTextInputsOnly = false;
				break;
			}
		}

		if( $usingTextInputsOnly ) {
			$builder->setAttribute('supports_readonly', true);
		}
	}
}
