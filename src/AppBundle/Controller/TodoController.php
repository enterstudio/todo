<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends Controller
{
    /**
     * @Route("/todo/add", name="todo_add")
     */
    public function addAction(Request $request)
    {
        return $this->render('todo/add.html.twig');
    }
    
    /**
     * @Route("/todo", name="todo_list")
     */
    public function listAction(Request $request)
    {
        return $this->render('todo/index.html.twig');
    }
    
    
    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        return $this->render('todo/edit.html.twig');
    }
    
    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id, Request $request)
    {
        return $this->render('todo/delete.html.twig');
    }
}