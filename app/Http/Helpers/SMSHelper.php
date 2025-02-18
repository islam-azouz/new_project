<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\DB;

trait SMSHelper
{

    public function sendMsg($phoneNumber, $msg)
    {
        $sms_appsid = DB::table('settings')->where('type', 'services_settings')->select('data->sms_appsid')->first()->sms_appsid;
        $http =  new \GuzzleHttp\Client();

        $http->post('http://el.cloud.unifonic.com/rest/SMS/Messages/Send', [
            'form_params' => [
                'AppSid'    => $sms_appsid, //"8ByWdcriRRKeBEAVpeQvdtUY11XCXO",
                'Body'      => $msg,
                'Recipient' => $phoneNumber,
            ],
        ]);
    }
}
