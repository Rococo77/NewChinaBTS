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

#[Route("/admin/recipes")]
class NewRecipeController extends AbstractController
{

    //Afficher Plat
    #[Route("/", name: "recipe_list", methods: ['GET'])]
    public function listPlats(PlatRepository $repository, SerializerInterface $serializer): Response
    {
        $plats = $repository->findAll();

        $jsonContent = $serializer->serialize($plats, 'json', ['groups' => 'recipe.index']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    //Afficher Plat en détail

    #[Route("/{id}", name: "recipe_details",  methods: ['GET'])]
    public function platDetails(int $id, PlatRepository $repository, SerializerInterface $serializer): Response
    {

        $plats = $repository->find($id);

        $jsonContent = $serializer->serialize($plats, 'json', ['groups' => 'recipe.show']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    //Creer Plat

    #[Route("/", name: "recipe_create", methods: ['POST'])]
    public function createRecipe(Request $request, PlatRepository $platRepository, RegionRepository $regionRepository, IngredientRepository $ingredientRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $platData = json_decode($request->getContent(), true);

        $plat = new Plat();

        $plat->setNom($platData['nom']);
        $plat->setDescription($platData['description']);
        $plat->setPrixUnit($platData['prix_unit']);
        $plat->setStockQtt($platData['stock_qtt']);
        $plat->setPeremptionDate((new \DateTime())->modify('+2 days'));
        $plat->setAllergen($platData['allergen']);

        $region = $regionRepository->find($platData['region']['id']);
        if (!$region) {
            return $this->json(['message' => 'Region not found'], Response::HTTP_BAD_REQUEST);
        }
        $plat->setRegion($region);

        foreach ($platData['ingredients'] as $ingredientData) {
            $ingredient = $ingredientRepository->find($ingredientData['id']);
            if (!$ingredient) {
                return $this->json(['message' => 'Ingredient not found'], Response::HTTP_BAD_REQUEST);
            }
            $plat->addIngredient($ingredient);
        }

        $entityManager->persist($plat);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($plat, 'json', ['groups' => 'recipe.show']);
        return new Response($jsonContent, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    //Modifier Plat (pas ingrédient)


    #[Route("/{id}", methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Plat $plat, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $regionRepository, IngredientRepository $ingredientRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom']) && $data['Nom'] !== $plat->getNom()) {
            $plat->setNom($data['Nom']);
        }
        if (isset($data['Description']) && $data['Description'] !== $plat->getDescription()) {
            $plat->setDescription($data['Description']);
        }
        if (isset($data['PrixUnit']) && $data['PrixUnit'] !== $plat->getPrixUnit()) {
            $plat->setPrixUnit($data['PrixUnit']);
        }
        if (isset($data['StockQtt']) && $data['StockQtt'] !== $plat->getStockQtt()) {
            $plat->setStockQtt($data['StockQtt']);
        }
        if (isset($data['Allergen']) && $data['Allergen'] !== $plat->getAllergen()) {
            $plat->setAllergen($data['Allergen']);
        }

        if (isset($data['region']['id'])){
            $region = $regionRepository->find($data['region']['id']);
            if ($region && $region !== $plat->getRegion()){
                $plat->setRegion($region);
            }
        }

        $entityManager->flush();

        return $this->json($plat, Response::HTTP_OK, [], ['groups' => ['recipe.show']]);
    }

    //Supprimer

    #[Route("/{id}", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Plat $plat, EntityManagerInterface $entityManager, PlatRepository $repository): Response
    {
        $entityManager->remove($plat);
        $entityManager->flush();

        $recipes = $repository->findAll();
        return $this->json($recipes, 200, [], ['groups' => ['recipe.index']]);
    }




}
