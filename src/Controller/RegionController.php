<?php

namespace App\Controller;

use App\Entity\Region;
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

#[Route("/admin/region", name: 'admin.region.')]
class RegionController extends AbstractController
{

    #[Route("/", methods: ['GET'])]
    public function index(RegionRepository $repository): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $regions = $repository->findAll();
        return $this->json($regions, 200, [], ['groups' => ['region.index']]);
    }

    #[Route("/", methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RegionRepository $repository): Response
    {
        $regionData = json_decode($request->getContent(), true);

        $region = $serializer->deserialize(json_encode($regionData), Region::class, 'json');


        $entityManager->persist($region);
        $entityManager->flush();

        return $this->json($region, Response::HTTP_CREATED, [], ['groups' => ['region.index']]);
    }

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

    #[Route("/{id}", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Region $region, EntityManagerInterface $entityManager, RegionRepository $repository): Response
    {
        $entityManager->remove($region);
        $entityManager->flush();

        $recipes = $repository->findAll();
        return $this->json($recipes, 200, [], ['groups' => ['region.index']]);
    }

}
