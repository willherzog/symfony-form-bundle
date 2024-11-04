<?php

namespace WHSymfony\WHFormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a backed enumeration and a string.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class BackedEnumToStringTransformer implements DataTransformerInterface
{
	public function __construct(protected readonly string $class)
	{
		if( !enum_exists($this->class) ) {
			throw new InvalidArgumentException(sprintf('Enumeration "%s" does not exist.', $this->class));
		}

		if( !is_subclass_of($this->class, \BackedEnum::class) ) {
			throw new InvalidArgumentException(sprintf('Enumeration "%s" is not a backed enum.', $this->class));
		}
	}

	public function transform(mixed $value): mixed
	{
		if( $value === null ) {
			return null;
		}

		if( !$value instanceof \BackedEnum ) {
			throw new TransformationFailedException('Expected a backed enum case.');
		}

		return $value->value;
	}

	public function reverseTransform(mixed $value): mixed
	{
		if( $value === null ) {
			return null;
		}

		if( !is_string($value) && !is_int($value) ) {
			throw new TransformationFailedException('Expected a string or an integer.');
		}

		try {
			return $this->class::from($value);
		} catch( \ValueError ) {
			throw new TransformationFailedException(sprintf('Expected a valid scalar value for enum "%s"', $this->class));
		}
	}
}
