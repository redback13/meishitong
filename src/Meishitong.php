<?php

namespace Redback13\Meishitong;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Meishitong
{
    public ?string $host = null;
    public ?string $appId = null;
    public ?string $appKey = null;
    public ?string $des3Key = null;
    public ?string $serviceSubjectCode = null;

    private string $mess;
    private string $timestamp;

    /**
     * 发送请求
     *
     * @param string $action 接口名称
     * @param array $data
     * @throws GuzzleException
     */
    public function sendRequest(string $action, array $data)
    {
        $client = new Client(['base_uri' => $this->host, 'timeout' => 30]);
        $response = $client->request('POST', sprintf("/simple/%s/%s", $this->appId, $action), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form' => $data,
        ]);

        $responseBody = $response->getBody();
        var_dump($responseBody);die;
    }


    public function alipay()
    {
        $this->sendRequest('/alipay/cashout', [
            "mobileNumber" => "17702044084",
            "idcard" => "42020319910513331X",
            "name" => "汪阳",
            "cardNo" => "17702044084",
            "dealSerialNo" => "20230225162500001",
            "serviceSubjectId" => "120001",
            "money" => "1",
            "platformId" => "1444"
        ]);
    }

    protected function encrypt(string $plaintext)
    {
        return openssl_encrypt($plaintext, 'des-ede3-cbc', $this->des3Key, 0, substr($this->des3Key, 0, 8));
    }

    protected function signPlaintext(string $ciphertext)
    {
        return sprintf("data=%s&key=%s&mess=%s&timestamp=%s", $ciphertext, $this->appKey, $this->mess, $this->timestamp);
    }

    protected function sign(string $plaintext)
    {
        return hash_hmac('sha256', $plaintext, $this->appKey);
    }

    protected function mess()
    {
        $this->mess = strval(mt_rand(1000000, 9999999));
    }

    protected function timestamp()
    {
        $this->timestamp = time();
    }
}