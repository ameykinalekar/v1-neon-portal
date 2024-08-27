<?php
/*****************************************************
# HomeController
# Class name : HomeController
# Author :
# Created Date :
# Functionality :
/*****************************************************/
namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Traits\GeneralMethods;
use GlobalVars;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use LaravelFileViewer;
use Session;

class HomeController extends Controller
{
    use GeneralMethods;
    /*
     * Function name : index
     * Purpose :
     * Author  :
     * Created Date : 28-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */

    public function index()
    {
        try {
            // session()->flush();
            $subdomain = CommonHelper::decryptId(request()->cookie(GlobalVars::COOKIE_TENANT_KEY));

            if (isset($subdomain) && $subdomain != null) {
                switch ($subdomain) {
                    case "pa":
                        return \Redirect::route('front_login');
                        break;

                    default:
                        // dd('subdomain -- '.$subdomain);
                        return \Redirect::route('tenant_login', [$subdomain]);
                        break;
                }
            } else {

                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/dropdown/portal-tenants';

                // $apiEndpoint = config('app.api_base_url') . '/dps/blog';
                $call = $client->post($apiEndpoint, [
                    'headers' => ['X_NEON' => config('app.api_key')],
                    //'body' => json_encode($data),
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $data['tenants'] = $response['result']['tenants'];
                $apiEndpoint = config('app.api_base_url') . '/dropdown/countries';
                $call = $client->post($apiEndpoint, [
                    // 'headers' => ['Authorization' => 'Bearer ' . $publicKey],

                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $data['countries'] = $response['result']['listing'];
                return view('welcome', $data);
            }

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // dd($e->getResponse());
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";

                return \Redirect::route('front_login')->withErrors($response->error->message);
                // }
                // dd($e->getResponse());
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            throw ($e);
        }
    }

    /**
     * Function Name :  doRedirect
     * Purpose       :  This function use for redirection to selected destination.
     * Author        :
     * Created Date  : 28-02-2024
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doRedirect(Request $request)
    {
        try {
            $minutes = (365 * 24 * 60);
            Cookie::queue(Cookie::forget(GlobalVars::COOKIE_TENANT_KEY));
            session()->flush();
            if (isset($request->subdomain) && $request->subdomain != null && $request->subdomain != 'pa') {
                Cookie::queue(GlobalVars::COOKIE_TENANT_KEY, CommonHelper::encryptId($request->subdomain), $minutes);
                return \Redirect::route('tenant_login', $request->subdomain);
            } else {
                Cookie::queue(GlobalVars::COOKIE_TENANT_KEY, CommonHelper::encryptId($request->subdomain), $minutes);
                return \Redirect::route('front_login');
            }

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_index')->with($notification);
        }
    }

    /**
     * Function Name :  doFlush
     * Purpose       :  This function use for redirection to select destination.
     * Author        :
     * Created Date  :  28-02-2024
     * Modified date :
     * Input Params  :  void
     * Return Value  :  void
     */
    public function doFlush()
    {
        session()->flush();
        return \Redirect::route('front_index');
    }

    /**
     * Function Name :  doBye
     * Purpose       :  This function use for redirection to select destination.
     * Author        :
     * Created Date  :  28-02-2024
     * Modified date :
     * Input Params  :  void
     * Return Value  :  void
     */
    public function doBye()
    {
        Cookie::queue(Cookie::forget(GlobalVars::COOKIE_TENANT_KEY));
        session()->flush();
        return \Redirect::route('front_index');
    }

    /**
     * Function Name :  invitationThanks
     * Purpose       :  This function use for redirection to select destination.
     * Author        :
     * Created Date  :  19-04-2024
     * Modified date :
     * Input Params  :  void
     * Return Value  :  void
     */
    public function invitationThanks()
    {
        return view('invitation-thanks');
    }

    /*
     * Function name : invitation
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : void
     * Return : void
     */
    public function invitation(Request $request)
    {

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $data['token'] = $token = $request->token ?? '';
        try {
            $userInfo = Session::get('user');
            $profileInfo = Session::get('profile_info');
            $tenantInfo = Session::get('tenant_info');

            // dd($tenantInfo['subdomain']);
            $client = new Client();
            if ($request->isMethod('post')) {
                $status = 'Invitation';
                if (isset($_POST['btnAccept'])) {
                    $status = GlobalVars::ACTIVE_STATUS;
                    // dd('accept::' . $token);

                    $apiEndpoint = config('app.api_base_url') . '/invitation-response';
                    $form_params = [
                        "token" => $token ?? '',
                        "session_subdomain" => $tenantInfo['subdomain'] ?? '',
                        "session_email" => $userInfo['email'] ?? '',
                        "session_userid" => $userInfo['user_id'] ?? '',
                        "status" => $status,
                    ];
                    // print_r($request->all());
                    // dd(json_encode($form_params));
                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);
                    $notification = array(
                        'message' => $response['result']['message'],
                        'alert-type' => 'success',
                    );
                    return \Redirect::route('front_invitation_thanks')->with($notification);
                }
                if (isset($_POST['btnDecline'])) {
                    $status = GlobalVars::INACTIVE_STATUS;
                    // dd('decline::' . $token);
                    $apiEndpoint = config('app.api_base_url') . '/invitation-response';
                    $form_params = [
                        "token" => $token ?? '',
                        "session_subdomain" => $tenantInfo['subdomain'] ?? '',
                        "session_email" => $userInfo['email'] ?? '',
                        "session_userid" => $userInfo['user_id'] ?? '',
                        "status" => $status,
                    ];
                    // print_r($request->all());
                    // dd(json_encode($form_params));
                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);
                    $notification = array(
                        'message' => $response['result']['message'],
                        'alert-type' => 'success',
                    );
                    return \Redirect::route('front_invitation_thanks')->with($notification);
                }
            }

            // $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/view-invitation';
            $form_params = [
                "token" => $token ?? '',
                "session_subdomain" => $tenantInfo['subdomain'] ?? '',
                "session_email" => $userInfo['email'] ?? '',
                "session_userid" => $userInfo['user_id'] ?? '',
            ];
            // print_r($request->all());
            // dd(json_encode($form_params));
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['result'] = $response['result'];
            // dd($response);

            return view('invitation', $data);

        } catch (RequestException $e) {
            throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('front_flush', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

    }

    public function file_preview()
    {
        try {
            $filename = 'uploads/library/1/1/vkbDKPmHLK6Gm9LNB1JN3FJEAN91MFjOR2HsbZfw.pptx';
            $filepath = config('app.api_asset_url') . '/' . $filename;
            // dd($filepath);
            // $filepath='public/'.$filename;
            $file_url = asset('storage/' . $filename);
            $file_data = [
                [
                    'label' => __('Label'),
                    'value' => "Value",
                ],
            ];

            // dd(config('app.aliases'));
            // return view('invitation-thanks');
            return LaravelFileViewer::show($filename, $filepath, $file_url, $file_data);
        } catch (Exception $e) {
            throw ($e);
        }

    }
}
