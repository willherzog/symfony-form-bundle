<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\PropertyAccess\{PropertyPath,PropertyPathInterface};

/**
 * @internal
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class AbstractTypeWithDynamicLabel extends AbstractType implements TypeWithPropertyAccessorInterface, TypeWithTranslatorInterface
{
	use TypeWithPropertyAccessorTrait, TypeWithTranslatorTrait;

	public const LABEL_SETTER_OPTION = 'dynamic_label_setter';
	public const DEFAULT_LABEL_OPTION = 'dynamic_label_default';

	protected function getDynamicLabelValue(FormView $view, FormInterface $form, array $options, bool $setDefaultLabelDataAttr = true): string
	{
		$defaultLabel = $options[self::DEFAULT_LABEL_OPTION] ?? '';

		if( $defaultLabel !== '' ) {
			if( $options['translation_domain'] !== false ) {
				$defaultLabel = $this->translator->trans($defaultLabel, domain: $options['translation_domain']);
			}

			if( $setDefaultLabelDataAttr ) {
				$view->vars['attr']['data-default-label'] = $defaultLabel;
			}
		}

		$labelSetter = $options[self::LABEL_SETTER_OPTION];
		$formData = $form->getData();

		if( is_callable($labelSetter) ) {
			$labelValue = $labelSetter($form);

			if( $labelValue === null && $defaultLabel !== '' ) {
				$labelValue = $defaultLabel;
			}
		} elseif( is_array($formData) || is_object($formData) ) {
			if( is_string($labelSetter) ) {
				$labelSetter = new PropertyPath($labelSetter);
			}

			if( $labelSetter instanceof PropertyPathInterface ) {
				$labelValue = $this->getPropertyAccessor()->getValue($formData, $labelSetter);
			}
		}

		if( !isset($labelValue) ) {
			if( $defaultLabel !== '' && empty($formData) ) {
				$labelValue = $defaultLabel;
			}

			$labelValue = $formData;
		}

		return (string) $labelValue;
	}
}
