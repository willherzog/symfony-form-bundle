<?php

namespace WHSymfony\WHFormBundle\Config;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
interface LimitedChoicesEnum
{
	public function isAllowedChoice(): bool;
}
