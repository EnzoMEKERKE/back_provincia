<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserRepository $users, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('app_account');
        }

        $data = [
            'pseudo' => trim((string) $request->request->get('pseudo', '')),
            'nom' => trim((string) $request->request->get('nom', '')),
            'prenom' => trim((string) $request->request->get('prenom', '')),
            'email' => strtolower(trim((string) $request->request->get('email', ''))),
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('register', (string) $request->request->get('_token', ''))) {
                $errors[] = 'Le formulaire a expiré. Réessaie.';
            }

            foreach ($data as $field => $value) {
                if ($value === '' || strlen($value) > 255) {
                    $errors[] = sprintf('Le champ %s est obligatoire et doit contenir au plus 255 caractères.', $field);
                }
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            } elseif ($users->findOneBy(['email' => $data['email']]) !== null) {
                $errors[] = 'Cette adresse email est déjà utilisée.';
            }

            $password = (string) $request->request->get('password', '');
            if (strlen($password) < 12) {
                $errors[] = 'Le mot de passe doit contenir au moins 12 caractères.';
            }
            if ($password !== (string) $request->request->get('password_confirm', '')) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if ($errors === []) {
                $user = (new User())
                    ->setPseudo($data['pseudo'])
                    ->setNom($data['nom'])
                    ->setPrenom($data['prenom'])
                    ->setEmail($data['email'])
                    ->setNiveau(1);
                $user->setPassword($passwordHasher->hashPassword($user, $password));

                try {
                    $entityManager->persist($user);
                    $entityManager->flush();
                } catch (UniqueConstraintViolationException) {
                    $errors[] = 'Cette adresse email est déjà utilisée.';
                }

                if ($errors === []) {
                    $this->addFlash('success', 'Compte créé. Tu peux maintenant te connecter.');

                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/register.html.twig', ['data' => $data, 'errors' => $errors]);
    }

    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('app_account');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['POST'])]
    public function logout(): never
    {
        throw new \LogicException('La déconnexion est gérée par Symfony Security.');
    }

    #[Route('/compte', name: 'app_account', methods: ['GET'])]
    public function account(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('security/account.html.twig');
    }
}
