<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwoFactorController extends AbstractController
{
    private GoogleAuthenticatorInterface $googleAuthenticator;
    private EntityManagerInterface $entityManager;

    public function __construct(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager)
    {
        $this->googleAuthenticator = $googleAuthenticator;
        $this->entityManager = $entityManager;
    }

    #[Route('/2fa/setup', name: 'app_2fa_setup')]
    public function setup2FA(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('auth_code');
            if ($this->googleAuthenticator->checkCode($user, $code)) {
                $user->setTwoFactorEnabled(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', '2FA activée avec succès!');
                return $this->redirectToRoute('profile');
            } else {
                $this->addFlash('error', 'Le code est incorrect, veuillez réessayer.');
            }
        }

        // Générer l'URL vers la route du QR code
        $qrCodeUrl = $this->generateUrl('app_2fa_qrcode');

        return $this->render('security/2fa_setup.html.twig', [
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    #[Route('/2fa/qrcode', name: 'app_2fa_qrcode')]
    public function showQrCode(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getGoogleAuthenticatorSecret()) {
            throw $this->createNotFoundException('Secret 2FA non trouvé pour l\'utilisateur.');
        }

        $qrCodeContent = $this->googleAuthenticator->getQRContent($user);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
