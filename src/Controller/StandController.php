<?php

namespace App\Controller;

use App\Entity\Stand;
use App\Form\StandType;
use App\Repository\StandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/stand')]
final class StandController extends AbstractController
{
    #[Route(name: 'app_stand', methods: ['GET'])]
    public function index(StandRepository $standRepository): Response
    {
        return $this->render('stand/index.html.twig', [
            'stands' => $standRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_stand_new', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_FORUM_ORGANIZER") or is_granted("ROLE_ADMIN")'))]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stand = new Stand();
        $form = $this->createForm(StandType::class, $stand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stand);
            $entityManager->flush();

            return $this->redirectToRoute('app_stand', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stand/new.html.twig', [
            'stand' => $stand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stand_show', methods: ['GET'])]
    public function show(Stand $stand): Response
    {
        return $this->render('stand/show.html.twig', [
            'stand' => $stand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stand_edit', methods: ['GET', 'POST'])]
//#[IsGranted(new Expression('is_granted("ROLE_FORUM_ORGANIZER") or is_granted("ROLE_ADMIN")'))]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $stand = $entityManager->getRepository(Stand::class)->find($id);

        if (!$stand) {
            throw $this->createNotFoundException('Le stand demandé n\'existe pas.');
        }

        $form = $this->createForm(StandType::class, $stand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stand_show', ['id' => $stand->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stand/edit.html.twig', [
            'stand' => $stand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stand_delete', methods: ['POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_FORUM_ORGANIZER") or is_granted("ROLE_ADMIN")'))]
    public function delete(Request $request, Stand $stand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stand->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stand', [], Response::HTTP_SEE_OTHER);
    }
}
