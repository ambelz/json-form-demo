<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\FormSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/generate-form', name: 'api_generate_form_options', methods: ['OPTIONS'])]
    public function generateFormOptions(): Response
    {
        $response = new Response();
        $this->addCorsHeaders($response);
        return $response;
    }

    #[Route('/generate-form', name: 'api_generate_form', methods: ['POST'])]
    public function generateForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jsonContent = $request->getContent();
        
        if (empty($jsonContent)) {
            $response = new JsonResponse(['error' => 'Aucun contenu JSON fourni'], 400);
            $this->addCorsHeaders($response);
            return $response;
        }

        // Validation du JSON
        $requestData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = new JsonResponse([
                'error' => 'JSON invalide',
                'details' => json_last_error_msg()
            ], 400);
            $this->addCorsHeaders($response);
            return $response;
        }

        // Validate the presence of the "schema" field
        if (!isset($requestData['schema'])) {
            $response = new JsonResponse([
                'error' => 'Missing "schema" field',
                'details' => 'Le champ "schema" contenant la structure du formulaire est requis'
            ], 400);
            $this->addCorsHeaders($response);
            return $response;
        }

        $jsonStructure = $requestData['schema'];

        // Validation basique de la structure du formulaire
        if (!isset($jsonStructure['title']) || !isset($jsonStructure['sections'])) {
            $response = new JsonResponse([
                'error' => 'Structure de formulaire invalide',
                'details' => 'Les champs "title" et "sections" sont requis dans le schema'
            ], 400);
            $this->addCorsHeaders($response);
            return $response;
        }

        try {
            // Sauvegarde du schema en base avec UUID
            $formSchema = new FormSchema();
            $formSchema->setJsonStructure($jsonStructure);
            
            $entityManager->persist($formSchema);
            $entityManager->flush();

            // Generate unique URLs
            $formUrl = $this->generateUrl('api_show_form', ['uuid' => $formSchema->getId()]);
            $jsonContentUrl = $this->generateUrl('api_form_json', ['uuid' => $formSchema->getId()]);

            $response = new JsonResponse([
                'success' => true,
                'uuid' => $formSchema->getId(),
                'url' => $formUrl,
                'jsonContentUrl' => $jsonContentUrl,
                'formTitle' => $formSchema->getTitle()
            ]);
            
            $this->addCorsHeaders($response);
            return $response;

        } catch (\Exception $e) {
            $response = new JsonResponse([
                'error' => 'Erreur lors de la sauvegarde du formulaire',
                'details' => $e->getMessage()
            ], 500);
            $this->addCorsHeaders($response);
            return $response;
        }
    }

    #[Route('/generate-form/{uuid}', name: 'api_show_form', methods: ['GET', 'POST'])]
    public function showForm(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $formSchema = $entityManager->getRepository(FormSchema::class)->find($uuid);
        
        if (!$formSchema) {
            throw $this->createNotFoundException('Formulaire non trouvé');
        }

        // Increment access counter
        $formSchema->incrementAccessCount();
        $entityManager->flush();

        return $this->render('demo/form_live.html.twig', [
            'jsonStructure' => $formSchema->getJsonStructure(),
            'category' => 'api',
            'formTitle' => $formSchema->getTitle(),
        ]);
    }

    #[Route('/generate-form/{uuid}/json-content', name: 'api_form_json', methods: ['GET'])]
    public function getFormJson(string $uuid, EntityManagerInterface $entityManager): JsonResponse
    {
        $formSchema = $entityManager->getRepository(FormSchema::class)->find($uuid);
        
        if (!$formSchema) {
            $response = new JsonResponse(['error' => 'Formulaire non trouvé'], 404);
            $this->addCorsHeaders($response);
            return $response;
        }

        $response = new JsonResponse($formSchema->getJsonStructure());
        $this->addCorsHeaders($response);
        return $response;
    }

    private function addCorsHeaders(Response $response): void
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
