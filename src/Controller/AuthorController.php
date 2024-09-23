<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    private $authors;
    public function __construct(){
        $this->authors=[
            array('id' => 1, 'picture' => 'pictures/victor-hugo.jpg','username' => 'Victor Hugo', 'email' =>
            'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => 'pictures/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
            ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => 'pictures/TahaHussein.jpg','username' => 'Taha Hussein', 'email' =>
            'taha.hussein@gmail.com', 'nb_books' => 300),
        ];
    }

    private function getAuthorById(int $id)
    {
        foreach ($this->authors as $author) {
            if ($author['id'] === $id) {
                return $author;
            }
        }
        return null;
    }

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route("/author/{name}",
       name:"app_author",
       methods:["GET"],
       defaults:["name"=>"taha hussain"])]
    public function showAuthor($name){
        return $this->render('author/show.html.twig',
        array(
            'name'=>$name
        ));
    }

    #[Route("/authors", name:"show.authors")]
    public function listAuthors (): Response
    {

        return $this->render('author/show.html.twig', [
            'authors'=>$this->authors
        ]);
    }


    #[Route("/showDetails/{id}",name:"app_showDetail",methods:["GET"])]
    public function showDetailsAction($id){

            // var_dump($id);
            // die();
            $author = $this->getAuthorById($id);
            // var_dump($author);
            // die();

            return $this->render("author/showDetails.html.twig", array(
                'author'=>$author
            ));
    }
}
