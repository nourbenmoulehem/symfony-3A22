<?php

namespace App\Controller;

use Symfony\Component\Console\Output\ConsoleOutput;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\AuthorRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

#[Route('/livre')]
final class LivreController extends AbstractController
{
    #[Route("/lists", name: 'livres.list')]
    public function listeLivre(Request $request, LivreRepository $livreRepository, LoggerInterface $logger): Response
    {
        $livres = [];

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            if ($title) {
                $logger->info("HELLOOOOO");
                $logger->info("TITLE: " . $title);

                $livres = $livreRepository->findBy(['title' => $title]);

                if (!empty($livres)) {
                    $logger->info("LIVRES FOUND:");
                    foreach ($livres as $index => $livre) {
                        $logger->info("LIVRE " . $index . " : " . $livre->getTitle());
                    }
                } else {
                    $this->addFlash('error', 'Book not found');
                    $logger->info("NO LIVRES FOUND");
                }
                return $this->redirectToRoute('livres.list', ['title' => $title]);
            }
        }

        $title = $request->query->get('title');
        if ($title) {
            $livres = $livreRepository->findBy(['title' => $title]);
        } else {
            $livres = $livreRepository->findAll();
        }
        return $this->render('livre/listeLivre.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/ajouter', name: 'livre.ajouter', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($livre);
            $em->flush();

            return $this->redirectToRoute('livres.list');
        }

        return $this->render('livre/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /*#[Route('/ajouter', name: 'livre.ajouter', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, AuthorRepository $authorRepository): Response
    {
        $em = $doctrine->getManager();
        if ($request->isMethod('POST')) {
            $ref = $request->request->get('ref');
            $title = $request->request->get('title');
            $authorName = $request->request->get('author');
            $nbrPages = $request->request->get('nbrPages');
            $picture = $request->request->get('picture');

            $logger->info("HELLOOOOO");
            $logger->info("AUTHOR NAME FROM FORMULAIRE " . $authorName);

            $author = $authorRepository->findByName($authorName);
            
            
            $livre = new Livre();

            if ($author) {
                $livre->setAuthor($author);
            } else {
                // Handle the case where the author is not found
                $this->addFlash('error', 'Author not found');
                $logger->info('no author found');
                return $this->redirectToRoute('livre.ajouter');
            }
            $livre->setRef($ref);
            $livre->setTitle($title);
            $livre->setNbrPages($nbrPages);
            $livre->setPicture($picture);


            
            $em = $doctrine->getManager();
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Book was added successfully');
            return $this->redirectToRoute('livres.list');
        }

        return $this->render('livre/ajouterLivre.html.twig');
    } */

    #[Route('/modifier/{id}', name: 'livre.modify', methods: ['POST', 'GET'])]
    public function modify(Request $request, ManagerRegistry $doctrine, Livre $livre): Response
    {
        $form = $this->createForm(LivreType::class, $livre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('livres.list');
        }


        return $this->render('livre/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*#[Route('/modifier', name: 'livre.modify', methods: ['POST'])]
    public function modify(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, AuthorRepository $authorRepository): Response
    {
        $em = $doctrine->getManager();
        if ($request->isMethod('POST')) {
            $ref = $request->request->get('ref');
            $title = $request->request->get('title');
            $authorName = $request->request->get('author');
            $nbrPages = $request->request->get('nbrPages');
            $picture = $request->request->get('picture');


            $author = $authorRepository->findByName($authorName);
            
            
            $livre = new Livre();

            if ($author) {
                $livre->setAuthor($author);
            } else {
                // Handle the case where the author is not found
                $this->addFlash('error', 'Author not found');
                $logger->info('no author found');
                return $this->redirectToRoute('livre.ajouter');
            }
            $livre->setRef($ref);
            $livre->setTitle($title);
            $livre->setNbrPages($nbrPages);
            $livre->setPicture($picture);


            
            $em = $doctrine->getManager();
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Book was modified successfully');
            return $this->redirectToRoute('livres.list');
        }

        return $this->render('livre/modifier.html.twig');
    } */



    #[Route('/delete/{id}', name: 'livre.delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, Livre $livre): Response
    {

        if ($livre) {
            $em = $doctrine->getManager();
            $em->remove($livre);
            $em->flush();

            $this->addFlash('success', 'Book was deleted successfully');
            return $this->redirectToRoute('livres.list');
        }


        //return $this->render('livre/edit.html.twig');
    }
}
