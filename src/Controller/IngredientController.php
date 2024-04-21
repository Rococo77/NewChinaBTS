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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/admin/ingredient", name: 'admin.plat.')]
class IngredientController extends AbstractController
{

    #[Route("/", methods: ['GET'])]
    public function index(IngredientRepository $repository): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $ingredients = $repository->findAll();
        return $this->json($ingredients, 200, [], ['groups' => ['ingredient.index']]);
    }



    #[Route("/{id}", methods: ['GET'])]
    public function getIngredient(IngredientRepository $repository, int $id): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $ingredient = $repository->find($id);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingrédient non trouvé'], 404);
        }

        return $this->json($ingredient, 200, [], ['groups' => ['ingredient.show']]);
    }
    #[Route("/", methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $ingredientData = json_decode($request->getContent(), true);

        $ingredient = $serializer->deserialize(json_encode($ingredientData), Ingredient::class, 'json');


        $entityManager->persist($ingredient);
        $entityManager->flush();


        return $this->json($ingredient, Response::HTTP_CREATED, [], ['groups' => ['ingredient.index']]);
    }

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

    #[Route("/{id}", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $entityManager, IngredientRepository $repository): Response
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();

        $ingredient = $repository->findAll();
        return $this->json($ingredient, 200, [], ['groups' => ['ingredient.index']]);
    }

}
