<?php

namespace WHSymfony\WHFormBundle\Form;

use Symfony\Component\Form\AbstractType;

use WHSymfony\WHFormBundle\Form\Util\StringUtil;

/**
 * Override of AbstractType for use with top-level forms.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
abstract class AbstractForm extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix (): string
	{
		return StringUtil::fqcnToBlockPrefix(static::class) ?: '';
	}
}
