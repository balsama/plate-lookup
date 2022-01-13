<?php

namespace Balsama\BostonPlateLookup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class RequestPlate
{
    private static string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36';
    private static string $cookie = 'visid_incap_391913=Hkzn565NQKKxpRnWU5HOu54Gl2EAAAAAQUIPAAAAAADezASmuDKfymsdMw2jYCcI; JSESSIONID=0000EJGmmud0EuoavSm6JTwPS3T:17bhd4lo9; nlbi_391913=2xJsVdnuGSB2bAgRSsw+NgAAAACIEfNzjBjVnrAfFeS3eZll; incap_ses_1466_391913=6+NFWSHtMhwrvtcxXEdYFNOp3GEAAAAAleCd7KVOsOAC015A9iQiCA==; nlbi_391913_2147483646=0pO1e8FYEhkzveDKSsw+NgAAAAD2gzstAXJzS+ARWiGIixTT; reese84=3:6UTgwZsIxgVD+21XIOLasA==:meYeHq3Gv9UXOKjsNbixdHT1gy/VPer6/qhIjH5a9wMNvhdogEDHEU7yzAhKTCZhAmh4maP8jQGYCbzZJC7HU6gxRnonuumSmUARExXq9lsesh2R2sgxrqFV834+dhJaAr1KPC3gh+Nfhz60qUs73g/qqLzus3zD1Os8ukqOJZkeNB2YXC+aifmNYDVSBJ0KrLVycfaHDyEQ7aO3hiuDBbZIal5Ko14DAXWh0S4mPUQcV9t4kDE+qpQkduraFEWJgywT19f+HEMd9V+DNz75Y3p7k0+Mh79xwED5i9k59EOGXHdaTKUVQEcsc06A/Aw/RTopVt9SkaI7F6CTQME12nMViVKWSg5eh8Mer/TxwOkyFcFgpnb6opnfKTtgT62Yc8+CEwJfO5JLPQl05OVQCI9zjp8UI4bTvntd0LZ2o7M=:cD7b19YGG9NqtUOJvOcuT2wHXpiyUwXlbuKR+iVkftc=';
    private static string $tokenKey = 'ztgaqgxwr';
    private static string $endpoint = 'https://wmq.etimspayments.com/pbw/inputAction.doh';

    public static function request(LookupParameters $lookupParameters, $retryOnError = 5)
    {
        $client = new Client();
        try {
            $request = $client->request('POST', self::$endpoint, [
                'headers' => [
                    'User-Agent' => self::$userAgent,
                    'cookie' => self::$cookie,
                    'Accept' => '*/*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                ],
                'form_params' => [
                    'clientcode'  => '03',
                    'clientAccount'  => '7',
                    'requestType'  => 'submit',
                    'paymentType'  => 'P',
                    'TokenKey'  => self::$tokenKey,
                    'plateNumber'  => $lookupParameters->plateNumber,
                    'plateType'  => $lookupParameters->plateType,
                    'birthMonth'  => $lookupParameters->getMonth(),
                    'birthDay'  => $lookupParameters->getMonthDay(),
                    'submit'  => 'Submit',
                ],
                'debug' => false
            ]);
        } catch (ServerException|GuzzleException $e) {
            if ($retryOnError) {
                $retryOnError--;
                usleep(250000);
                return self::request($lookupParameters, $retryOnError);
            }
            throw $e;
        }
        return $request;
    }

}