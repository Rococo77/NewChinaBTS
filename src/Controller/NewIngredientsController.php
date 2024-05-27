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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/admin/ingredients")]
class NewIngredientsController extends AbstractController
{
    #[Route("/", name: "ingredient_list", methods: ['GET'])]
    public function listIngredients(IngredientRepository $repository, SerializerInterface $serializer): Response
    {

        $ingredient = $repository->findAll();

        $jsonContent = $serializer->serialize($ingredient, 'json', ['groups' => 'ingredient.index']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    //AFFICHER DETAIL
    #[Route("/{id}", name: "ingredient_details",  methods: ['GET'])]
    public function ingredientDetails(int $id, IngredientRepository $repository, SerializerInterface $serializer): Response
    {

        $ingredient = $repository->find($id);

        $jsonContent = $serializer->serialize($ingredient, 'json', ['groups' => 'ingredient.show']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    //CREER
    #[Route("/", name: "ingredient_create", methods: ['POST'])]
    public function createIngredient(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $ingredientData = json_decode($request->getContent(), true);

        $ingredient = new Ingredient();
        $ingredient->setNom($ingredientData['Nom']);

        $entityManager->persist($ingredient);
        $entityManager->flush();
        return $this->json($ingredient, Response::HTTP_CREATED, [], ['groups' => 'ingredient.index']);
    }

    //UPDATE
    #[Route("/{id}", methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom']) && $data['Nom'] !== $ingredient->getNom()) {
            $ingredient->setNom($data['Nom']);
        }

        $entityManager->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], ['groups' => ['ingredient.index']]);
    }

    //DELETE
    #[Route("/{id}", name: "ingredient_delete", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $entityManager, IngredientRepository $repository): Response
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();

        $ingredient = $repository->findAll();
        return $this->json($ingredient, 200, [], ['groups' => ['ingredient.index']]);
    }

}
