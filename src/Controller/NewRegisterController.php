<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/open")]
class NewRegisterController extends AbstractController
{
    #[Route('/register', name: 'api_user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $userData = json_decode($request->getContent(), true);

        if ($userData === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($userData['Email']) || empty($userData['Password'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Email and Password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($userData['Email']);
        $user->setPassword($hasher->hashPassword($user, $userData['Password']));
        $user->setRoles(['ROLE_USER']);

        if (!empty($userData['Nom'])) {
            $user->setNom($userData['Nom']);
        }
        if (!empty($userData['Username'])) {
            $user->setUsername($userData['Username']);
        }
        if (!empty($userData['Prenom'])) {
            $user->setPrenom($userData['Prenom']);
        }
        if (!empty($userData['Adresse'])) {
            $user->setAdresse($userData['Adresse']);
        }
        if (!empty($userData['Zip'])) {
            $user->setZIP($userData['Zip']);
        }
        if (!empty($userData['Ville'])) {
            $user->setVille($userData['Ville']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => "User registered successfully"], Response::HTTP_CREATED);
    }
}

