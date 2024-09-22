<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'app_first')] //attribut
    public function index(): Response
    {
        // die('Je suis la requete /first'); // stop the execution of the method
        return $this->render('first/index.html.twig', [
            'name' => 'Nour',
            'firstName' => 'Bnm'
        ]);
    }

    #[Route('/hello/{name}/{firstname}', name: 'hello')] //attribut
    public function hello( $name, $firstname, Request $request ): Response
    {
        dd($request);
        $rand = rand(0, 100);
        // echo $rand;
        if ($rand % 2 == 0) {
            return $this->forward('App\\Controller\\FirstController::index');
        }
        return $this->render('first/hello.html.twig', [
            'name' => $name,
            'firstName' => $firstname
        ]);
    }
}
