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

#[Route('/enable_2fa', name: 'app_2fa_form')]
public function setup2FA(Request $request): Response
{
/** @var User $user */
$user = $this->getUser();

if (!$user->getGoogleAuthenticatorSecret()) {
// Générer le secret si non existant
$secret = $this->googleAuthenticator->generateSecret();
$user->setGoogleAuthenticatorSecret($secret);
$this->entityManager->persist($user);
$this->entityManager->flush();
}

if ($request->isMethod('POST')) {
$code = $request->request->get('auth_code');
if ($this->googleAuthenticator->checkCode($user, $code)) {
// Si le code est correct, activer 2FA
$user->setTwoFactorEnabled(true);
$this->entityManager->persist($user);
$this->entityManager->flush();
$this->addFlash('success', '2FA activée avec succès!');
return $this->redirectToRoute('app_profile');
} else {
// Code incorrect
$this->addFlash('error', 'Le code est incorrect, veuillez réessayer.');
}
}

// Générer le QR code
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

// Encoder le QR code en base64 pour l'inclure directement dans le template
$qrCodeBase64 = base64_encode($result->getString());

return $this->render('security/2fa_form.html.twig', [
'qrCodeBase64' => $qrCodeBase64, // Passer l'image encodée en base64
]);
}
    #[Route('/2fa_check', name: '2fa_login_check')]
    public function check2FA(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirige si non connecté
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('auth_code');

            if ($this->googleAuthenticator->checkCode($user, $code)) {
                $this->addFlash('success', 'Authentification réussie!');
                return $this->redirectToRoute('app_profile'); // Redirige vers le profil
            } else {
                $this->addFlash('error', 'Le code est incorrect, veuillez réessayer.');
            }
        }

        return $this->render('security/2fa_setup.html.twig'); // Assurez-vous que le bon template est rendu
    }

}