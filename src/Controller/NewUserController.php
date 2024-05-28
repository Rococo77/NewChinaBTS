<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/api/admin/user")]
class NewUserController extends AbstractController
{
    #[Route('/create', name: 'api_user_create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $userData = json_decode($request->getContent(), true);

        if ($userData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setNom($userData['Nom']);
        $user->setUsername($userData['Username']);
        $user->setPrenom($userData['Prenom']);
        $user->setAdresse($userData['Adresse']);
        $user->setZIP($userData['Zip']);
        $user->setVille($userData['Ville']);
        $user->setEmail($userData['Email']);
        $hashedPassword = $hasher->hashPassword($user, $userData['Password']);
        $user->setPassword($hashedPassword);
        $user->setRoles($userData['Roles']);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => "L'utilisateur a été créé avec succès"], Response::HTTP_CREATED);
    }

    #[Route('/update/{id}', name: 'api_user_update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function updateUser(int $id, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $userData = json_decode($request->getContent(), true);

        if ($userData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (isset($userData['Nom'])) {
            $user->setNom($userData['Nom']);
        }
        if (isset($userData['Username'])) {
            $user->setUsername($userData['Username']);
        }
        if (isset($userData['Prenom'])) {
            $user->setPrenom($userData['Prenom']);
        }
        if (isset($userData['Adresse'])) {
            $user->setAdresse($userData['Adresse']);
        }
        if (isset($userData['Zip'])) {
            $user->setZIP($userData['Zip']);
        }
        if (isset($userData['Ville'])) {
            $user->setVille($userData['Ville']);
        }
        if (isset($userData['Email'])) {
            $user->setEmail($userData['Email']);
        }
        if (isset($userData['Password'])) {
            $hashedPassword = $hasher->hashPassword($user, $userData['Password']);
            $user->setPassword($hashedPassword);
        }
        if (isset($userData['Roles'])) {
            $user->setRoles($userData['Roles']);
        }

        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => "Les informations de l'utilisateur ont été mises à jour avec succès"], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => "L'utilisateur a été supprimé avec succès"], Response::HTTP_OK);
    }

    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function getCurrentUser(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['status' => 'error', 'message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $userData = [
            'id' => $user->getId(),
            'Nom' => $user->getNom(),
            'Username' => $user->getUsername(),
            'Prenom' => $user->getPrenom(),
            'Adresse' => $user->getAdresse(),
            'Zip' => $user->getZIP(),
            'Ville' => $user->getVille(),
            'Email' => $user->getEmail(),
            'Roles' => $user->getRoles(),
        ];

        return new JsonResponse(['status' => 'success', 'data' => $userData], Response::HTTP_OK);
    }
    #[Route('/show/{id}', name: 'api_user_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function showUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $userData = [
            'id' => $user->getId(),
            'Nom' => $user->getNom(),
            'Username' => $user->getUsername(),
            'Prenom' => $user->getPrenom(),
            'Adresse' => $user->getAdresse(),
            'Zip' => $user->getZIP(),
            'Ville' => $user->getVille(),
            'Email' => $user->getEmail(),
            'Roles' => $user->getRoles(),
        ];

        return new JsonResponse(['status' => 'success', 'data' => $userData], Response::HTTP_OK);
    }
}
