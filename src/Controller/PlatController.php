<?php

namespace App\Controller;

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

#[Route("/admin/plat", name: 'admin.plat.')]
class PlatController extends AbstractController
{

    #[Route("/", methods: ['GET'])]
    public function index(PlatRepository $repository): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $plats = $repository->findAll();
        return $this->json($plats, 200, [], ['groups' => ['plat.index']]);
    }

    #[Route("/{id}", name: "get_plat", methods: ["GET"])]
    public function getPlat(int $id, PlatRepository $repository): Response
    {
        $plat = $repository->find($id);

        if (!$plat) {
            return $this->json(['message' => 'Plat not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($plat, 200, [], ['groups' => ['plat.index']]);
    }

    #[Route("/", methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $regionRepository, IngredientRepository $ingredientRepository): Response
    {
        $platData = json_decode($request->getContent(), true);

        $region = $regionRepository->find($platData['region']['id']);
        if(!$region){
            throw $this->createNotFoundException("La catégorie n'existe pas. ");
        }

        $ingredients = [];
        foreach ($platData['ingredient'] as $ingredientData) {
            $ingredient = $ingredientRepository->find($ingredientData['id']);
            if(!$ingredient){
                throw $this->createNotFoundException("L'ingrédient n'existe pas. ");
            }
            $ingredients[] = $ingredient;
        }

        $plat = $serializer->deserialize(json_encode($platData), Plat::class, 'json');

        $plat->setRegion($region);
        foreach ($ingredients as $ingredient) {
            $plat->addIngredient($ingredient);
        }

        $entityManager->persist($plat);
        $entityManager->flush();

        return $this->json($plat, Response::HTTP_CREATED, [], ['groups' => ['plat.index']]);
    }



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

        if (isset($data['ingredients'])) {
            // Supprimez tous les anciens ingrédients du plat
            foreach ($plat->getIngredients() as $existingIngredient) {
                $plat->removeIngredient($existingIngredient);
            }

            // Ajoutez les nouveaux ingrédients fournis dans les données de la requête
            foreach ($data['ingredients'] as $ingredientData) {
                $ingredient = $ingredientRepository->find($ingredientData['id']);
                if (!$ingredient) {
                    // Si l'ingrédient n'est pas trouvé, retournez un message d'erreur
                    return $this->json(['error' => "L'ingrédient avec l'ID " . $ingredientData['id'] . " n'existe pas."], Response::HTTP_NOT_FOUND);
                }
                $plat->addIngredient($ingredient);
            }
        }

        $entityManager->flush();

        return $this->json($plat, Response::HTTP_OK, [], ['groups' => ['plat.index']]);
    }



    #[Route("/{id}", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Plat $plat, EntityManagerInterface $entityManager, PlatRepository $repository): Response
    {
        $entityManager->remove($plat);
        $entityManager->flush();

        $recipes = $repository->findAll();
        return $this->json($recipes, 200, [], ['groups' => ['plat.index']]);
    }

}
