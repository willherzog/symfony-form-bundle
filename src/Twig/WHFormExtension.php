<?php

namespace WHSymfony\WHFormBundle\Twig;

use Symfony\Component\Form\FormView;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\{TwigFilter,TwigFunction};

use WHPHP\Util\StringUtil;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHFormExtension extends AbstractExtension implements GlobalsInterface
{
	private int $indentLevel;
	private string $indentPrototype;

	public function __construct(protected readonly int $defaultFormIndent, int $indentSpaces)
	{
		$this->indentLevel = $defaultFormIndent;
		$this->indentPrototype = $indentSpaces > 0 ? str_repeat(' ', $indentSpaces) : "\t";
	}

	/**
	 * @inheritDoc
	 */
	public function getGlobals(): array
	{
		return [
			'form_indent_prototype' => $this->indentPrototype
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getFilters(): array
	{
		return [
			new TwigFilter('form_trim_trailing_newlines', function (string $str, string $newlineChars = "\n\r"): string {
				return rtrim($str, $newlineChars);
			}, ['is_safe' => ['all']])
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('form_indent', function (FormView $form = null): string {
				if( isset($form->vars['indent_levels']) ) {
					$this->indentLevel = $form->vars['indent_levels'];
				}

				return str_repeat($this->indentPrototype, $this->indentLevel);
			}),

			new TwigFunction('form_set_indent_level', function (int $levelToSet = null): void {
				$this->indentLevel = $levelToSet ?? $this->defaultFormIndent;
			}),

			new TwigFunction('form_increment_indent_level', function (int $levelsToIncrement = 1): void {
				$this->indentLevel += $levelsToIncrement;
			}),

			new TwigFunction('form_decrement_indent_level', function (int $levelsToDecrement = 1): void {
				if( $levelsToDecrement > $this->indentLevel ) {
					$this->indentLevel = 0;
				} else {
					$this->indentLevel -= $levelsToDecrement;
				}
			}),

			new TwigFunction('form_current_indent_level', function (): string {
				return $this->indentLevel;
			}),

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
