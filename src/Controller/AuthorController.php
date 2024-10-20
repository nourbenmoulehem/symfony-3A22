<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends AbstractController
{
    private $authors;
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
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
    public function listAuthors (AuthorRepository $authorRepository): Response
    {
        //$authorsdb = $this->doctrine->getRepository(Author::class)->findAll();
        $authorsdb = $authorRepository->findAll();
        return $this->render('author/showFromDB.html.twig', [
            'authorsdb'=>$authorsdb
        ]);
    }

    #[Route("/authors/{id}", name:"search.authors")]
    public function searchAuthor (AuthorRepository $authorRepository, $id): Response
    {
        //$authorsdb = $this->doctrine->getRepository(Author::class)->findAll();
        $author = $authorRepository->find($id);
        return $this->render("author/showDetails.html.twig", array(
            'author'=>$author
        ));
    }

    #[Route("/authors/add", name:"author.add")]
    public function addAuthor(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        if($request->isMethod('POST')){
            

            $name = $request->request->get('name');
            $email = $request->request->get('email');

            $author = new Author();
            //$author->setId(4);
            $author->setUsername($name);
            $author->setEmail($email);

           
            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash("success", "L'autheur $name avec le email $email a été ajoutè");

        }
        

        
        return $this->render('author/ajoutAuthor.html.twig');
    }


    #[Route("/authors/update/{name}/{email}/{id}", name:"author.update")]
    public function updateAuthor(Request $request, $name, $email, $id): Response
    {
        $entityManager = $this->doctrine->getManager();
        if($request->isMethod('POST')){
            $author = $entityManager->getRepository(Author::class)->find($id);

            if (!$author) {
                throw $this->createNotFoundException(
                    'Aucun auteur trouvee avec l\'id ' . $id
                );
            }
            $name = $request->request->get('name');
            $email = $request->request->get('email');

            $author->setUsername($name);
            $author->setEmail($email);

            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash("success", "L'autheur $name avec le email $email a été modifié");

        }
        
        

        
        return $this->render('author/modifier.html.twig',
        array(
            'name'=>$name,
            'email'=>$email,
            'id'=>$id
        ));
    }

    #[Route("/authors/delete/{id}", name:"author.delete")]
    public function deleteAuthor(Request $request, $id): Response
    {
        $entityManager = $this->doctrine->getManager();
        if($request){
            $author = $entityManager->getRepository(Author::class)->find($id);

            if (!$author) {
                throw $this->createNotFoundException(
                    'Aucun auteur trouvee avec l\'id ' . $id
                );
            }
           

            $entityManager->remove($author);
            $entityManager->flush();

            $this->addFlash("success", "L'autheur $id été supprimé");

        }
        
        
        return $this->forward('App\\Controller\\AuthorController::listAuthors');
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
