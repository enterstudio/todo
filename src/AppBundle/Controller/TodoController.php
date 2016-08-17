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
        	
        	$this->addFlash('notice', 'Todo Saved Succesfully!');
        	
        	return $this->redirectToRoute('todo_home');
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
    public function completeAction($id)
    {
        $session = $this->get('session');
		$sessionId = $session->getId();
        
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        $todo->setCompleted(1);
        
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
     * @Route("/todo/filter/{filter}", name="todo_filter")
     */
    public function filterAction($filter = 'all')
    {
		$customService = $this->get('custom_service');
		
		$session = $this->get('session');
		$sessionId = $session->getId();
		
		switch ($filter) {
		case 'all':
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
			break;
		case 'completed':
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findCompleted($sessionId);
			break;
		case 'active':
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findActive($sessionId);
            break;
        default:
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBySessionId($sessionId);
		}
		
		$todo_count = count($todos);
		
		$isAjax = $this->get('Request')->isXMLHttpRequest();
		if ($isAjax) {         
			$response = [];
			if(isset($todos)) {
				foreach ($todos as $todo) {
					$response[] = [
						'todo_count' => $todo_count,
						'todo_id' => $todo->getId(),
						'todo_desc' => $todo->getDescription(),
						'todo_status' => $todo->getCompleted(),
					];
				}
			} else {
				$response = [];
			}
			
			return new JsonResponse(json_encode($response));
		}
		
		$this->addFlash('notice', 'Todos filter set to: ' . strtoupper($filter) . '!');
		
		return $this->render('todo/index.html.twig', [
			'text' => $customService->getStatus(),
			'count' => $todo_count,
			'todos' => $todos
		]);
    }
    
    /**
	 * @Route("/todo/cookie/{value}", name="cookie_write")
	 */
	public function cookieAction($value = 'ABCDEFG', Request $request)
	{
		if ($value == 'read') {
			$cookies = $request->cookies->all();
			$value = $cookies["varName"];
		} else {
			$response = new Response();          
			$response->headers->setCookie(new Cookie('varName', $value, time() + (3600 * 48)));
			$response->sendHeaders();
		}
		
		return $this->render('todo/cookie.html.twig', [
        	'action' => $value,
        	'val' => $value
        ]);
	}
}
