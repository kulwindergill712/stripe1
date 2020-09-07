<?php

namespace App\Http\Controllers;

use App\custtrans;
use App\Payment;
use App\Traits\reply;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentController extends Controller
{
    use reply;
    public function emph(Request $request)
    {
        $user = $request->user_id;
        $check_cust_exist = payment::where('user_id', $user)->first();
        if (!$check_cust_exist) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer = \Stripe\Customer::create([
                'name' => 'kulwinder singh',
                'email' => 'kulwindergill712@gmail.com',
                'address' => [
                    'line1' => 'mohali',
                    'postal_code' => '148033',
                    'city' => 'Swd',
                    'state' => 'pujab',
                    'country' => 'india',
                ],
            ]);
            $data['customer'] = $customer->id;
            $data['user_id'] = $user;
            payment::create($data);

            $resul = payment::where('user_id', $user)->first();

            \Stripe\Stripe::setApiKey("sk_test_51HATXKEpm2bORyJFSs36tzo8mqxiNVTLlUs3cNhP5txWId1oV7CN4Ev8PF2JfGHJ49A1kMMhOpaCFuXx4NmwwZcJ00Q24qxY2o");
            \Stripe\Stripe::setApiVersion("2020-03-02");
            $key = \Stripe\EphemeralKey::create(
                ['customer' => $resul->customer],
                ['stripe_version' => '2020-03-02']
            );
            return $this->s('empherical key generated successfully', $key);
        }
        $check = payment::where('user_id', $user)->first();
        \Stripe\Stripe::setApiKey("sk_test_51HATXKEpm2bORyJFSs36tzo8mqxiNVTLlUs3cNhP5txWId1oV7CN4Ev8PF2JfGHJ49A1kMMhOpaCFuXx4NmwwZcJ00Q24qxY2o");
        \Stripe\Stripe::setApiVersion("2020-03-02");
        return $key = \Stripe\EphemeralKey::create(
            ['customer' => $check->customer],
            ['stripe_version' => '2020-03-02']
        );

    }

    public function custumer(Request $request)
    {
        $user_id = $request->user_id;
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $resul = payment::where('user_id', $user_id)->first();
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HATXKEpm2bORyJFSs36tzo8mqxiNVTLlUs3cNhP5txWId1oV7CN4Ev8PF2JfGHJ49A1kMMhOpaCFuXx4NmwwZcJ00Q24qxY2o'
        );
        $stripe->paymentMethods->attach(
            $request->stripeToken,
            ['customer' => $resul->customer]
        );

        $data = Charge::create([
            "customer" => $resul->customer,
            "amount" => $request->amount * 100,
            "currency" => "inr",
            "description" => "payment for testing",
        ]);

        $result['customer'] = $data->customer;
        $result['amount'] = $data->amount;
        $result['balance_transaction'] = $data->balance_transaction;
        $result['payment_method'] = $data->payment_method;
        custtrans::create($result);
        return $this->s('payment done successfully', "");

    }

    public function getalltran(Request $request)
    {
        $user_id = $request->user_id;
        $data = payment::where('user_id', $user_id)->first('customer');
        return custtrans::where('customer', $data->customer)->get(['amount', 'balance_transaction', 'payment_method']);

    }

    public function stripe(Request $request)
    {

        $stripe = new \Stripe\StripeClient(
            'sk_test_51HATXKEpm2bORyJFSs36tzo8mqxiNVTLlUs3cNhP5txWId1oV7CN4Ev8PF2JfGHJ49A1kMMhOpaCFuXx4NmwwZcJ00Q24qxY2o'
        );
        return $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $request->number,
                'exp_month' => $request->exp_month,
                'exp_year' => $request->exp_year,
                'cvc' => $request->cvc,
            ],
        ]);
    }

}
