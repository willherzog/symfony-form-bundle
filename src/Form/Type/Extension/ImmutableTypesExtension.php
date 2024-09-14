<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extension for native Symfony form types that always render as an HTML tag for which the 'immutable' attribute is supported.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ImmutableTypesExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [
			Type\IntegerType::class,
			Type\MoneyType::class,
			Type\NumberType::class,
			Type\PercentType::class,
			Type\TextType::class
		];
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->setAttribute('supports_readonly', true);
	}
}
