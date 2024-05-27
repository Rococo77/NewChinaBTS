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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/admin/regions")]
class NewRegionController extends AbstractController
{

    //LIST DE REGION
    #[Route("/", name: "region_list", methods: ['GET'])]
    public function listRegion(RegionRepository $repository, SerializerInterface $serializer): Response
    {
        $region = $repository->findAll();

        $jsonContent = $serializer->serialize($region, 'json', ['groups' => 'region.index']);
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


// AFFICHER REGION DETAILS
    #[Route("/{id}", name: "region_details",  methods: ['GET'])]
    public function regionDetails(int $id, RegionRepository $repository, SerializerInterface $serializer): Response
    {
        $region = $repository->find($id);

        if (!$region) {
            return $this->json(['message' => 'Region not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $serializer->serialize($region, 'json', [
            'groups' => 'region.show',
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['region'] // Pour éviter la récursion infinie
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    //CREER
    #[Route("/", name: "region_create", methods: ['POST'])]
    public function createRegion(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $region = $serializer->deserialize($request->getContent(), Region::class, 'json');

        $entityManager->persist($region);
        $entityManager->flush();

        return $this->json($region, Response::HTTP_CREATED, [], ['groups' => 'region.index']);
    }

    //UPDATE
    #[Route("/{id}", methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Region $region, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['nom']) && $data['nom'] !== $region->getNom()) {
            $region->setNom($data['nom']);
        }

        $entityManager->flush();

        return $this->json($region, Response::HTTP_OK, [], ['groups' => ['region.index']]);
    }

    //SUPPRIMER
    #[Route("/{id}", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Region $region, EntityManagerInterface $entityManager, RegionRepository $repository): Response
    {
        $entityManager->remove($region);
        $entityManager->flush();

        $recipes = $repository->findAll();
        return $this->json($recipes, 200, [], ['groups' => ['region.index']]);
    }
}
