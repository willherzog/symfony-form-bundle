<?php

namespace WHSymfony\WHFormBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Override of AbstractType strictly for use with root-level forms with no block prefix.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class AbstractAnonymousForm extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix(): string
	{
		return '';
	}
}
