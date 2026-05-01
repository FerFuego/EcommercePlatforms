<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\Setting;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\PreApprovalPlan\PreApprovalPlanClient;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Client\Payment\PaymentClient;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected $planClient;
    protected $preApprovalClient;
    protected $paymentClient;

    public function __construct()
    {
        $accessToken = Setting::get('mp_access_token');
        if ($accessToken) {
            MercadoPagoConfig::setAccessToken($accessToken);
        }
    }

    protected function getPlanClient()
    {
        if (!$this->planClient) {
            $this->planClient = new PreApprovalPlanClient();
        }
        return $this->planClient;
    }

    protected function getPreApprovalClient()
    {
        if (!$this->preApprovalClient) {
            $this->preApprovalClient = new PreApprovalClient();
        }
        return $this->preApprovalClient;
    }

    protected function getPaymentClient()
    {
        if (!$this->paymentClient) {
            $this->paymentClient = new PaymentClient();
        }
        return $this->paymentClient;
    }

    /**
     * Sync a SubscriptionPlan with MP (PreApprovalPlan)
     */
    public function syncPlan(SubscriptionPlan $plan)
    {
        $frequency = $plan->billing_period === 'monthly' ? 1 : 12;
        $siteUrl = rtrim(Setting::get('site_url') ?: config('app.url'), '/');

        $data = [
            "reason" => $plan->name,
            "auto_recurring" => [
                "frequency" => $frequency,
                "frequency_type" => "months",
                "transaction_amount" => (float) $plan->price,
                "currency_id" => "ARS"
            ],
            "back_url" => $siteUrl . "/cook/subscription"
        ];

        try {
            $client = $this->getPlanClient();
            if ($plan->mp_plan_id) {
                $mpPlan = $client->update($plan->mp_plan_id, [
                    "reason" => $plan->name
                ]);
                return $mpPlan->id;
            } else {
                $mpPlan = $client->create($data);
                return $mpPlan->id;
            }
        } catch (\Exception $e) {
            $this->logError('SyncPlan', $e);
            return null;
        }
    }

    /**
     * Create a Subscription (PreApproval)
     */
    public function createSubscription(array $data)
    {
        try {
            return $this->getPreApprovalClient()->create($data);
        } catch (\Exception $e) {
            $this->logError('CreateSubscription', $e);
            return null;
        }
    }

    /**
     * Get Subscription details
     */
    public function getSubscription($id)
    {
        try {
            return $this->getPreApprovalClient()->get($id);
        } catch (\Exception $e) {
            $this->logError('GetSubscription', $e);
            return null;
        }
    }

    /**
     * Cancel/Update Subscription
     */
    public function updateSubscription($id, array $data)
    {
        try {
            return $this->getPreApprovalClient()->update($id, $data);
        } catch (\Exception $e) {
            $this->logError('UpdateSubscription', $e);
            return null;
        }
    }

    /**
     * Get Payment details
     */
    public function getPayment($id)
    {
        try {
            return $this->getPaymentClient()->get($id);
        } catch (\Exception $e) {
            $this->logError('GetPayment', $e);
            return null;
        }
    }

    /**
     * Deactivate a plan
     */
    public function deactivatePlan(string $mpPlanId)
    {
        try {
            return $this->getPlanClient()->update($mpPlanId, ["status" => "cancelled"]);
        } catch (\Exception $e) {
            $this->logError('DeactivatePlan', $e);
            return false;
        }
    }

    public function testToken(string $token)
    {
        try {
            MercadoPagoConfig::setAccessToken($token);
            
            $parts = explode('-', $token);
            $userId = count($parts) > 1 ? $parts[1] : null;

            if (!$userId || !is_numeric($userId)) {
                 return [
                    'status' => 'error',
                    'message' => 'Formato de token inválido. No se pudo extraer el User ID.'
                ];
            }

            $user = (new \MercadoPago\Client\User\UserClient())->get();

            $isSandbox = str_starts_with($token, 'TEST-');
            
            return [
                'status' => 'success',
                'environment' => $isSandbox ? 'sandbox' : 'production',
                'user' => [
                    'nickname' => $user->nickname,
                    'site_id' => $user->site_id
                ]
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (method_exists($e, 'getApiResponse')) {
                $content = $e->getApiResponse()->getContent();
                $message = $content['message'] ?? $message;
            }
            return [
                'status' => 'error',
                'message' => $message
            ];
        }
    }

    protected function logError($action, \Exception $e)
    {
        $message = "MercadoPagoService $action Error: " . $e->getMessage();
        if (method_exists($e, 'getApiResponse')) {
            $response = $e->getApiResponse();
            $message .= ' | Response: ' . json_encode($response->getContent());
        }
        Log::error($message);
    }

    /**
     * Search for recent payments
     */
    public function searchPayments(array $filters = [])
    {
        try {
            $client = $this->getPaymentClient();
            $searchRequest = new \MercadoPago\Net\MPSearchRequest(50, 0, $filters);
            return $client->search($searchRequest);
        } catch (\Exception $e) {
            $this->logError('SearchPayments', $e);
            return null;
        }
    }
}
