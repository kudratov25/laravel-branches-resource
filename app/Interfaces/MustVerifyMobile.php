<?php

namespace App\Interfaces;

interface MustVerifyMobile
{
    public function sendMobileVerificationNotification($user);

}
