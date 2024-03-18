<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function show($slug, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->findOneBy(['title' => $slug]);
        if(!$campaign){
            throw $this->createNotFoundException(
                'No campaign found for Campagne Name : '.$slug
            );
        }
        
        $participants = $campaign->getParticipants();
        //dd($participants);

        /** Show the campaign */
        return $this->render('campaign/show.html.twig', [
            'title' => $campaign->getTitle(),
            'name' => $campaign->getName(),
            'content' => $campaign->getContent(),
            'goal' => $campaign->getGoal(),
            'participants' => $campaign->getParticipants(),
        ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request): Response
    {
        $cagName = $request->request->get('cag_name');
        /** build form **/
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->get('title')->setData($cagName);

        /** handle form submission **/
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($campaign);

            /** To Do: check for spam before flushing **/

            $this->entityManager->flush();

            return $this->redirectToRoute('app_show', ['slug'=> $campaign->getName()]);
        }

        return $this->render('campaign/create.html.twig', [
            'create_form' => $form,
            'cag_name' => $cagName,
        ]);


    }
}
