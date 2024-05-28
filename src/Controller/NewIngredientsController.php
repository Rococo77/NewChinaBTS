<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/admin/ingredients")]
class NewIngredientsController extends AbstractController
{
    #[Route("/", name: "ingredient_list", methods: ['GET'])]
    public function listIngredients(IngredientRepository $repository, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

        $ingredient = $repository->findAll();

        $jsonContent = $serializer->serialize($ingredient, 'json', ['groups' => 'ingredient.index']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/{id}", name: "ingredient_details", methods: ['GET'])]
    public function ingredientDetails(int $id, IngredientRepository $repository, SerializerInterface $serializer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

        $ingredient = $repository->find($id);

        $jsonContent = $serializer->serialize($ingredient, 'json', ['groups' => 'ingredient.show']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/", name: "ingredient_create", methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function createIngredient(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $ingredientData = json_decode($request->getContent(), true);

        $ingredient = new Ingredient();
        $ingredient->setNom($ingredientData['Nom']);
        $ingredient->setAllergen($ingredientData['Allergen']);

        $entityManager->persist($ingredient);
        $entityManager->flush();

        return $this->json($ingredient, Response::HTTP_CREATED, [], ['groups' => 'ingredient.index']);
    }

    #[Route("/{id}", methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_ADMIN")]
    public function updateIngredient(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom']) && $data['Nom'] !== $ingredient->getNom()) {
            $ingredient->setNom($data['Nom']);
        }

        $entityManager->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], ['groups' => ['ingredient.index']]);
    }

    #[Route("/{id}", name: "ingredient_delete", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteIngredient(Ingredient $ingredient, EntityManagerInterface $entityManager, IngredientRepository $repository): Response
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();

        $ingredients = $repository->findAll();
        return $this->json($ingredients, 200, [], ['groups' => ['ingredient.index']]);
    }

    #[Route("/{id}/fournisseurs", name: "ingredient_add_fournisseur", methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function addFournisseurToIngredient(int $id, Request $request, IngredientRepository $ingredientRepository, FournisseurRepository $fournisseurRepository, EntityManagerInterface $entityManager): Response
    {
        $ingredient = $ingredientRepository->find($id);
        if (!$ingredient) {
            return new Response('Ingredient not found', Response::HTTP_NOT_FOUND);
        }

        $fournisseurData = json_decode($request->getContent(), true);
        $fournisseur = $fournisseurRepository->find($fournisseurData['id']);
        if (!$fournisseur) {
            return new Response('Fournisseur not found', Response::HTTP_NOT_FOUND);
        }

        $ingredient->addFournisseur($fournisseur);
        $entityManager->flush();

        return new Response('Fournisseur added to ingredient', Response::HTTP_OK);
    }

    #[Route("/{id}/fournisseurs/{fournisseurId}", name: "ingredient_remove_fournisseur", methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function removeFournisseurFromIngredient(int $id, int $fournisseurId, IngredientRepository $ingredientRepository, FournisseurRepository $fournisseurRepository, EntityManagerInterface $entityManager): Response
    {
        $ingredient = $ingredientRepository->find($id);
        if (!$ingredient) {
            return new Response('Ingredient not found', Response::HTTP_NOT_FOUND);
        }

        $fournisseur = $fournisseurRepository->find($fournisseurId);
        if (!$fournisseur) {
            return new Response('Fournisseur not found', Response::HTTP_NOT_FOUND);
        }

        $ingredient->removeFournisseur($fournisseur);
        $entityManager->flush();

        return new Response('Fournisseur removed from ingredient', Response::HTTP_OK);
    }
}
