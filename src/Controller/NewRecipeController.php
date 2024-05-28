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

#[Route("/api/admin/recipes")]
class NewRecipeController extends AbstractController
{

    //Afficher Plat
    #[Route("/", name: "recipe_list", methods: ['GET'])]
    public function listPlats(PlatRepository $repository, SerializerInterface $serializer): Response
    {
        // Vérification des rôles
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

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
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }
        $plat = $repository->find($id);

        $jsonContent = $serializer->serialize($plat, 'json', [
            'groups' => 'recipe.show',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    //Creer Plat
    #[Route("/", name: "recipe_create", methods: ['POST'])]
    public function createRecipe(Request $request, RegionRepository $regionRepository, IngredientRepository $ingredientRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $platData = json_decode($request->getContent(), true);

        // Vérifiez la validité du JSON reçu
        if (!$platData) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $plat = new Plat();

        $plat->setNom($platData['nom'] ?? null);
        $plat->setDescription($platData['description'] ?? null);
        $plat->setPrixUnit($platData['prix_unit'] ?? null);
        $plat->setStockQtt($platData['stock_qtt'] ?? null);
        $plat->setPeremptionDate(new \DateTime($platData['peremption_date'] ?? 'now'));
        $plat->setAllergen($platData['allergen'] ?? null);

        $region = $regionRepository->find($platData['region']['id'] ?? null);
        if (!$region) {
            return $this->json(['message' => 'Region not found'], Response::HTTP_BAD_REQUEST);
        }
        $plat->setRegion($region);

        if (isset($platData['ingredients'])) {
            foreach ($platData['ingredients'] as $ingredientData) {
                $ingredient = $ingredientRepository->find($ingredientData['id']);
                if (!$ingredient) {
                    return $this->json(['message' => 'Ingredient not found'], Response::HTTP_BAD_REQUEST);
                }
                $plat->addIngredient($ingredient);
            }
        }

        $entityManager->persist($plat);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($plat, 'json', [
            'groups' => 'recipe.show',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonContent, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    //Modifier Plat (pas ingrédient)


    #[Route("/{id}", name: "recipe_update", methods: ['PUT'])]
    public function updatePlat(int $id, Request $request, PlatRepository $repository, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $regionRepository, IngredientRepository $ingredientRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

        $plat = $repository->find($id);

        if (!$plat) {
            return new Response('Plat not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom'])) {
            $plat->setNom($data['Nom']);
        }
        if (isset($data['Description'])) {
            $plat->setDescription($data['Description']);
        }
        if (isset($data['PrixUnit'])) {
            $plat->setPrixUnit($data['PrixUnit']);
        }
        if (isset($data['StockQtt'])) {
            $plat->setStockQtt($data['StockQtt']);
        }
        if (isset($data['PeremptionDate'])) {
            $plat->setPeremptionDate(new \DateTime($data['PeremptionDate']));
        }
        if (isset($data['Allergen'])) {
            $plat->setAllergen($data['Allergen']);
        }
        if (isset($data['region']['id'])) {
            $region = $regionRepository->find($data['region']['id']);
            if ($region) {
                $plat->setRegion($region);
            }
        }
        if (isset($data['ingredients'])) {
            foreach ($plat->getIngredients() as $ingredient) {
                $plat->removeIngredient($ingredient);
            }
            foreach ($data['ingredients'] as $ingredientData) {
                $ingredient = $ingredientRepository->find($ingredientData['id']);
                if ($ingredient) {
                    $plat->addIngredient($ingredient);
                }
            }
        }

        $entityManager->flush();

        $jsonContent = $serializer->serialize($plat, 'json', [
            'groups' => 'recipe.show',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
