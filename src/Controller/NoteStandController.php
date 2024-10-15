<?php
namespace App\Controller;

use App\Entity\NoteStand;
use App\Entity\Stand;
use App\Form\NoteStandType;
use App\Repository\NoteStandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoteStandController extends AbstractController
{
    #[Route('/note_stand/new_notes/{id}', name: 'app_note_stand_new', methods: ['GET', 'POST'])]
    #[Is_Granted('ROLE_ATTENDEE')]
    public function new(Stand $stand, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$stand) {
            throw new NotFoundHttpException('Stand not found.');
        }

        $noteStand = new NoteStand();
        $noteStand->setStand($stand);

        $form = $this->createForm(NoteStandType::class, $noteStand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($noteStand);
            $entityManager->flush();

            return $this->redirectToRoute('app_stand_show', ['id' => $stand->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note_stand/new.html.twig', [
            'form' => $form,
            'stand' => $stand,
        ]);
    }

    #[Route('/note_stand/{id}', name: 'app_note_stand_show', methods: ['GET'])]
    public function show(NoteStand $noteStand): Response
    {
        return $this->render('note_stand/show.html.twig', [
            'note_stand' => $noteStand,
        ]);
    }

    //#[Route('/note_stand/{id}/edit', name: 'app_note_stand_edit', methods: ['GET', 'POST'])]
    //#[Is_Granted('ROLE_ATTENDEE')]
    //public function edit(Request $request, NoteStand $noteStand, EntityManagerInterface $entityManager): Response
    //{
    //    $form = $this->createForm(NoteStandType::class, $noteStand);
    //    $form->handleRequest($request);

    //    if ($form->isSubmitted() && $form->isValid()) {
    //        $entityManager->flush();

    //        return $this->redirectToRoute('app_stand_show', ['id' => $noteStand->getStand()->getId()], Response::HTTP_SEE_OTHER);
    //    }

    //    return $this->render('note_stand/edit.html.twig', [
    //        'note_stand' => $noteStand,
    //        'form' => $form->createView(),
    //    ]);
    //}

    #[Route('/note_stand/{id}', name: 'app_note_stand_delete', methods: ['POST'])]
    #[Is_Granted('ROLE_ATTENDEE')]
    public function delete(Request $request, NoteStand $noteStand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $noteStand->getId(), $request->request->get('_token'))) {
            $entityManager->remove($noteStand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stand_show', ['id' => $noteStand->getStand()->getId()], Response::HTTP_SEE_OTHER);
    }
}