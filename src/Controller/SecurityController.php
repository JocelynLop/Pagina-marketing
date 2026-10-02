<?php

namespace App\Controller;

use App\Security\AppUser;
use App\Security\UserStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('Symfony intercepta esta ruta para cerrar sesion.');
    }

    #[Route('/registro', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserStore $userStore,
        UserPasswordHasherInterface $passwordHasher,
        #[Autowire(env: 'ADMIN_EMAIL')]
        string $adminEmail,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $errors = [];
        $email = mb_strtolower(trim((string) $request->request->get('email')));
        $birthdate = trim((string) $request->request->get('birthdate'));

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('register_user', (string) $request->request->get('_token'))) {
                $errors[] = 'La sesion expiro. Vuelve a intentarlo.';
            }

            $password = (string) $request->request->get('password');
            $confirmPassword = (string) $request->request->get('confirm_password');
            $parsedBirthdate = \DateTimeImmutable::createFromFormat('!d/m/Y', $birthdate);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Escribe un correo valido.';
            }

            if (!$parsedBirthdate || $parsedBirthdate->format('d/m/Y') !== $birthdate) {
                $errors[] = 'Escribe tu fecha de nacimiento en formato DD/MM/AAAA.';
            } elseif ($parsedBirthdate > new \DateTimeImmutable('today')) {
                $errors[] = 'La fecha de nacimiento no puede estar en el futuro.';
            }

            if (mb_strlen($password) < 8) {
                $errors[] = 'La contrasena debe tener al menos 8 caracteres.';
            }

            if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
                $errors[] = 'La contrasena debe incluir mayuscula, minuscula y numero.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Las contrasenas no coinciden.';
            }

            if ($userStore->exists($email) || $email === mb_strtolower(trim($adminEmail))) {
                $errors[] = 'No se pudo crear la cuenta con esos datos.';
            }

            if ($errors === []) {
                $user = new AppUser($email, '');
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $userStore->create($email, $hashedPassword, ['ROLE_USER'], $birthdate);

                $this->addFlash('success', 'Cuenta creada. Ahora puedes iniciar sesion.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'errors' => $errors,
            'email' => $email,
            'birthdate' => $birthdate,
        ]);
    }
}
