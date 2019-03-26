<?php
declare(strict_types=1);

namespace App\UI\Controller\_DIRECTORY_\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class _FILENAME_Form
 * @package App\UI\Controller\_DIRECTORY_\Form
 */
class _FILENAME_Form extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('id', TextType::class, [
//                'constraints' => [
//                    new NotBlank(),
//                ],
//            ]);
    }
}
