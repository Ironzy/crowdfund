<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Entity\Payment;
use App\Form\ParticipantType;
use App\Form\PaymentType;
use App\Repository\CampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Regex;

//require_once('vendor/autoload.php');

class PaymentController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ){   
    }

    #[Route('/payment/{slug}', name: 'app_payment')]
    public function index(Request $request, $slug, CampaignRepository $campaignRepository): Response
    {
        // get data about the campaign being paid for
        $campaign = $campaignRepository->findOneBy(['title' => $slug]);
        if(!$campaign){
            throw $this->createNotFoundException(
                'No campagne found for Campagne Name : '.$slug
            );
        }

        /** build form for payment **/
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        /** handle form request */
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // Stripe


            //create a participant and add payment for the participant
            $campaign->addParticipant($payment->getParticipant());
            $this->entityManager->persist($campaign);
            $this->entityManager->persist($payment);

            //flush
            $this->entityManager->flush();
        }

        //$payment->setParticipant($participant);
        //$participa
    

        return $this->render('payment/index.html.twig', [
            'payment_form' => $form,
            'name' => $campaign->getName(),
            'title' => $campaign->getTitle(),
        ]);
    }
}
