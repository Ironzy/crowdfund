<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\CampaignRepository;
use App\Repository\PaymentRepository;
use App\StripePay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

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
    public function payment(Request $request, $slug, CampaignRepository $campaignRepository, StripePay $stripe): Response
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

            //set the sessionId and paymentStatus for Payment Entity
            $payment
                ->setSessionId($sessionId)
                ->setPaymentStatus('not paid')
            ;
            $this->entityManager->persist($payment);
            $this->entityManager->flush();

            return $this->redirect($checkoutUrl, 303);

        }

        $status = '';

        if($paymentStatus = $request->query->get('payment')){

            if($paymentStatus === 'paid'){

                $message = 'Thank you for your generosity !';
            }else if($paymentStatus === 'failed'){

                $message = 'Sorry, your payment was not successful';
            }   
        }

        return $this->render('payment/index.html.twig', [
            'payment_form' => $form,
            'name' => $campaign->getName(),
            'title' => $campaign->getTitle(),
            'paymentStatus' => $message,
        ]);
    }

    /** returned after payment on stripe */
    #[Route('/payment', name: 'app_payment_success')]
    public function payment_stripe(Request $request, PaymentRepository $paymentRepository){

        if($request->query->get('status') && $campaignTitle = $request->query->get('campagne')){

            //message to user
            return $this->redirectToRoute('app_payment', ['slug'=>$campaignTitle, 'payment'=>'failed']);
            
        }else{

            if($sessionId = $request->query->get('payment') && $campaignTitle = $request->query->get('campagne')){

                //update status in Payment
                $payment = $paymentRepository->findOneBy(['sessionId' => $sessionId]);

                if(!$payment){

                    throw $this->createNotFoundException(
                        'Payment not found'
                    );
                }

                $payment
                    ->setPaymentStatus('paid')
                    ->setUpdatedAtValue()
                ;
                
                $this->entityManager->flush();

                return $this->redirectToRoute('app_payment', ['slug'=>$campaignTitle, 'payment'=>'paid']);
               
            }
        }
         
        
    }
}
