<?php

namespace App\Http\Controllers;

use App\Helpers\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
        private string $baseUrl;
    private array $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = config('app.url') . '/api/v1';
        $this->defaultHeaders = [
            'Accept' => 'application/json'


        ];
    }

    public function getStripeConfig()
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)

                ->get($this->baseUrl . '/stripe/config');

            if ($response->successful()) {
                return $response->json();
            }



             return response()->json(['error' => 'Failed to retrieve Stripe configuration'], 500);

        } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
        }
    }


       public function create(Request $request)
    {

         $loan_id = IdGenerator::generateLoanId();


        $borower_id = IdGenerator::generateBorrowerId();

        return view('payments.create',compact('borower_id','loan_id'));
    }

    function add(Request $request) {
             try {


        } catch (RequestException $e) {
            return redirect()->route('payments.create')->with('error',$e->getMessage());

        }

    }
}
