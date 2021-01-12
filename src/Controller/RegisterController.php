<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    //METHOD2
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder  ): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() &&$form->isValid() ){

            $user  = $form->getData();

            $password = $encoder->encodePassword($user,$user->getPassword());

            $user->setpassword($password);

            //dd($password); //PERMET D EDEBUGGER lA VARIABLE $PASSWORD

           // dd($user); //PERMET DE VERIFIER SI L'ENTREE DES DONNES FONICTIONNENT POUR LA VARIABLE $USER

            //ENREGISTRER LES INFOS DANS LA BDD GRACE A DOCTRINE
                //METHODE1
//                $doctrine = $this->getDoctrine()->getManager();
                //METHODE2 CONSTRUCTOR
//                $doctrine->persist($user);
//                $doctrine->flush();
                $this->entityManager->persist($user);
                $this->entityManager->flush();
        }

        return $this->render('register/index.html.twig',[
        'form' => $form->createView()
        ]);
    }
}
