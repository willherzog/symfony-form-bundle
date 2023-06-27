<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\{AbstractType,FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type is not mapped to the underlying data object as it is only intended to allow an arbitrary static template to be placed within the form.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class IncludeType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefaults([
				'compound' => false,
				'mapped' => false,
				'required' => false,
				'context' => []
			])
			->setDefined('template')
			->setRequired('template')
			->setAllowedTypes('template', 'string')
			->setAllowedTypes('context', ['array','callable'])
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		if( is_callable($options['context']) ) {
			$callback = $options['context'];
			$context = $callback($form);

			if( is_array($context) ) {
				$options['context'] = $context;
			} else {
				throw new \RuntimeException(sprintf('If the "context" option is set to a callable, it must return an array (got a(n) "%s" instead).', \get_debug_type($context)));
			}
		}

		$view->vars['template'] = $options['template'];
		$view->vars['template_context'] = $options['context'];
	}
}
