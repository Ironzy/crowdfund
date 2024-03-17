<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Entity\Payment;
use App\Form\ParticipantType;
use App\Form\PaymentType;
use App\Repository\CampaignRepository;
use App\Repository\PaymentRepository;
use App\stripePay;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Regex;

//require_once('vendor/autoload.php');

class PaymentController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ){   
    }

    /** get campaign details 
     * create a Payment form 
     * Add participant
     * Add payment
     * Redirect to stripe checkout pay
     */
    #[Route('/payment/{slug}', name: 'app_payment')]
    public function index(Request $request, $slug, CampaignRepository $campaignRepository, stripePay $stripe): Response
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

            //set user payment session
            $today = new \DateTimeImmutable();
            $today = $today->format("H:i:s.v");

            $session = new Session();

            $sessionId = $session->getId();
            $sessionId = $sessionId.$today;
            $session->set('sessionId', $sessionId);
            
            // Create Stripe Price
            $price = $form['amount']->getData() * 100;
            $campaignTitle = $campaign->getTitle();

            //create checkouturl
            $checkoutUrl = $stripe->stripe_pay($price, $campaignTitle, $sessionId);
            //redirect to checkout

            //if checkout url generation is successfull, persist to the database
            //create a participant and add payment for the participant
            $campaign->addParticipant($payment->getParticipant());
            $this->entityManager->persist($campaign);

            $payment
                ->setSessionId($sessionId)
                ->setPaymentStatus('not paid')
            ;
            $this->entityManager->persist($payment);
            $this->entityManager->flush();

            return $this->redirect($checkoutUrl, 303);

        }

        return $this->render('payment/index.html.twig', [
            'payment_form' => $form,
            'name' => $campaign->getName(),
            'title' => $campaign->getTitle(),
        ]);
    }

    /** returned after payment on stripe */
    #[Route('/payment', name: 'app_payment_success')]
    public function payment(Request $request, Payment $payment){

        if($status = $request->query->get('status')){

            $status = 'Failed';
            //message to user
            dd($status);
        }else{

            if($sessionId = $request->query->get('payment')){

                //update status in Payment

                dd($sessionId);
            }
        }
         
        
    }
}
