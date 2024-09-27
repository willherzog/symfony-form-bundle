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
		$resolver
			->setDefault('button_text', function (FormInterface $form): string {
				$image = $form->getData();

				if( $image !== null && $image !== '' ) {
					if( is_object($image) && method_exists($image, 'getName') ) {
						return (string) $image->getName();
					}

					return (string) $image;
				}

				return null;
			})
			->setDefault('default_button_label', 'wh_form.label.select_image')
		;
	}
}
