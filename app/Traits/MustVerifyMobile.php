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
    }


    protected function sendSMS($to, $message)
    {
        $this->vonageClient->sms()->send(new SMS($to, config('vonage.sms_from'), $message));
    }

    protected function generateVerificationCode()
    {
        return str_pad(mt_rand(1, 999999), 6, STR_PAD_LEFT);
    }
}
