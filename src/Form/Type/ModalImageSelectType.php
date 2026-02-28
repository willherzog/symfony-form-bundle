<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * An image-specific version of ModalSelectType.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalImageSelectType extends AbstractType
{
	public function getParent(): string
	{
		return ModalSelectType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$labelSetterOption = $resolver->isDefined('value_label') ? 'value_label' : 'button_text';
		$resolver->setDefault($labelSetterOption, function (FormInterface $form): ?string {
			$image = $form->getNormData();

			if( $image !== null && $image !== '' ) {
				if( is_object($image) && method_exists($image, 'getName') ) {
					return (string) $image->getName();
				}

				return (string) $image;
			}

			return null;
		});

		if( $resolver->isDefined('default_button_label') ) {
			$resolver->setDefault('default_button_label', 'wh_form.label.select_image');
		} else {
			$resolver->setDefault('button_label', 'wh_form.label.select_image_alt');
		}
	}
}
