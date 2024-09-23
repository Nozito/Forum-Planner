<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $formType = $request->get('2fa_form');

            if ($formType === '1') {
                // Rediriger l'utilisateur vers la configuration 2FA, sans activer directement
                return $this->redirectToRoute('app_2fa_form');
            } elseif ($formType === '0') {
                if ($user->isTwoFactorEnabled()) {
                    // Désactiver la 2FA pour l'utilisateur
                    $user->setTwoFactorEnabled(false);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    // Ajoutez un message flash de succès
                    $this->addFlash('success', 'L\'authentification à deux facteurs a été désactivée.');
                }
            }
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/2fa_qrcode', name: 'app_2fa_qrcode')]
    public function show2FAQrCode(): Response
    {
        return $this->render('security/2fa_setup.html.twig');
    }
}