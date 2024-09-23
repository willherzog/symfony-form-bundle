<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

use WHSymfony\WHFormBundle\Config\LabelAwareEnum;

/**
 * Enumerator-specific version of ModalSelectType with a suitable automated default for the "button_text" option.
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
		$resolver
			->setDefault('button_text', function (FormInterface $form, array $options): string {
				$currentValue = $form->getData();

				if( !empty($currentValue) && is_a($options['class'], \BackedEnum::class, true) && is_a($options['class'], LabelAwareEnum::class, true) ) {
					return $options['class']::tryFrom($currentValue)?->getLabel() ?? $currentValue;
				}

				return $options['default_button_label'];
			})
		;

		$resolver
			->define('default_button_label')
				->default('wh_form.label.click_to_change')
				->allowedTypes('string')
		;

		$resolver
			->define('class')
				->required()
				->allowedTypes('string')
				->allowedValues(enum_exists(...))
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['translate_button_text'] = true;
		$view->vars['attr']['data-default-label'] = $this->getTranslator()->trans($options['default_button_label']);
	}
}
