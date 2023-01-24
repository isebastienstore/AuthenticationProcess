<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use function PHPUnit\Framework\equalTo;

class SecurityController extends AbstractController
{

    public function __construct(private ManagerRegistry $managerRegistry){}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'isConnected'=>true]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/forget', name: 'user.forget')]
    public function changeLoginAndpassword(Request $request){
        $user = new User();
        $mailSend = false;
        $form = $this->createForm(UserType::class, $user);
        $form->remove("password");
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $doctrine = $this->managerRegistry->getRepository(User::class);
            $user_database = $doctrine->findAll();

            for ($i = 0; $i< count($user_database); $i++){
                if ($user_database[$i]->getUserIdentifier() == $user->getUserIdentifier()){
                    $message = 'Un email a été envoyé à votre adreese; veillez vous rendre dans la boite de reception pour créer de nouveaux identifiants !';
                    self::sendMail('echo "Cliquez sur le lien pour changer vos informations d\'identifications : http://localhost:8000/updateLoginAndPassword/'.$user_database[$i]->getId().'" | mail -s "Informations d\'identification" '.$user->getUserIdentifier());
                    $mailSend = true;
                }
            }
            if (!$mailSend)
                $message = "Il n'y a pas de compte avec cette addresse";

            return $this->render('user/changeLoginAndPassword.html.twig', ['message' => $message]);
        }
        else{
            return $this->render('user/changeLoginAndPassword.html.twig', ['form'=>$form->createView()]);
        }
    }

    /**Utilisation d'un serveur SMPT (postfix) configuré en localhost et executable sur le shell pour envoyé un mail vers tout les domaines
     * @param $mail : ligne de commande exécutée sur le terminal
     * @return void
     */
    public static function sendMail($mail){
        exec($mail);
    }
}
