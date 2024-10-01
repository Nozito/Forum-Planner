<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RoleFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserManagerController extends AbstractController
{
    #[Route('/manager_user', name: 'app_user_manager')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_manager/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/roles_user', name: 'app_edit_roles', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoleFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //Message flash
            $this->addFlash('success', 'Roles updated successfully!');

            return $this->redirectToRoute('app_user_manager', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_manager/edit-roles.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/new_user', name: 'app_usermanager_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_manager', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_manager/new_user.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}