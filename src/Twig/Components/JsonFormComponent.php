<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Ambelz\JsonToFormBundle\Service\JsonToFormTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PostHydrate;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent('JsonFormComponent')]
class JsonFormComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public array $jsonStructure;

    #[LiveProp]
    public string $category;

    #[LiveProp]
    public string $formTitle;

    #[LiveProp(writable: true)]
    public bool $submitLoading = false;

    public function __construct(
        private JsonToFormTransformer $jsonToFormTransformer,
        private RequestStack $requestStack,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $data = []; // vos données initiales pour pré-remplir le formulaire

        $structure = $this->jsonStructure;

        $builder = $this->createFormBuilder($data, [
            'label_attr' => [
                'class' => 'h2', // classe pour les labels
            ],
            'attr' => [
                'class' => 'p-3', // classe pour styliser le bloc principal du formulaire
            ],
        ]);

        return $this->jsonToFormTransformer->transform($structure, $data, $builder);
    }

    /**
     * Hook appelé après que les données du composant ont été hydratées depuis le client.
     */
    #[PostHydrate]
    public function afterFormValuesHydrated(): void
    {
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();
        $form = $this->getForm();
        dd($form->getData());

        if (!$form->isValid()) {
            $this->addFlash('error', 'Le formulaireee contient des erreurs. Veuillez les corriger.');
        }
        $this->addFlash('success', 'Félicitationssss ! Votre formulaire a été soumis avec succès.');
    } 
}
