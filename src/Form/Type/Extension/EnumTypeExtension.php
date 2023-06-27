<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\{Options,OptionsResolver};

use WHSymfony\WHFormBundle\Config\{LabelAwareEnum,LimitedChoicesEnum};

/**
 * Extension for EnumType to automatically handle interface-based enumerator variations.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class EnumTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [EnumType::class];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefault('choice_label', function(\UnitEnum $choice): string {
				if( $choice instanceof LabelAwareEnum ) {
					return $choice->getLabel();
				}

				return $choice->name;
			})
			->setDefault('choice_filter', function(Options $options): ?string {
				if( is_a($options['class'], LimitedChoicesEnum::class, true) ) {
					return 'isAllowedChoice';
				}

				return null;
			})
		;
	}
}
