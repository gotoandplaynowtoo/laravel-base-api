<?php

namespace App;

use App\BaseModels\BaseAuthenticatableModel;

class User extends BaseAuthenticatableModel
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'verified',
        'verification_token',
        'api_token',
        'record_status'
    ];

    protected $hidden = [
        'password',
        'verified',
        'verification_token',
        'api_token',
        'record_status'
    ];

    // CUSTOMS
    public function generateToken() {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }

    public function generateVerificationToken() {
        $this->verification_token = str_random(40);
        $this->save();

        return $this->verification_token;
    }

    public function getApiToken() {
        return $this->api_token;
    }
}
