<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/todo/add", name="todo_add")
     */
    public function addAction(Request $request)
    {
        $todo = new Todo();
    	$form = $this->createForm(TodoType::class, $todo);
    
        $form->add('submit', SubmitType::class, [
            'label' => 'Create Todo'
        ]);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
        	$todo->setName($form['name']->getData());
        	$todo->setDescription($form['description']->getData());
        	$todo->setCategory($form['category']->getData());
        	$todo->setPriority($form['priority']->getData());
        	$todo->setDueDate($form['dueDate']->getData());
        	$todo->setCompleted(0);
        	
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($todo);
        	$em->flush();
        	
        	$this->addFlash('notice', 'Todo Added Succesfully!');
        	
        	return $this->redirectToRoute('todo_home');
        }
		
		return $this->render('todo/add.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/todo", name="todo_home")
     */
    public function listAction(Request $request)
    {
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
        
        return $this->render('todo/index.html.twig', ['todos' => $todos]);
    }
    
    /**
     * @Route("/todo/view/{id}", name="todo_view")
     */
    public function viewAction($id, Request $request)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        
        return $this->render('todo/view.html.twig', ['todo' => $todo]);
    }
    
    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $form = $this->createForm(TodoType::class, $todo);
    
        $form->add('submit', SubmitType::class, [
            'label' => 'Save Todo'
        ]);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
        	$todo->setName($form['name']->getData());
        	$todo->setDescription($form['description']->getData());
        	$todo->setCategory($form['category']->getData());
        	$todo->setPriority($form['priority']->getData());
        	$todo->setDueDate($form['dueDate']->getData());
        	$todo->setCompleted(0);
        	
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($todo);
        	$em->flush();
        	
        	$this->addFlash('nmfootice', 'Todo Saved Succesfully!');
        	
        	return $this->redirectToRoute('todo_home');
        }
        
        return $this->render('todo/edit.html.twig', ['todo' => $todo, 'form' => $form->createView()]);
    }
    
    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);
        $em->flush();
        
        $this->addFlash('notice', 'Todo Deleted Succesfully!');
       	return $this->redirectToRoute('todo_home');
    }
}