<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/todo')]
class ToDoController extends AbstractController
{
    // afficher le tableau
    
    #[Route('', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        if (!$session->has('todos')) {
            $session->set('todos', [ // tableau associatif key => value
                "achat" => "keyboard ⌨️",
                "rdv" => "dojdoj 🐸",
                "cours" => "php 🐘",
                "mekla" => "spaghetti 🍝"
            ]);
            $this->addFlash("info", "La liste des taches a été initialisée");
        }
        else {
            $todos = $session->get('todos');
            // $this->addFlash("info", "La liste des taches a été initialisée");
        }
        return $this->render('to_do/index.html.twig');
    }

    #[Route('/add', name: 'todo_add')]
    public function addToDo(Request $request): Response
    {
        if ($request->isMethod('Post')) {
            $name = $request->request->get('name');
            $content = $request->request->get('content');
            $session = $request->getSession();
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash("error", "La tache $name existe déjà");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash("success", "La tache $name a été ajoutée");
            }
        }
        return $this->render('to_do/add.html.twig');
    }

    #[Route('/update/{name}/{content}', name: 'todo_update')]
    public function updateToDo(Request $request, $name, $content): Response{

        $session = $request->getSession();
        $todos = $session->get('todos');

        if($request->isMethod('Post')){
            $oldName = $request->request->get('old_name');
            $newName = $request->request->get('new_name');
            $content = $request->request->get('content');
            if (isset($todos[$oldName])) {
                
                if(($oldName == $newName) && ($todos[$oldName] == $content)){
                    $this->addFlash("info", "Aucune modification n'a été apportée"); 
                } else {
                    unset($todos[$oldName]);
                    $todos[$newName] = $content;
                    $session->set('todos', $todos);
                    $this->addFlash("success", "La tache $name a été modifiée");
                }
                
            }
            else {
                $this->addFlash("error", "La tache $name n'existe pas");
            }
        }
        
        
        
        return $this->render('to_do/update.html.twig', [
            'name' => $name,
            'content' => $content
        ]);
    }


    #[Route('/delete/{name}/{content}', name: 'todo_delete')]
    public function deleteToDo(Request $request, $name, $content): Response{

        $session = $request->getSession();
        $todos = $session->get('todos');

        if(isset($todos[$name])){
            unset($todos[$name]);
            $session->set('todos', $todos);
            $this->addFlash("success", "La tache $name avec le contenu $content a été supprimée");
        }
        else {
            $this->addFlash("error", "La tache $name n'existe pas");
        }
        
        
        
        return $this->render('to_do/index.html.twig');
    }


    #[Route('/reset', name: 'todo_reset')]
    public function reset(Request $request): Response
    {
        $session = $request->getSession();

        if ($session->has('todos')) {
            $session->set('todos', [ // tableau associatif key => value
                "achat" => "keyboard ⌨️",
                "rdv" => "dojdoj 🐸",
                "cours" => "php 🐘",
                "mekla" => "spaghetti 🍝"
            ]);
            $this->addFlash("info", "La liste des taches a été initialisée");
        }
        
        return $this->render('to_do/index.html.twig');
    }
}
