<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * A "toggle switch" visual mod for single-checkbox fields: backend functionality is identical.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ToggleSwitchType extends AbstractType
{
	public function getParent(): string
	{
		return CheckboxType::class;
	}
}
