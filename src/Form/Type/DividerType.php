<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type is not mapped to the underlying data object as it is only intended to allow an <hr> tag to be placed within the form.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class DividerType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'compound' => false,
				'mapped' => false,
				'required' => false,
				'label' => false
			])
		;
	}
}
