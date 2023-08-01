<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

use WHSymfony\WHFormBundle\Config\LabelAwareEnum;

/**
 * Enumerator-specific version of ModalSelectType with a suitable default for the "button_text" option.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalEnumSelectType extends AbstractType
{
	protected readonly string $buttonDefaultLabel;

	public function __construct(TranslatorInterface $translator)
	{
		$this->buttonDefaultLabel = $translator->trans('wh_form.label.click_to_change');
	}

	public function getParent(): string
	{
		return ModalSelectType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefault('button_text', function (FormInterface $form, array $options): string {
				$currentValue = $form->getData();

				if( !empty($currentValue) && is_a($options['class'], LabelAwareEnum::class, true) ) {
					return $options['class']::tryFrom($currentValue)?->getLabel() ?? $currentValue;
				}

				return $this->buttonDefaultLabel;
			})
			->define('class')
				->required()
				->allowedTypes('string')
				->allowedValues(enum_exists(...))
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['attr']['data-default-label'] = $this->buttonDefaultLabel;
	}
}
