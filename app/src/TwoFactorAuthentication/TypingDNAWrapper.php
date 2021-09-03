<?php

namespace App\TwoFactorAuthentication;

class TypingDNAWrapper
{
    private TypingDNAVerifyClient $client;

    public function __construct(string $clientID, string $applicationID, string $secret)
    {
        $this->client = new TypingDNAVerifyClient($clientID, $applicationID, $secret);
    }

    /**
     * @param string $phoneNumber
     * @return array
     */
    public function getDataAttributes(string $phoneNumber) : array
    {
        return $this->getClient()->getDataAttributes(
            [
                'phoneNumber' => $phoneNumber,
                'language' => 'en',
                'mode' => 'standard',
            ]
        );
    }

    /**
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function isValidOTP(string $phoneNumber, string $otp) : bool
    {
        $response = $this->getClient()
            ->validateOTP([
                'phoneNumber' => $phoneNumber,
            ], $otp);

        return $response['success'];
    }

    private function getClient() : TypingDNAVerifyClient
    {
        return $this->client;
    }
}