<?php

namespace WHSymfony\WHFormBundle\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\Form\Extension\Core\Type\{ButtonType,ChoiceType,DateType,FormType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use WHPHP\Util\StringUtil;

/**
 * Extension for the native Symfony form types to override certain behaviors.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class BaseTypeExtension extends AbstractTypeExtension
{
	public static function getExtendedTypes(): iterable
	{
		return [ButtonType::class, FormType::class];
	}

	public function __construct(protected readonly bool $idAttributesUseDashes)
	{}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setInfo('help_html', 'Enables use of the Markdown filter with help text (see the "form_help" block in form_layout.html.twig).');

		$resolver
			->define('help_markdown_lines')
			->allowedValues('single', 'line_breaks_only', 'full_multi_line')
			->default('single')
			->info('Allow enabling either full multi-line formatting or just line breaks with the Markdown filter on a case-by-case basis.')
		;

		$resolver
			->define('translate_attributes')
			->allowedTypes('bool')
			->default(true)
			->info('Allow disabling translation of HTML attributes specifically (i.e. without disabling translation of labels and help messages).')
		;

		$resolver
			->define('use_parent_row_type')
			->allowedTypes('bool', 'null')
			->default(null)
			->info('If TRUE, the block prefix of the parent form type is used to generate the HTML type class for the field\'s row element (instead of its own block prefix). Also, by default, any type with ChoiceType or DateType as its parent will automatically use that parent\'s block prefix; if FALSE, this behavior is disabled.')
		;

		$resolver
			->define('immutable')
			->allowedTypes('bool')
			->default(false)
			->info('Field type does not allow its value to be changed: skip adding required/disabled classes. Also adds the row class "immutable" and the boolean "readonly" attribute to the widget.')
		;

		$resolver
			->define('indent_levels')
			->allowedTypes('int', 'null')
			->default(null)
			->info('How many indentation levels to use (instead of the configured default) if this is a root form.')
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['indent_levels'] = $form->isRoot() ? $options['indent_levels'] : null;
		$view->vars['translate_attributes'] = $options['translate_attributes'];
		$view->vars['help_markdown_lines'] = $options['help_markdown_lines'];

		$rowClasses = [];
		$formType = $form->getConfig()->getType();

		if( $options['use_parent_row_type'] !== false
			&& $formType->getParent() !== null
			&& ($options['use_parent_row_type'] === true
			|| ($formType->getParent()->getInnerType() instanceof ChoiceType)
			|| ($formType->getParent()->getInnerType() instanceof DateType))
		) {
			$blockPrefix = $formType->getParent()->getBlockPrefix();
		} else {
			$blockPrefix = $formType->getBlockPrefix();
		}

		// Get the cardinal/HTML field type.
		$type = match($blockPrefix) {
			'choice' => $options['expanded'] ? ($options['multiple'] ? 'checkbox' : 'radio') : 'select',
			'integer', 'money', 'percent' => 'number',
			'include' => 'custom',
			default => $blockPrefix
		};

		// Prepend automated classes to row class attribute.
		if( $type !== '' && $type !== 'fieldset' ) {
			$rowClasses[] = StringUtil::convertUnderscoresToDashes($type, true);
		}

		$name = $form->getName();

		if( $name !== '' && !is_numeric($name) ) {
			$name = StringUtil::convertUnderscoresToDashes($name, true);
			$addName = false;

			if( $name !== $type ) {
				if( $form->getParent()?->getConfig()->hasOption('prototype_name') ) {
					$protoName = $form->getParent()->getConfig()->getOption('prototype_name');

					if( $name !== $protoName ) {
						$addName = true;
					}
				} else {
					$addName = true;
				}
			}

			if( $addName ) {
				$rowClasses[] = $name;
			}
		}

		if( $options['immutable'] ) {
			$rowClasses[] = 'immutable';
			$view->vars['attr']['readonly'] = true;
		} else {
			if( isset($view->vars['required']) && $view->vars['required'] ) {
				if( !$view->vars['compound'] ) {
					$rowClasses[] = 'required';
				}
			} elseif( $view->vars['disabled'] ) {
				$rowClasses[] = 'disabled';
			}
		}

		$rowClassesStr = implode(' ', $rowClasses);

		if( isset($view->vars['row_attr']['class']) ) {
			$rowClassesStr .= ' '. trim($view->vars['row_attr']['class']);
		}

		$view->vars['row_attr']['class'] = $rowClassesStr;

		// Use dash instead of underscore as the separator in the widget ID attribute.
		if( $this->idAttributesUseDashes ) {
			if( $view->parent && $view->parent->vars['full_name'] !== '' ) {
				$view->vars['id'] = sprintf('%s-%s', $view->parent->vars['id'], $name);
			} else {
				$view->vars['id'] = $name;
			}
		}
	}
}
