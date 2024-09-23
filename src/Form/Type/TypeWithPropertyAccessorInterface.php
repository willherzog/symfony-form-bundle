<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @internal
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
interface TypeWithPropertyAccessorInterface
{
	public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor = null): void;
}
