<?php

namespace WHSymfony\WHFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('whsymfony');

		$treeBuilder->getRootNode()
			->children()
				->integerNode('indent_spaces')
					->defaultValue(0)
					->info('Amount of spaces to use for each level of indentation (positive integer = that number of spaces is used instead of a tab character).')
				->end()
				->arrayNode('form')
					->isRequired()
					->addDefaultsIfNotSet()
					->children()
						->integerNode('default_indent')
							->defaultValue(2)
							->info('Indentation level to use by default for root forms. Note the indentation levels of child forms are automatically incremented based on the indentation level of their parent form(s).')
						->end()
						->booleanNode('default_optional')
							->defaultTrue()
							->info('Whether to make the "required" option default to FALSE.')
						->end()
						->booleanNode('id_attributes_use_dashes')
							->defaultTrue()
							->info('Whether to convert underscores to dashes for form widget "id" attributes.')
						->end()
					->end()
				->end()
			->end()
		;

		return $treeBuilder;
	}
}
