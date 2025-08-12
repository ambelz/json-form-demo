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
    public string $jsonFilePath;

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
        $data = [];

        $jsonFile = $this->jsonFilePath;
        $structure = json_decode(file_get_contents($jsonFile), true);

        $builder = $this->createFormBuilder($data, [
            'label_attr' => [
                'class' => 'h4',
            ],
            'attr' => [
                'class' => '',
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
        $this->submitLoading = true;
        sleep(2);
        $this->submitLoading = false;

        $this->addFlash('success', 'Félicitations ! Votre formulaire a été soumis avec succès.');

        return $this->redirectToRoute('demo_form', [
            'category' => $this->category,
            'formTitle' => $this->formTitle,
        ]);
    }
}
