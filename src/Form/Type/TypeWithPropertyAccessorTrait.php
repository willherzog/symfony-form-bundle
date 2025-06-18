<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\PropertyAccess\{PropertyAccess,PropertyAccessorInterface};

/**
 * @internal
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
trait TypeWithPropertyAccessorTrait
{
	private PropertyAccessorInterface $propertyAccessor;

	public function setPropertyAccessor(?PropertyAccessorInterface $propertyAccessor = null): void
	{
		$this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
	}

	final protected function getPropertyAccessor(): PropertyAccessorInterface
	{
		return $this->propertyAccessor;
	}
}
