<?php
namespace App\payment;

class Zibal{

public static function send($amount) {
    $data = array(
        'merchant' =>env('ZIBAL_API'),
        'amount' =>$amount,
        'callbackUrl' =>env('PAYMENT_CALLBACK_URL')."?gate=zibal",
        'description' => 'خرید تست'
    );



    $jsonData = json_encode($data);
    $ch = curl_init('https://gateway.zibal.ir/v1/request');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        )
    );


    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true);
    curl_close($ch);


    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        if ($result["result"] == 100) {

            return ['url'=>'https://gateway.zibal.ir/start/' . $result["trackId"],
                'token'=>$result["trackId"]] ;
        } else {
            return 'ERR: ' . $result["result"];
        }
    }
}
public static function verify( $token) {
    $MerchantID = env('ZIBAL_API');


    $Authority = $token;

    $data = array('merchant' => $MerchantID, 'trackId' => $token);
    $jsonData = json_encode($data);
    $ch = curl_init('https://gateway.zibal.ir/v1/verify');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        )
    );
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    $result = json_decode($result, true);
    return $result;
    if ($err) {
        return ['error' => 'عملیات پرداخت با شکست انجام شد'. 'شماره خطا=' . $err] ;
    } else {
        if ($result['result'] == 100) {


            return $result;



        } else {
            // dd($result,'fail');
            return ['error'=>'عملیات پرداخت با شکست انجام شد'];
        }
    }
}

}
