<?php

namespace WHSymfony\WHFormBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use WHPHP\Util\StringUtil;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHFormExtension extends AbstractExtension
{
	/**
	 * @inheritDoc
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('form_attr_html_classes', function (array $attr, array $prefixes, array $suffixes = []): array {
				$classes = $prefixes;

				if( isset($attr['class']) && is_string($attr['class']) ) {
					$classes[] = trim($attr['class']);
				}

				$classes = array_merge($classes, $suffixes);
				$classesVerified = [];

				foreach( $classes as $class ) {
					if( is_string($class) && $class !== '' ) {
						$classesVerified[] = $class;
					}
				}

				$attr['class'] = implode(' ', array_unique($classesVerified));

				return $attr;
			}),

			new TwigFunction('expanded_choice_container_classes', function ($currentValue, $choiceValue): string {
				$classes = ['expanded', 'choice'];

				if( is_string($choiceValue) && !is_numeric($choiceValue) ) {
					$classes[] = StringUtil::convertUnderscoresToDashes($choiceValue);
				}

				if( $choiceValue === $currentValue ) {
					$classes[] = 'selected';
				}

				return implode(' ', $classes);
			})
		];
	}
}
