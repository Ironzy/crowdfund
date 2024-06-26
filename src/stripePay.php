<?php

namespace App;

//use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StripePay
{
    private $stripe;
    private $stripeKey;

    public function __construct(
        //#[Autowire('%env(STRIPE_KEY)%')] string $stripeKey,
    ){

        $this->stripeKey = $_ENV['STRIPE_KEY'];
        $this->stripe = new \Stripe\StripeClient($this->stripeKey);
        //$stripeKey;
    }

    public function stripe_create_price(int $price, string $campaignTitle){

        //create product
        //$stripe->products->create(['name' => $campaign_title]);
        $price = $this->stripe->prices->create([
            'currency' => 'eur',
            'unit_amount' => $price,
            'product_data' => ['name' => $campaignTitle],
        ]);

        return $price;
    }

    public function stripe_pay($price, $campaignTitle, $sessionId)
    {
        //create price
        $price = $this->stripe_create_price($price, $campaignTitle);
        \Stripe\Stripe::setApiKey($this->stripeKey);

        //create checkout session
        $myUrl = $_ENV['DOMAIN'];
        $checkoutSession = \Stripe\Checkout\Session::create([
            'line_items' => [[
              'price' => $price->id,
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $myUrl . "payment?payment=$sessionId",
            'cancel_url' => $myUrl . "payment?campagne=$campaignTitle&payment=$sessionId&status=canceled",
        ]);

          
        return $checkoutSession->url;
    }
}