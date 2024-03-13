<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use Egulias\EmailValidator\Parser\CommentStrategy\CommentStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampaignController extends AbstractController
{
    
    #[Route('/campaign', name: 'app_campaign')]
    public function campaign(): Response
    {
        return $this->render('campaign/index.html.twig', [
            'controller_name' => 'CampaignController',
        ]);
    }
    
    #[Route('/show', name: 'app_show')]
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

        //handle requests

        return $this->render('campaign/create.html.twig', [
            'create_form' => $form,
        ]);


    }
}
