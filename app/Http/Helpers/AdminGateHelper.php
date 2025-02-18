<?php

namespace App\Http\Helpers;


use Illuminate\Support\Facades\DB;

trait AdminGateHelper
{

    public function createUnderConstructionAccount($data)
    {
        $link = env('ADMIN_API_LINK') . "/Admin-Gate/create-under-construction-account";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'company_name'        => $data->company_name,
                    'sub_domain'          => $data->sub_domain,
                    'mobile_number'       => $data->mobile_number,
                    'mobile_country_code' => $data->mobile_country_code,
                    'full_mobile_number'  => $data->full_mobile_number,
                    'email'               => $data->email,
                    'password'            => $data->password,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;
    }

    public function getUnderConstructionAccountData($accountId){

        $link = env('ADMIN_API_LINK') . "/Admin-Gate/get-under-construction-account-data/".$accountId;

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->get($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'account_id'    => $accountId,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;

    }

    public function resendUnderConstructionAccountConfirmationCode($accountId){

        $link = env('ADMIN_API_LINK') . "/Admin-Gate/resend-under-construction-account-confirmation-code";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'account_id'    => $accountId,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;

    }

    public function confirmUnderConstructionAccount($accountId, $confirmationCode){

        $link = env('ADMIN_API_LINK') . "/Admin-Gate/confirm-under-construction-account";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'account_id'    => $accountId,
                    'confirmation_code'    => $confirmationCode,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;

    }

    public function getDefaultAccountsTypes($accountId){

        $link = env('ADMIN_API_LINK') . "/Admin-Gate/get-default-accounts-types/";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->get($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;

    }

    public function getHelpDataFromAdmin($current_link){
        # New Request To Admin Gate Application
        $link = env('ADMIN_API_LINK') . "/Admin-Gate/get-help-data";
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key'  => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'link'    => $current_link,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            //dd($e->getResponse());
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;
    }

    public function getPlansFromAdmin()
    {
        $link = env('ADMIN_API_LINK') . "/Admin-Gate/get-plans/";

        # New Request To Admin Gate Application
        try {
            $http = new \GuzzleHttp\Client();

            $response = $http->get($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key' => env('ADMIN_GATE_KEY'),
                    'Accept' => 'application/json',
                ],
                'form_params' => [],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;
    }

    public function sendSubscriptionDataToAdmin($data)
    {
        $link = env('ADMIN_API_LINK') . "/Admin-Gate/add-subscription";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key' => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'subscription_data' => json_encode($data),
                    'account_id'        => tenant()->id,
                    'plan_id'           => $data->plan_id,
                ],
            ]);

            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;
    }


    public function paySubscription($subscriptionId)
    {
        $link = env('ADMIN_API_LINK') . "/Admin-Gate/pay-subscription";

        # New Request To Admin Gate Application
        try {
            $http     =  new \GuzzleHttp\Client();
            $response = $http->post($link, [
                'http_errors' => true,
                'headers' => [
                    'admin-gate-key' => env('ADMIN_GATE_KEY'),
                    'Accept'         => 'application/json'
                ],
                'form_params' => [
                    'subscription_id' => $subscriptionId
                ],
            ]);

            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 200, 'response' => $response];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $response = json_decode($response, true);
            $response = ['code' => 500, 'response' => $response['message']];
        }

        # Return To Response.
        return $response;
    }

}
