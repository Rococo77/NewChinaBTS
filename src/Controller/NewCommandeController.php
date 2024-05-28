<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class NewCommandeController extends AbstractController
{
    #[Route('api/admin/commande/user-{userId}/panier-{panierId}', name: 'create_commande', methods: ['POST'])]
    public function createCommande(
        int $userId,
        int $panierId,
        UserRepository $userRepository,
        PanierRepository $panierRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {

        // Récupérer l'utilisateur par son ID
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }

        // Récupérer le panier par son ID
        $panier = $panierRepository->find($panierId);

        if (!$panier || $panier->getUsers() !== $user) {
            return new JsonResponse(['error' => 'Panier non trouvé pour cet utilisateur.'], 404);
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setDateCom(new \DateTime());
        $commande->setUser($user);

        // Ajouter les éléments du panier à la commande
        foreach ($panier->getItems() as $item) {
            $item->setCommande($commande);
            $commande->addItem($item);
        }

        // Enregistrer la commande en base de données
        $entityManager->persist($commande);
        $entityManager->flush();

        // Sérialiser la commande pour la réponse avec circular_reference_handler
        $data = $serializer->serialize($commande, 'json', [
            'groups' => ['commande:read'],
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new JsonResponse($data, 201, [], true);
    }

    #[Route('api/admin/commande-{commandeId}/user-{userId}', name: 'delete_commande', methods: ['DELETE'])]
    public function deleteCommande(
        int $commandeId,
        int $userId,
        CommandeRepository $commandeRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupérer l'utilisateur par son ID
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }

        // Récupérer la commande par son ID
        $commande = $commandeRepository->find($commandeId);

        if (!$commande) {
            return new JsonResponse(['error' => 'Commande non trouvée.'], 404);
        }

        // Vérifier que la commande est associée à l'utilisateur
        if ($commande->getUser() !== $user) {
            return new JsonResponse(['error' => 'La commande n\'est pas associée à cet utilisateur.'], 403);
        }

        // Supprimer la commande
        $entityManager->remove($commande);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Commande supprimée avec succès.'], 200);
    }
}
