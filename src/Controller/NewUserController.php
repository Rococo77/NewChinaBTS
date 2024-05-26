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
}