<?php

namespace Azuriom\Plugin\TON;

use Azuriom\Models\User;
use Azuriom\Plugin\Shop\Cart\Cart;
use Azuriom\Plugin\Shop\Models\Payment;
use Azuriom\Plugin\Shop\Payment\PaymentMethod;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TONMethod extends PaymentMethod
{
    /**
     * The payment method id name.
     *
     * @var string
     */
    protected $id = 'ton';

    /**
     * The payment method display name.
     *
     * @var string
     */
    protected $name = 'TON';

    public function startPayment(Cart $cart, float $amount, string $currency)
    {
        $payment = $this->createPayment($cart, $amount, $currency);
        $payment->update(['status' => 'pending']);

        $address = $this->gateway->data['address'];
        $pay_id = $payment->id;
        $price = $this->gateway->data['price'];
        $tons = (int) (($amount / $price) * 1000000000);

        return redirect()->away("https://app.tonkeeper.com/transfer/{$address}?amount={$tons}&text={$pay_id}");
    }

    public function notification(Request $request, ?string $paymentId)
    {
        return abort(404);
    }
    public function checkPayments()
    {
        $address = $this->gateway->data['address'];

        //prevent large list of payments to check
        Payment::scopes(['Pending', 'withRealMoney'])
            ->with('user')
            ->where('gateway_type','ton')->where('created_at', '<=', Carbon::now()->subSecond(43200)) //12 hours
            ->delete();

        $paymentsCheck = Payment::scopes(['Pending', 'withRealMoney'])
            ->with('user')
            ->where('gateway_type','ton')
            ->latest()
            ->paginate();
        
        if ($paymentsCheck->isEmpty()) {
            return true;
        }

        $url = "https://tonapi.io/v2/blockchain/accounts/{$address}/transactions?limit=100";
        $json = json_decode(file_get_contents($url));
        $price = $this->gateway->data['price'];

        foreach($json->transactions as $transaction) {
            if ($transaction->in_msg != [] and property_exists($transaction->in_msg,'decoded_body') and $transaction->success) {
                $payment = Payment::find($transaction->in_msg->decoded_body->text);
                if ($payment != null) {
                    $tons = (int) (($payment->price / $price) * 1000000000);
                    if (TONMethod::existsInArray($payment, $paymentsCheck) and ($transaction->in_msg->value == $tons)) {
                        $hash = $transaction->hash;
                        return $this->processPayment($payment, $hash);
                    }
                }
            }
        }

        return true;

    }

   public function success(Request $request)
    {
        return redirect()->route('shop.home')->with('success', trans('messages.status.success'));
    }

    public function view()
    {
        return 'ton::admin.ton';
    }

    public function rules()
    {
        return [
            'address' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'color' => ['required', 'int'],
        ];
    }

    public function image()
    {
        if(!isset($this->gateway->data['color'])) {
            return asset('plugins/ton/img/ton_logo_dark_background.svg');
        }

        if($this->gateway->data['color'] == 1){
            return asset('plugins/ton/img/ton_logo_light_background.svg');
        } else {
            return asset('plugins/ton/img/ton_logo_dark_background.svg');
        }
    }

    public function existsInArray($entry, $array) {
        foreach ($array as $compare) {
            if ($compare->id == $entry->id) {
                return true;
            }
        return false;
        }
    }

}
