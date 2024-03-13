<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Egulias\EmailValidator\Parser\CommentStrategy\CommentStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampaignController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){   
    }

    #[Route('/campaign', name: 'app_campaign')]
    public function campaign(): Response
    {
        return $this->render('campaign/index.html.twig', [
            'controller_name' => 'CampaignController',
        ]);
    }
    
    #[Route('/show/{slug}', name: 'app_show')]
    public function show(): Response
    {
        return $this->render('campaign/show.html.twig', [
            'controller_name' => 'CampaignController',
        ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request, CampaignRepository $campaignRepository): Response
    {
        
        //build form
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);

        //handle form submission
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($campaign);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_show', ['slug'=> $campaign->getName()]);
        }

        return $this->render('campaign/create.html.twig', [
            'create_form' => $form,
        ]);


    }
}
