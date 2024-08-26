<?php

namespace WHSymfony\WHFormBundle\Form\Util;

use Symfony\Component\Form\Util\StringUtil as OrigStringUtil;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class StringUtil extends OrigStringUtil
{
	/**
	 * @inheritDoc
	 */
	static public function fqcnToBlockPrefix(string $fqcn): ?string
	{
		if (preg_match('~([^\\\\]+?)(form)?$~i', $fqcn, $matches)) {
			return strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], $matches[1]));
		}

		return null;
	}

	/**
	 * Applies same character replacements as in {@link Symfony\Component\Form\Extension\Core\Type\TimezoneType}.
	 */
	static public function humanizeTimezone(string $timezone): string
	{
		return str_replace(['/', '_'], [' / ', ' '], $timezone);
	}
}
