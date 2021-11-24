<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Page login 
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        //On va récupérer l'erreur si elle existe 
        $error = $utils->getLastAuthenticationError();

        //On va récupérer l'email mentionné et le ré-injecter afin que l'utilisateur ne le ré-écrit
        $email = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            "error" => $error !== null, "email" => $email
        ]);
    }


    /**
     * Page logout
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout()
    {
        //C'est le composant de Symfony qui s'occuper de la déconnexion (config/packages/security.yaml)
    }



    /**
     * Page d'inscription'
     * @Route("/register", name="account_register")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordHasherInterface $encoder)
    {
        //On va créer un nouvel Objet User()
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On va hacher le MDP
            $hash = $encoder->hashPassword($user, $user->getMdphashed());
            //On va setter le MDP
            $user->setMdphashed($hash);

            //Enregister 
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('Success', "<strong>{$user->getFirstname()}</strong> votre inscription est enregistré sur la BD");

            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/register.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Page de modification de profile
     * @Route("/account/profile", name="account_profile")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("Success", "<strong>{$user->getFirstname()}</strong> votre profil a été modifié avec succès");
        }

        return $this->render("account/profile.html.twig", [
            "form" => $form->createView()
        ]);
    }


    /**
     * Page modification MDP
     * @Route("/account/password_update", name="account_passwordupdate")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    public function passwordUpdate(Request $request, ObjectManager $manager, UserPasswordHasherInterface $encoder)
    {
        //On va créer un nouvel Objet Modification de MDP 
        $passwordUpdate = new PasswordUpdate();

        //On va récupérer l'Utilisateur connecté afin de manipuler son MDP
        $user = $this->getUser();

        //On va créer un formulaire afin de modifier les MDP
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        //Si soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            //On va vérifier le MDP sasie et le MDP de l'User connecté
            if (password_verify($passwordUpdate->getOldPassword(), $user->getMdphashed())) {

                //On va récupérer le nouveau MDP sasie 
                $newPassword = $passwordUpdate->getNewPassword();

                //On va crypter ce nouveau MDP 
                $hash = $encoder->hashPassword($user, $newPassword);

                //On va setter le MPD de l'User connecté
                $user->setMdphashed($hash);

                //Enregister 
                $manager->persist($user);
                $manager->flush();

                //Message de succès
                $this->addFlash("success", "Votre MDP a été modifié avec succès");

                //Rediriger
                return $this->redirectToRoute("homepage");
            } else {

                //Message d'erreur
                $form->get('oldPassword')->addError(new FormError("Le MDP saisie n'est pas votre MDP actuelle"));
            }
        }

        return $this->render("account/passwordupdate.html.twig", ["form" => $form->createView()]);
    }


    /**
     * Page qui affiche le compte de l'user connecté
     * @Route("/account" , name="account_userConnect")
     * @return Response
     */
    public function myAcount()
    {
        $user = $this->getUser();
        return $this->render("user/index.html.twig", ['user' => $user]);
    }



    /**
     * Page qui affiche toutes les réservations
     * @Route("account/reservations", name="account_reservations")
     *
     * @return Response 
     */
    public function reservations()
    {
        return $this->render("account/reservations.html.twig");
    }
}
