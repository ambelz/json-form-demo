<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demo')]
class DemoFormController extends AbstractController
{
    private const JSON_FORMS_PATH = __DIR__.'/../../config/forms/';

    public function __construct()
    {
    }

    #[Route('/', name: 'demo_index')]
    public function index(): Response
    {
        return $this->render('demo/index.html.twig');
    }

    #[Route('/form/{category}/{formName}', name: 'demo_form')]
    public function showForm(string $category, string $formName): Response
    {
        $jsonFile = self::JSON_FORMS_PATH.$category.'/'.$formName.'.json';

        if (!file_exists($jsonFile)) {
            throw new NotFoundHttpException("Formulaire '{$formName}' non trouvé dans la catégorie '{$category}'");
        }

        $jsonFileContent = file_get_contents($jsonFile);
        $jsonFileContent = json_decode($jsonFileContent, true);
        $formTitle = $jsonFileContent['title'] ?? 'Super formulaire';

        return $this->render('demo/form_live.html.twig', [
            'jsonStructure' => $jsonFileContent,
            'category' => $category,
            'formTitle' => $formTitle,
        ]);
    }

    #[Route('/form/{category}/{formName}/json-content', name: 'demo_form_json')]
    public function showFormJson(string $category, string $formName): JsonResponse
    {
        $jsonFile = self::JSON_FORMS_PATH.$category.'/'.$formName.'.json';

        if (!file_exists($jsonFile)) {
            throw new NotFoundHttpException("Formulaire '{$formName}' non trouvé dans la catégorie '{$category}'");
        }

        $jsonFileContent = file_get_contents($jsonFile);
        $jsonData = json_decode($jsonFileContent, true);

        $response = new JsonResponse($jsonData);
        
        // Configuration CORS pour autoriser tous les domaines
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        return $response;
    }
}
