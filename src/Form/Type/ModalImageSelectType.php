<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormBuilderInterface,FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Image-specific version of ModalSelectType with a suitable default for the "button_text" option.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalImageSelectType extends AbstractType
{
	protected readonly string $buttonDefaultLabel;

	public function __construct(TranslatorInterface $translator)
	{
		$this->buttonDefaultLabel = $translator->trans('wh_form.label.select_image');
	}

	public function getParent(): string
	{
		return ModalSelectType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver
			->setDefault('button_text', function (FormInterface $form): string {
				$image = $form->getData();

				if( $image !== null && $image !== '' ) {
					if( is_object($image) && method_exists($image, 'getName') ) {
						return $image->getName();
					}

					return (string) $image;
				}

				return $this->buttonDefaultLabel;
			})
		;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->setAttribute('button_default_label', $this->buttonDefaultLabel);
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['attr']['data-default-label'] = $form->getConfig()->getAttribute('button_default_label', '');
	}
}
