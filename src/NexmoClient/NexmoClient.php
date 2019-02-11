<?php

namespace Botjaeger\NexmoBundle\NexmoClient;

use Nexmo\Client;
use Nexmo\Verify\Verification;

class NexmoClient
{
    private $client;
    private $brand;

    /**
     * NexmoClient constructor.
     * @param Client $client
     * @param string $brand
     */
    public function __construct(Client $client, string $brand)
    {
        $this->client = $client;
        $this->brand = $brand;
    }

    /**
     * @param string $recipient
     * @param string $message
     * @return \Nexmo\Message\Message
     * @throws Client\Exception\Exception
     * @throws Client\Exception\Request
     * @throws Client\Exception\Server
     */
    public function sendSms(string $recipient, string $message)
    {
        return $this->client->message()->send([
            'to'    => $recipient,
            'from'  => $this->brand,
            'text'  => $message,
        ]);
    }

    /**
     * @param string $recipient
     * @return \Nexmo\Verify\Verification
     */
    public function verifyRequest(string $recipient)
    {
        $verification = new Verification($recipient, $this->brand);
        $this->client->verify()->start($verification);
        return $verification;
    }

    /**
     * @param Verification|string request_id $id
     * @param string $code
     * @return array
     * @throws \Exception
     */
    public function verifyCode($id, string $code)
    {
        $verification = new Verification($id);
        try {
            return $this->client->verify()->check($verification, $code);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param Verification|string request_id $id
     * @return Verification
     * @throws \Exception
     */
    public function verifySearch($id)
    {
        $verification = new Verification($id);
        try {
            return $this->client->verify()->search($verification);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param string $id
     * @return \Nexmo\Verify\Verification
     */
    public function resendCode(string $id)
    {
        $verification = new Verification($id);
        return $this->client->verify()->trigger($verification);
    }
}
