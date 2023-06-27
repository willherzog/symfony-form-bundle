<?php

namespace WHSymfony\WHFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\{FormInterface,FormView};
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class ModalEditorType extends AbstractType
{
	public function getParent(): string
	{
		return ActionType::class;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'mapped' => true,
			'attr' => ['class' => 'open-editor']
		]);
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['attr']['aria-haspopup'] = 'dialog';
	}
}
