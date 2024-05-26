<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\PanierItem;
use App\Repository\PanierRepository;
use App\Repository\PlatRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


#[Route("/api/admin/panier")]
class NewPanierController extends AbstractController
{

    #Afficher le panier d'un utilisatuer
    #[Route("/user/{userId}", name: "panier_show", methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function showPanier(int $userId, PanierRepository $panierRepository, SerializerInterface $serializer): JsonResponse
    {
        $panier = $panierRepository->findOneBy(['Users' => $userId]);

        if (!$panier) {
            return new JsonResponse(['error' => 'Le panier pour cet utilisateur n\'existe pas.'], 404);
        }

        $data = $serializer->serialize($panier, 'json', ['groups' => ['panier:read']]);

        return new JsonResponse($data, 200, [], true);
    }

    #Creer un panier
    #[Route('/user/{userId}/', name: 'create_panier', methods: ['POST'])]
    public function createPanier(
        int $userId,
        Request $request,
        UserRepository $userRepository,
        PlatRepository $platRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['items']) || !is_array($data['items'])) {
            return new JsonResponse(['error' => 'Les éléments du panier sont manquants ou invalides.'], 400);
        }

        $panier = new Panier();
        $panier->setUsers($user);

        foreach ($data['items'] as $itemData) {
            if (!isset($itemData['platId']) || !isset($itemData['quantité'])) {
                continue;
            }

            $plat = $platRepository->find($itemData['platId']);

            if (!$plat) {
                continue;
            }

            $panierItem = new PanierItem();
            $panierItem->setPlat($plat);
            $panierItem->setQuantité($itemData['quantité']);
            $panierItem->setPanier($panier);

            $entityManager->persist($panierItem);
        }

        $entityManager->persist($panier);
        $entityManager->flush();

        $data = $serializer->serialize($panier, 'json', ['groups' => ['panier:read']]);

        return new JsonResponse($data, 201, [], true);
    }

    #[Route('/{panierId}', name: 'panier_items_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function showPanierItems(int $panierId, PanierRepository $panierRepository, SerializerInterface $serializer): JsonResponse
    {
        $panier = $panierRepository->find($panierId);

        if (!$panier) {
            return new JsonResponse(['error' => 'Le panier avec cet ID n\'existe pas.'], 404);
        }

        $data = $serializer->serialize($panier->getItems(), 'json', ['groups' => ['panier:read']]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{panierId}', name: 'panier_update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function updatePanier(
        int $panierId,
        Request $request,
        PanierRepository $panierRepository,
        PlatRepository $platRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $panier = $panierRepository->find($panierId);

        if (!$panier) {
            return new JsonResponse(['error' => 'Le panier avec cet ID n\'existe pas.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['items']) || !is_array($data['items'])) {
            return new JsonResponse(['error' => 'Les éléments du panier sont manquants ou invalides.'], 400);
        }

        # Supprimer les éléments existants du panier
        foreach ($panier->getItems() as $existingItem) {
            $entityManager->remove($existingItem);
        }
        $panier->getItems()->clear();

        # Ajouter les nouveaux éléments
        foreach ($data['items'] as $itemData) {
            if (!isset($itemData['platId']) || !isset($itemData['quantité'])) {
                continue;
            }

            $plat = $platRepository->find($itemData['platId']);

            if (!$plat) {
                continue;
            }

            $panierItem = new PanierItem();
            $panierItem->setPlat($plat);
            $panierItem->setQuantité($itemData['quantité']);
            $panierItem->setPanier($panier);

            $entityManager->persist($panierItem);
            $panier->addItem($panierItem);
        }

        $entityManager->flush();

        $data = $serializer->serialize($panier, 'json', ['groups' => ['panier:read']]);

        return new JsonResponse($data, 200, [], true);
    }

    #Supprimer un panier
    #[Route('/{panierId}', name: 'panier_delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function deletePanier(
        int $panierId,
        PanierRepository $panierRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $panier = $panierRepository->find($panierId);

        if (!$panier) {
            return new JsonResponse(['error' => 'Le panier avec cet ID n\'existe pas.'], 404);
        }

        $entityManager->remove($panier);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Le panier a été supprimé avec succès.'], 200);
    }
}