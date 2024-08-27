<?php
/*****************************************************
# TrusteeController
# Class name : TrusteeController
# Author :
# Created Date : 20-06-2024
# Functionality : Trustee dashboard related logics
/*****************************************************/
namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Traits\GeneralMethods;
use Exception;
use GlobalVars;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

class TrusteeController extends Controller
{
    use GeneralMethods;
    /*
     * Function name : index
     * Purpose : trustee dashboard
     * Author  :
     * Created Date : 20-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function index()
    {
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        return view('trustee.index');
    }

    /*
     * Function name : schools
     * Purpose : trustee dashboard
     * Author  :
     * Created Date : 20-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function schools()
    {
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        // if ($request->isMethod('post')) {
        //     dd($request->all());
        // }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-trustee-schools' . '?search_text=' . $search_text . '&page=' . $page;

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['total'];
                $current_page = $response['result']['listing']['current_page'];
                $prev_page_url = $response['result']['listing']['prev_page_url'];
                $next_page_url = $response['result']['listing']['next_page_url'];

                $prev_page = $current_page;
                $next_page = $current_page;

                $has_next_page = true;
                if ($next_page_url == null) {
                    $has_next_page = false;
                    $prev_page = ($current_page > 1) ? ($current_page - 1) : $current_page;
                    $next_page = $current_page;
                } else {
                    $next_page = $current_page + 1;
                }
                $has_previous_page = true;
                if ($prev_page_url == null) {
                    $has_previous_page = false;
                    $prev_page = $current_page;
                    $next_page = $current_page + 1;
                } else {
                    $prev_page = $current_page - 1;
                }
            }
            $no_image = GlobalVars::API_NO_IMAGE;
            return view(
                'trustee.schools', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url','no_image'
                )
            );

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('t_dashboard');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('t_dashboard')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : myaccount
     * Purpose : trustee myaccount
     * Author  :
     * Created Date : 20-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function myAccount(Request $request)
    {
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        if ($request->isMethod('post')) {
            // dd($request->all());
            //Update profile information
            if (isset($_POST['btnUpdateProfile'])) {
                // dd($request->all());
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/user/update';
                    $form_params = [
                        "user_id" => $userInfo['user_id'],
                        "first_name" => $request->first_name ?? '',
                        "middle_name" => $request->middle_name ?? '',
                        "last_name" => $request->last_name ?? '',
                        "address" => $request->address ?? '',
                        "phone" => $request->phone ?? '',
                        "profile_image" => $request->imagedata_profile_image ?? '',
                    ];
                    // dd(json_encode($form_params));
                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);
                    $notification = array(
                        'message' => 'Profile data updated successful.',
                        'alert-type' => 'success',
                    );
                    $this->refreshLoginData();
                    return \Redirect::route('t_myaccount')->with($notification);

                } catch (RequestException $e) {
                    // Catch all 4XX errors
                    // To catch exactly error 400 use
                    if ($e->hasResponse()) {
                        //if ($e->getResponse()->getStatusCode() == '400') {
                        // echo "Got response 400";
                        // $response = json_decode($e->getResponse()->getBody()->getContents());
                        $notification = array(
                            'message' => $response->error->message,
                            'alert-type' => 'error',
                        );
                        // dd($response->error->message);
                        return \Redirect::route('front_login')->with($notification);
                        //}
                    }
                    // You can check for whatever error status code you need

                } catch (Exception $e) {
                    $notification = array(
                        'message' => $e->getMessage(),
                        'alert-type' => 'error',
                    );
                    // throw ($e);
                    // throw new \App\Exceptions\AdminException($e->getMessage());
                    return \Redirect::route('front_login')->with($notification);
                }
            }

            //change password
            if (isset($_POST['btnChangePassword'])) {
                // dd($request->all());
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/user/change-password';
                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => [
                            "current_password" => $request->current_password ?? '',
                            "new_password" => $request->new_password ?? '',
                            "confirm_password" => $request->confirm_password ?? '',
                        ],
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    Session()->put('userl', $response['result']['credentials']);
                    // dd($response);
                    $notification = array(
                        'message' => 'Password changed successful.',
                        'alert-type' => 'success',
                    );
                    $this->refreshLoginData();
                    return \Redirect::route('t_myaccount')->with($notification);

                } catch (RequestException $e) {
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
                        return \Redirect::route('t_myaccount')->with($notification);
                        //}
                    }
                    // You can check for whatever error status code you need

                } catch (Exception $e) {
                    $notification = array(
                        'message' => $e->getMessage(),
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('t_myaccount')->with($notification);
                    // throw ($e);
                    // throw new \App\Exceptions\AdminException($e->getMessage());
                }
            }
        }

        return view('trustee.myaccount', compact('userInfo', 'profileInfo', 'tenantInfo'));
    }


}
