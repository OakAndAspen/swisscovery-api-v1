<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UtilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class LoginController
{
    /**
     * @Route("/signup", methods="POST")
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function signup(Request $req, EntityManagerInterface $em)
    {
        // Get the request elements
        $email = $req->get('email');
        $password = $req->get('password');

        // Check that email is not already used
        $user = $em->getRepository(User::class)->findBy(['email' => $email]);
        if ($user) return new JsonResponse(['error' => 'User with email ' . $email . ' already exists']);

        // Create the new user
        $user = new User();
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['success' => 'User was successfully created.']);
    }

    /**
     * @Route("/login", methods="POST")
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function login(Request $req, EntityManagerInterface $em)
    {
        if (!$req->get('email') || !$req->get('password')) {
            return new JsonResponse(['error' => 'loginMissingData']);
        }

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $req->get('email')
        ]);

        if (!$user) return new JsonResponse(['error' => 'User not found']);

        if (!password_verify($req->get('password'), $user->getPassword())) {
            return new JsonResponse(['error' => 'loginPwIncorrect']);
        }

        // Create a JWT
        $jwt = UtilityService::generateJWT($user);
        $em->flush();

        return new JsonResponse(['authKey' => $jwt]);
    }
}
