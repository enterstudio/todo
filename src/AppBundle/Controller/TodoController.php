<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo_home")
     */
    public function indexAction(Request $request)
    {
		$customService = $this->get('custom_service');
		
		$session = $this->get('session');
		$sessionId = $session->getId();
		
		$isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			$filter = $request->get('filter');
			
			$todos = $this->getFilteredTodos($sessionId, $filter);
			$session->set('todos', $todos);
			
			return new JsonResponse([
				'todos' => $todos
			]);
		}
		
		$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
		
		$session->set('todos', $todos);
		
        return $this->render('todo/index.html.twig', [
        	'todos' => $todos,
        	'text' => $customService->getStatus()
        ]);
    }
    
    /**
     * @Route("/todo/add", name="todo_add")
     */
    public function addAction(Request $request)
    {
		$session = $this->get('session');
		$sessionId = $session->getId();
		$todo_desc = $request->get('todo');
		
		$todo = new Todo();
		
		$isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			$todo->setDescription($todo_desc);
			$todo->setCompleted(0);
			$todo->setSessionId($sessionId);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			
			return new JsonResponse([
				'todo' => $todo_desc,
				'todo_id' => $todo->getId()
			]);
		}
	    
	    $form = $this->createForm(TodoType::class, $todo);
		$form->add('submit', SubmitType::class, [
            'label' => 'Create Todo'
        ]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
        	$todo->setDescription($form['description']->getData());
        	$todo->setCompleted(0);
        	$todo->setSessionId($sessionId);
        	
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($todo);
        	$em->flush();
        	
        	$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
        	$session->set('todos', $todos);
        	
        	$this->addFlash('notice', 'Todo Added Succesfully!');
        	
        	return $this->redirectToRoute('todo_home');
        }
		return $this->render('todo/add.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
		$session = $this->get('session');
		$sessionId = $session->getId();
		
		$todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        
       	$isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			$todo_desc = $request->get('todo');
			$todo->setDescription($todo_desc);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			
			return new JsonResponse([
				'status' => 'success'
			]);
		}
        
        $form = $this->createForm(TodoType::class, $todo);
        
        $form->add('submit', SubmitType::class, [
            'label' => 'Save Todo'
        ]);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
			$todo->setDescription($form['name']->getData());
			$todo->setCompleted(0);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			
			$this->addFlash('notice', 'Todo Saved Succesfully!');
			
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
			$session->set('todos', $todos);
			
			return $this->redirectToRoute('todo_home', ['todos', $todos]);
        }
        
        return $this->render('todo/edit.html.twig', ['todo' => $todo, 'form' => $form->createView()]);
    }
    
    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $session = $this->get('session');
		$sessionId = $session->getId();
        
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);
        $em->flush();
        
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
        $session->set('todos', $todos);
        
        $isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			return new JsonResponse([
				'status' => 'success'
			]);
		}
		
		$this->addFlash('notice', 'Todo ' . $id . ' Deleted Succesfully!');
       	return $this->redirectToRoute('todo_home', ['todos' => $todos]);
    }
    
    /**
     * @Route("/todo/complete/{id}", name="todo_complete")
     */
    public function completeAction($id, Request $request)
    {
        $session = $this->get('session');
		$sessionId = $session->getId();
        $status = $request->get('status');
        
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $todo->setCompleted($status);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();
        
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
        $session->set('todos', $todos);
        
        $isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			return new JsonResponse([
				'status' => 'success'
			]);
		}
        
        $this->addFlash('notice', 'Todo ' . $id . ' Status changed Succesfully!');
       	return $this->redirectToRoute('todo_home', ['todos' => $todos]);
    }

	/**
     * 
     */
    private function getFilteredTodos($sid, $filter = 'all')
    {
		switch ($filter) {
			case 'all':
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sid);
				break;
			case 'completed':
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findCompleted($sid);
				break;
			case 'active':
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findActive($sid);
				break;
			case 'clear_all':
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->clearAll($sid);
				break;
			case 'clear_completed':
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->clearCompleted($sid);
				break;
			default:
				$ftodos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sid);
		}
		
		$todo_count = count($ftodos);
		$todos = [];
		
		if(isset($ftodos)) {
			foreach ($ftodos as $todo) {
				$todos[] = [
					'todo_count' => $todo_count,
					'todo_id' => $todo->getId(),
					'todo_desc' => $todo->getDescription(),
					'todo_status' => $todo->getCompleted(),
				];
			}
		} else {
			$todos = [];
		}
			
		return $todos;
    }
    
    /**
	 * @Route("/todo/cookie/{value}", name="cookie_write")
	 */
	public function cookieAction($value = 'ABCDEFG', Request $request)
	{
		if ($value == 'read') {
			$cookies = $request->cookies->all();
			$cookie_val = $cookies["varName"];
		} else {
			$cookie_val = $value;
			$response = new Response();          
			$response->headers->setCookie(new Cookie('varName', $value, time() + (3600 * 48)));
			$response->sendHeaders();
		}
		
		return $this->render('todo/cookie.html.twig', [
        	'action' => $value,
        	'val' => $cookie_val
        ]);
	}
}
