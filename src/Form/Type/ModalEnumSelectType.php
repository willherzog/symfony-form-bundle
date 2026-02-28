<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormBuilderInterface,FormInterface};
use Symfony\Component\OptionsResolver\OptionsResolver;

use WHSymfony\WHFormBundle\Config\LabelAwareEnum;

/**
 * An enumerator-specific version of ModalSelectType.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalEnumSelectType extends AbstractType implements TypeWithTranslatorInterface
{
	use TypeWithTranslatorTrait;

	public function getParent(): string
	{
		return ModalSelectType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$labelSetterOption = $resolver->isDefined('value_label') ? 'value_label' : 'button_text';
		$resolver->setDefault($labelSetterOption, function (FormInterface $form): ?string {
			$currentValue = $form->getNormData();

			if( !empty($currentValue) ) {
				$enumClass = $form->getConfig()->getAttribute('_enum_class');

				if( is_subclass_of($enumClass, \BackedEnum::class) ) {
					$enumValue = $enumClass::tryFrom($currentValue);

					if( $enumValue === null ) {
						return (string) $currentValue;
					} elseif( is_subclass_of($enumClass, LabelAwareEnum::class) ) {
						$translationDomain = $form->getConfig()->getAttribute('_translation_domain');

						if( $translationDomain !== false ) {
							return $this->translator->trans($enumValue->getLabel(), domain: $translationDomain);
						} else {
							return $enumValue->getLabel();
						}
					} else {
						return (string) $enumValue;
					}
				}
			}

			return null;
		});

		$resolver->define('class')
			->required()
			->allowedTypes('string')
			->allowedValues(enum_exists(...))
		;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		// Make these options available directly from the form config
		$builder->setAttribute('_enum_class', $options['class']);
		$builder->setAttribute('_translation_domain', $options['translation_domain']);
	}
}
