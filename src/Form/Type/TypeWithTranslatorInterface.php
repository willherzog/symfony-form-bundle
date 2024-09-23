<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
interface TypeWithTranslatorInterface
{
	public function setTranslator(TranslatorInterface $translator): void;
}
