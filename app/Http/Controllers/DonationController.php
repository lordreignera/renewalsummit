<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\SwappPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(protected SwappPaymentService $swapp) {}

    public function show(): View
    {
        return view('donate');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'donor_name'     => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'amount'         => 'required|integer|min:5000',
            'payment_method' => 'required|in:mobile_money,visa',
            'network'        => 'required_if:payment_method,mobile_money|nullable|in:MTN,AIRTEL',
            'message'        => 'nullable|string|max:500',
        ]);

        $donation = Donation::create([
            'donor_name'     => $data['donor_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'] ?? null,
            'amount'         => $data['amount'],
            'currency'       => 'UGX',
            'payment_method' => $data['payment_method'],
            'network'        => $data['network'] ?? null,
            'message'        => $data['message'] ?? null,
            'status'         => 'pending',
        ]);

        $result = $this->swapp->initiateDonationMobileMoney([
            'donor_name' => $data['donor_name'],
            'phone'      => $data['phone'],
            'amount'     => $data['amount'],
            'network'    => $data['network'] ?? 'MTN',
        ]);

        if (isset($result['data']['transaction_id'])) {
            $donation->update(['swapp_transaction_id' => $result['data']['transaction_id']]);
        }

        return redirect()->route('home')->with(
            'success',
            'Thank you ' . $data['donor_name'] . '! Your donation request of UGX ' . number_format($data['amount']) . ' has been sent to your phone. Please approve the prompt.'
        );
    }
}
