<?php

namespace App\Controller;

use App\Entity\Label;
use App\Repository\LabelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


#[Route("/api/admin/label")]
class NewLabelController extends AbstractController
{
    #[Route("/", name: "label_list", methods: ['GET'])]
    public function listLabel(LabelRepository $repository, SerializerInterface $serializer): Response
    {
        $label = $repository->findAll();
        $jsonContent = $serializer->serialize($label, 'json', ['groups' => 'label.index']);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/{id}", name: "label_details", methods: ['GET'])]
    public function labelDetails(int $id, LabelRepository $repository, SerializerInterface $serializer): Response
    {
        $label = $repository->find($id);

        if (!$label) {
            return new Response('Label not found', Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $serializer->serialize($label, 'json', ['groups' => 'label.show']);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/", name: "label_create", methods: ['POST'])]
    public function createLabel(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $labelData = json_decode($request->getContent(), true);

        $label = new Label();
        $label->setName($labelData['Nom']);

        $entityManager->persist($label);
        $entityManager->flush();

        return $this->json($label, Response::HTTP_CREATED, [], ['groups' => 'label.index']);
    }

    #[Route("/{id}", name: "label_update", methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateLabel(Label $label, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['Nom'])) {
            $label->setName($data['Nom']);
        }

        $entityManager->flush();

        return $this->json($label, Response::HTTP_OK, [], ['groups' => 'label.index']);
    }

    #[Route("/{id}", name: "label_delete", methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteLabel(Label $label, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($label);
        $entityManager->flush();

        return $this->json(['message' => 'Label deleted successfully'], 200);
    }
}
