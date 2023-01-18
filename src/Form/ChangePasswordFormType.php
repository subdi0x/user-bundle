<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('plainPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'first_options' => [
					'constraints' => [
						new NotBlank([
							'message' => 'Please enter a password',
						]),
						new Length([
							'min' => 6,
							'minMessage' => 'Password must be at least {{ limit }} characters',
							// max length allowed by Symfony for security reasons
							'max' => 4096,
						]),
					],
					'label' => 'New Password',
				],
				'second_options' => [
					'label' => 'Re-Type Password',
				],
				'invalid_message' => 'The entered passwords do not match',
				// Instead of being set onto the object directly,
				// this is read and encoded in the controller
				'mapped' => false,
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([]);
	}
}
