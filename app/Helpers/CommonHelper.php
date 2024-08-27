<?php
namespace App\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommonHelper
{
    /*
     * Function Name :  excerpt
     * Purpose       :  This function return excerpt string from original provisioned string
     * Author        :  SM
     * Created Date  :
     * Input Params  :  string original,int limit ,string end
     * Return Value  :  string excerpt
     */
    public static function excerpt($original, $limit = 20, $end = '...')
    {
        return $excerpt = Str::limit($original, $limit, $end);
    }

    /*
     * Function Name :  getCancelbuttonUrl
     * Purpose       :  This function return the redirect url on clicking cancel button of add/edit page
     * Author        :  KB
     * Created Date  :
     * Input Params  :  string $routePrefix, string $fromPage, array $extraParams
     * Return Value  :  string $url
     */

    public static function getCancelbuttonUrl($routePrefix, $fromPage, $extraParams = array())
    {

        if (trim($routePrefix) != '' && trim($fromPage) != '') {
            $url = \Route($routePrefix . '.' . $fromPage);
        } elseif (trim($fromPage) != '') {
            $url = \Route($fromPage);
        } else {
            if (count($extraParams)) {
                $url = \Route($routePrefix . '.list', $extraParams);
            } else {
                $url = \Route($routePrefix . '.list');
            }

        }
        return $url;
    }

    /*
     * Function Name :  encrypt
     * Purpose       :  This function is use for encrypt a string.
     * Author        :  KB
     * Created Date  :
     * Input Params  :  string $value
     * Return Value  :  string
     */

    public static function encrypt($value)
    {
        $cipher = 'AES-128-ECB';
        $key = \Config::get('app.key');
        return openssl_encrypt($value, $cipher, $key);
    }

    /*
     * Function Name :  decrypt
     * Purpose       :  This function is use for decrypt the encrypted string.
     * Author        :  KB
     * Created Date  :
     * Input Params  :  string $value
     * Return Value  :  string
     */

    public static function decrypt($value)
    {
        $cipher = 'AES-128-ECB';
        $key = \Config::get('app.key');
        return openssl_decrypt($value, $cipher, $key);
    }

    /*
     * Function Name :  partialEmailidDisplay
     * Purpose       :  This function is use for hiding some characters of en email id.
     * Author        :  KB
     * Created Date  :
     * Input Params  :  string $value
     * Return Value  :  string
     */

    public static function partialEmailidDisplay($email)
    {
        $rightPartPos = strpos($email, '@');
        $leftPart = substr($email, 0, $rightPartPos);
        $displayChars = (strlen($leftPart) / 2);
        if ($displayChars < 1) {
            $displayChars = 1;
        }
        return substr($leftPart, 0, $displayChars) . '*******' . substr($email, $rightPartPos);
    }

    public static function encryptId($value)
    {
        // $hashids = new Hashids(\Config::get('app.key'));
        // return $hashids->encode($value);
        $cipher = 'AES-128-ECB';
        $key = \Config::get('app.key');
        return base64_encode(openssl_encrypt($value, $cipher, $key));
    }

    public static function decryptId($value)
    {
        // $hashids = new Hashids(\Config::get('app.key'));
        // return (count($decptid = $hashids->decode($value))? $decptid[0]: '');
        $cipher = 'AES-128-ECB';
        $key = \Config::get('app.key');
        return openssl_decrypt(base64_decode($value), $cipher, $key);
    }

    public  function getUserResult($user_id,$exam_id)
    {
        $details=array();
        try {
            $publicKey = Session()->get('usertoken');
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/user-result';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                    "examination_id" => $exam_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $details = $response['result']['listing'];
            // dd($data);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                //$response = json_decode($e->getResponse()->getBody()->getContents());
                
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            
        }
        return $details;
    }
}
