<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for various native Symfony form types to override the default for the "required" option.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class FormTypesExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		// The types have to be listed individually (as opposed to simply using Formtype) so that overriding *certain* defaults works as expected.
		return [
			Type\CheckboxType::class,
			Type\ChoiceType::class,
			Type\DateType::class,
			Type\EmailType::class,
			Type\FileType::class,
			Type\IntegerType::class,
			Type\MoneyType::class,
			Type\NumberType::class,
			Type\PasswordType::class,
			Type\PercentType::class,
			Type\TelType::class,
			Type\TextType::class,
			Type\TextareaType::class,
			Type\UrlType::class
		];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		// Make form fields optional by default; note that this overrides child forms if set on a parent form.
		$resolver->setDefault('required', false);
	}
}
