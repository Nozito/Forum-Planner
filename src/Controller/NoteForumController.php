<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\NoteForum;
use App\Form\NoteForumType;
use App\Repository\NoteForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//#[IsGranted(new Expression('is_granted("ROLE_FORUM_ORGANIZER") or is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
final class NoteForumController extends AbstractController
{
    //#[Route(name: 'app_note_forum_index', methods: ['GET'])]
    //public function index(NoteForumRepository $noteForumRepository): Response
    //{
    //    return $this->render('note_forum/index.html.twig', [
    //        'note_forums' => $noteForumRepository->findAll(),
    //    ]);
    //}

    #[Route('/new/{id}', name: 'app_note_forum_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ATTENDEE')]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $forum = $entityManager->getRepository(Forum::class)->find($id);

        if (!$forum) {
            throw $this->createNotFoundException('Forum not found');
        }

        $noteForum = new NoteForum();
        $noteForum->setForum($forum);

        $form = $this->createForm(NoteForumType::class, $noteForum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($noteForum);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_show', ['id' => $forum->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note_forum/new.html.twig', [
            'note_forum' => $noteForum,
            'form' => $form,
            'forum' => $forum,
        ]);
    }

    //#[Route('/{id}', name: 'app_note_forum_show', methods: ['GET'])]
    //#[IsGranted(('ROLE_ATTENDEE'))]
    //public function show(NoteForum $noteForum): Response
    //{
    //    return $this->render('note_forum/show.html.twig', [
    //        'note_forum' => $noteForum,
    //    ]);
    //}

    //#[Route('/{id}/edit', name: 'app_note_forum_edit', methods: ['GET', 'POST'])]
    //#[IsGranted(('ROLE_ATTENDEE'))]
    //public function edit(Request $request, NoteForum $noteForum, EntityManagerInterface $entityManager): Response
    //{
    //    $form = $this->createForm(NoteForumType::class, $noteForum);
    //    $form->handleRequest($request);

    //    if ($form->isSubmitted() && $form->isValid()) {
    //        $entityManager->flush();

    //        return $this->redirectToRoute('app_note_forum_index', [], Response::HTTP_SEE_OTHER);
    //    }

    //    return $this->render('note_forum/edit.html.twig', [
    //        'note_forum' => $noteForum,
    //        'form' => $form,
    //    ]);
    //}

    #[Route('/{id}', name: 'app_note_forum_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ATTENDEE')]
    public function delete(Request $request, NoteForum $noteForum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noteForum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($noteForum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_note_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}
