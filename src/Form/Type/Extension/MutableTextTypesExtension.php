<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extension for native Symfony form types which are child types of {@link Type\TextType} yet do not support the HTML `readonly` attribute.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class MutableTextTypesExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [
			Type\ColorType::class,
			Type\RangeType::class
		];
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->setAttribute('supports_readonly', false);
	}
}
