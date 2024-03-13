<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Entity\Payment;
use App\Form\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ){   
    }

    #[Route('/payment/{slug}', name: 'app_payment')]
    public function index($slug): Response
    {
        $campaign = new Campaign();
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy(['title' => $slug]);
        if(!$campaign){
            throw $this->createNotFoundException(
                'No campagne found for Campagne Name : '.$slug
            );
        }

        /** build form **/
        $participant = new Participant();
        $form = $this->createForm(Participant::class, $participant);

        /** handle form submission **/
    

        return $this->render('payment/index.html.twig', [
            'payment_form' => $form,
            'name' => $campaign->getName(),
            'title' => $campaign->getTitle(),
        ]);
    }
}
