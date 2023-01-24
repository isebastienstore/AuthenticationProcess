<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $manager,
        private SluggerInterface $slugger
    ){}

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/list', name: 'user.list.alls')]
    public function getUsers(){
        $repository = $this->manager->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('user/index.html.twig', ['users'=>$users]);
    }


    #[Route('/updateLoginAndPassword/{id?0}')]
    public function updateLoginAndPassword(Request $request, User $user = null){
        $in_database = false;

        if ($user==null)
           $user = new User();
           $user2 = new User();


        $form = $this->createForm(UserType::class, $user2);
       $form->remove('email');

       // $user2->setEmail($user->getUserIdentifier());
        $form->handleRequest($request);

        if ($form->isSubmitted()){
           // dd($user2);
            $user->setPassword($user2->getPassword());
            $manager = $this->manager->getManager();
            $manager->persist($user);
            $manager->flush();

            $message = 'les identifiants ont été mis à jour avec succès a été mis à jour avec succès !';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user.list.alls');
        }
        else{
                return $this->render('user/setPassword.html.twig', ['form'=>$form->createView()]);
        }
    }
    /*
    #[Route('/add', name: 'user.add')]
    public function addUser(Request $request){
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $manager = $this->managerRegistry->getManager();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'utilisateur ajouté avec succès!');
            return $this->redirectToRoute('user.list.alls');
        }
        else {
            return $this->render('user/add_user.html.twig', ['form' => $form->createView()]);
        }
    }

    #[Route('/edit/{id?0}', name: 'user.edit')]
    public function editUser(User $user = null, Request $request){
        $new = false;
        if (!$user){
            $new = true;
            $user = new User();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $manager = $this->managerRegistry->getManager();
            $manager->persist($user);
            $manager->flush();

            if (!$new){
                $message = 'l\'utilisateur a été mis à jour avec succès !';
            }
            else{
                $message = 'l\'utilisateur a été ajouté avec succès !';
            }
            $this->addFlash('success', $message);

            return $this->redirectToRoute('user.list.alls');
        }
        else{
            return $this->render('user/add_user.html.twig', ['form' => $form->createView()]);
        }
    }

    #[Route('/delete/{id}', name: 'user.delete')]
    public function deleteUser(User $user = null){
        if ($user) {
            $manager = $this->managerRegistry->getManager();
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'L\'utilisateura été supprimé avec succès !');

            return $this->redirectToRoute('user.list.alls');
        }
        else{
            $this->addFlash('error', 'L\'utilisateur n\'existe pas !' );
            return $this->redirectToRoute('user.list.alls');
        }
    }

    */
}
