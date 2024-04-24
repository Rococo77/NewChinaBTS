<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Plat;
use App\Entity\Region;
use App\Repository\IngredientRepository;
use App\Repository\PlatRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/admin/recipe/{idPlat}/compo")]
class NewCompoRecipeController extends AbstractController
{

    #[Route("/", name: "compoRecipe_list", methods: ['GET'])]
    public function compoRecipeDetails(int $idPlat, PlatRepository $platRepository, SerializerInterface $serializer): Response
    {
        $plat = $platRepository->find($idPlat);
        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredients = $plat->getIngredients();

        $jsonContent = $serializer->serialize($ingredients, 'json', ['groups' => 'compo.index']);

        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    #[Route("/{idIngredient}", name: "compoRecipe_details", methods: ['GET'])]
    public function compoRecipeDetailsIngredients (int $idPlat, int $idIngredient, PlatRepository $platRepository, SerializerInterface $serializer): Response
    {
        $plat = $platRepository->find($idPlat);

        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredient = $plat->getIngredients()->filter(function($ingredient) use ($idIngredient) {
            return $ingredient->getId() === $idIngredient;
        })->first();

        if (!$ingredient) {
            return $this->json(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $serializer->serialize($ingredient, 'json', ['groups' => 'compo.index']);

        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    #[Route('/', name: 'compoRecipe_add', methods: ['POST'])]
    public function addCompoRecipe(int $idPlat, Request $request, PlatRepository $platRepository, IngredientRepository $ingredientRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $plat = $platRepository->find($idPlat);
        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['id'])) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $ingredient = $ingredientRepository->find($data['id']);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $plat->addIngredient($ingredient);

        $entityManager->flush();

        $jsonContent = $serializer->serialize($plat, 'json', ['groups' => 'compo.index']);
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{idIngredient}', name: 'compoRecipe_delete', methods: ['DELETE'])]
    public function deleteCompoRecipe(int $idPlat, int $idIngredient, PlatRepository $platRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $plat = $platRepository->find($idPlat);
        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredientToRemove = $plat->getIngredients()->filter(function($ingredient) use ($idIngredient) {
            return $ingredient->getId() === $idIngredient;
        })->first();

        if (!$ingredientToRemove) {
            return $this->json(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $plat->removeIngredient($ingredientToRemove);
        $entityManager->flush();

        return $this->json(['message' => 'Ingredient removed successfully'], Response::HTTP_OK);
    }
    #[Route("/", name: "compoRecipe_update", methods: ['PUT'])]
    public function updateCompoRecipe(int $idPlat, Request $request, PlatRepository $platRepository, IngredientRepository $ingredientRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $plat = $platRepository->find($idPlat);
        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        foreach ($plat->getIngredients() as $ingredient) {
            $plat->removeIngredient($ingredient);
        }

        $data = json_decode($request->getContent(), true);


        foreach ($data as $ingredientId) {
            $ingredient = $ingredientRepository->find($ingredientId);
            if (!$ingredient) {
                return $this->json(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
            }
            $plat->addIngredient($ingredient);
        }

        $entityManager->flush();

        $jsonContent = $serializer->serialize($plat, 'json', ['groups' => 'compo.index']);
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


}
