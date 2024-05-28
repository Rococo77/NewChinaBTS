<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/api/admin/fournisseurs")]
class NewFournisseurController extends AbstractController
{
    #[Route("/", name: "fournisseur_list", methods: ['GET'])]
    public function listFournisseur(FournisseurRepository $repository, SerializerInterface $serializer): Response
    {
        $fournisseur = $repository->findAll();
        $jsonContent = $serializer->serialize($fournisseur, 'json', ['groups' => 'fournisseur.index']);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/{id}", name: "fournisseur_details", methods: ['GET'])]
    public function fournisseurDetails(int $id, FournisseurRepository $repository, SerializerInterface $serializer): Response
    {
        $fournisseur = $repository->find($id);

        if (!$fournisseur) {
            return new Response('Fournisseur not found', Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $serializer->serialize($fournisseur, 'json', ['groups' => 'fournisseur.show']);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/", name: "fournisseur_create", methods: ['POST'])]
    public function createFournisseur(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $fournisseurData = json_decode($request->getContent(), true);

        $fournisseur = new Fournisseur();
        $fournisseur->setNom($fournisseurData['Nom']);
        $fournisseur->setLocalisation($fournisseurData['Localisation']);

        $entityManager->persist($fournisseur);
        $entityManager->flush();

        return $this->json($fournisseur, Response::HTTP_CREATED, [], ['groups' => 'fournisseur.index']);
    }

    #[Route("/{id}", name: "fournisseur_update", methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateFournisseur(Fournisseur $fournisseur, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom'])) {
            $fournisseur->setNom($data['Nom']);
        }
        if (isset($data['Localisation'])) {
            $fournisseur->setLocalisation($data['Localisation']);
        }

        $entityManager->flush();

        return $this->json($fournisseur, Response::HTTP_OK, [], ['groups' => 'fournisseur.index']);
    }

    #[Route("/{id}", name: "fournisseur_delete", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteFournisseur(Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($fournisseur);
        $entityManager->flush();

        return $this->json(['message' => 'Fournisseur deleted successfully'], 200);
    }
}
