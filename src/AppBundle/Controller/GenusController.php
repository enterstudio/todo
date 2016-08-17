<?php

namespace AppBundle\Controller;

use AppBundle\Form\GenusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

Class GenusController extends Controller
{
    /**
     * @Route("/genus", name="genus_home")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(GenusType::class);
    	$form->add('submit', SubmitType::class);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
        	$genusName = $form['name']->getData();
        
        	return $this->redirectToRoute('genus_view', ['genusName' => $genusName]);
        }
		
		return $this->render('genus/index.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/genus/{genusName}", name="genus_view")
     */
    public function showAction($genusName)
    {
        return $this->render('genus/show.html.twig', [
            'name' => $genusName,
        ]);
    }

    /**
     * @Route("/genus/{genusName}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction()
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];

        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
