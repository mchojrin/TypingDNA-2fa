<?php

namespace App\Controller;

use App\TwoFactorAuthentication\TypingDNAWrapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/verify/", name="app_verify_otp")
     */
    public function verifyOTP(Request $request, TypingDNAWrapper $typingDNA) : Response
    {
        $error = null;

        $user = $this->getUser();
        $phoneNumber = $user->getPhoneNumber();

        if ($otp = $request->get('otp')) {
            if ($typingDNA->isValidOTP($phoneNumber, $otp)) {
                $user
                    ->setRoles(array_merge($user->getRoles(), ['TWO_FACTOR_PASSED']));

                return $this->redirectToRoute('dashboard');
            } else {

                return $this->redirectToRoute('2fa-failed');
            }
        }

        return $this->render(
            'security/verify.html.twig',
            [
                'error' => $error,
                'typingdna' => $typingDNA->getDataAttributes($phoneNumber)
            ]
        );
    }

    /**
     * @return Response
     * @Route (path="/2fa_failed", name="2fa-failed")
     */
    public function TwoFAFailed() : Response
    {
        return $this->render(
            'security/2fa_failed.html.twig'
        );
    }
}
