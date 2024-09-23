<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
trait TypeWithTranslatorTrait
{
	private readonly TranslatorInterface $translator;

	public function setTranslator(TranslatorInterface $translator): void {
		$this->translator = $translator;
	}

	final protected function getTranslator(): TranslatorInterface
	{
		return $this->translator;
	}
}
