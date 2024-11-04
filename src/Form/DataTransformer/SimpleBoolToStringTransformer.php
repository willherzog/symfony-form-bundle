<?php

namespace WHSymfony\WHFormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;

/**
 * A simpler version of {@link BooleanToStringTransformer} that omits the false values option
 * (PHP's `empty()` function is used instead) and provides a default for the true value.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class SimpleBoolToStringTransformer implements DataTransformerInterface
{
	public function __construct(protected readonly string $trueValue = '1')
	{}

	public function transform(mixed $value): ?string
	{
		if( $value === null ) {
			return null;
		}

		if( !is_bool($value) ) {
			throw new TransformationFailedException('Expected a boolean.');
		}

		return $value ? $this->trueValue : null;
	}

	public function reverseTransform(mixed $value): bool
	{
		if( $value !== null && !is_string($value) ) {
			throw new TransformationFailedException('Expected a string.');
		}

		return empty($value) ? false : true;
	}
}
