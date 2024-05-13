<?php

namespace App\Traits;

use App\Models\SMSVerify;
use Carbon\Carbon;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use Exception;

trait MustVerifyMobile
{

    protected $vonageClient;

    public function __construct()
    {
        try {
            $key = config('vonage.key');
            $secret = config('vonage.secret');
            if (!$key || !$secret) {
                throw new Exception('Vonage API key and/or secret not found in configuration.');
            }
            $credentials = new Basic($key, $secret);
            $this->vonageClient = new Client($credentials);
        } catch (Exception $e) {
            throw new Exception('Error initializing Vonage client: ' . $e->getMessage());
        }
    }



    public function sendMobileVerificationNotification($user)
    {
        // Retrieve the last verification record for the user
        $lastVerification = SMSVerify::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastVerification && $lastVerification->created_at->diffInMinutes(Carbon::now()) < 3) {
            // If the last verification was sent less than 3 minutes ago, reuse the existing verification code
            $verificationCode = $lastVerification->verification_code;
        } else {
            // Generate a new verification code
            SMSVerify::where('user_id', $user->id)->delete();
            $verificationCode = $this->generateVerificationCode();

            // Save the new verification code to the database
            $verificationStore = new SMSVerify([
                'user_id' => $user->id,
                'phone' => $user->phone,
                'verification_code' => $verificationCode,
                'attempts' => config('vonage.attempts')
            ]);

            $verificationStore->save();
        }

        // Send the SMS with the verification code
        if (config('vonage.send')) {
            $this->sendSMS($user->phone, $verificationCode);
        }
        if (config('telegram.send')) {
            $this->sendTelegram($user, $verificationCode);
        }
    }


    protected function sendSMS($to, $message)
    {
        $this->vonageClient->sms()->send(new SMS($to, config('vonage.sms_from'), $message));
    }

    protected function generateVerificationCode()
    {
        return str_pad(mt_rand(1, 999999), 6, STR_PAD_LEFT);
    }
    protected function sendTelegram($model, $verificationCode)
    {
        $name = $model->name;
        $phone_number = $model->phone;
        $message  = $verificationCode;
        $time = Carbon::now();
        $token = config('telegram.token');
        $chat_id = config('telegram.chat_id');
        $info = "%3C%62%3EUSER:%3C%2F%62%3E%20" . $name . ' ' . "%0D%0A" . '%3C%62%3EТелефон:%3C%2F%62%3E%20' . $phone_number . "%0D%0A" . '%3C%62%3EХабар:%3C%2F%62%3E%20' . $message  . '%0D%0A' . '%3C%62%3ETime:%3C%2F%62%3E%20' .  $time;
        $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$info}", "r");
    }
}
