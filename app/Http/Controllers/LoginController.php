<?php
/*****************************************************
# LoginController
# Class name : LoginController
# Author :
# Created Date :
# Functionality :
/*****************************************************/
namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Exception;
use GlobalVars;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Validator;
use \Redirect;

class LoginController extends Controller
{
    /*
     * Function name : login
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : void
     * Return : void
     */
    public function login()
    {
        $subdomain = CommonHelper::decryptId(request()->cookie(GlobalVars::COOKIE_TENANT_KEY));
        if ($subdomain == null) {
            return \Redirect::route('front_index');
        }
        return view('login.index');
    }

    /**
     * Function Name :  dologin
     * Purpose       :  This function use for login a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function dologin(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            if ($Validator->fails()) {
                return \Redirect::route('front_login')->withErrors($Validator);
            } else {
                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/auth';
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'email' => $request->email,
                        'password' => $request->password,
                    ],
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);
                $request->session()->put('usertoken', $response['result']['token']);
                $request->session()->put('userl', $response['result']['credentials']);
                // Auth::login($user, true);
                // dd($request->session()->all());

                $publicKey = $request->session()->get('usertoken');

                $apiEndpoint = config('app.api_base_url') . '/auth/validate';
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'token' => $publicKey,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);

                //dd($response);

                if ($response['result']['user']['user_type'] == 'SW' || $response['result']['user']['user_type'] == 'TA' || $response['result']['user']['status'] == GlobalVars::INACTIVE_STATUS) {
                    session()->flush();
                    $notification = array(
                        'message' => 'You are not authorized to login here...',
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('front_login')->with($notification);
                }

                Session()->put('user', $response['result']['user']);
                Session()->put('profile_info', $response['result']['profile_info']);
                Session()->put('tenant_info', $response['result']['tenant_info']);
                Session()->put('setting_info', $response['result']['setting_info']);
                Session()->put('total_active_users_count', $response['result']['total_active_users_count']);
                // dd($response['result']);

                if (!empty($response['result']['tenant_info'])) {

                    //set total active user count

                    $notification = array(
                        'message' => 'Tenant Login successful.',
                        'alert-type' => 'success',
                    );
                    // dd(Session()->get('tenant_info')['subdomain']);
                    return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                } else {
                    if ($response['result']['user']['user_type'] == 'T') {
                        $notification = array(
                            'message' => 'Trustee Login successful.',
                            'alert-type' => 'success',
                        );
                        return \Redirect::route('t_dashboard')->with($notification);
                    } else {
                        $notification = array(
                            'message' => 'Portal Admin Login successful.',
                            'alert-type' => 'success',
                        );
                        return \Redirect::route('pa_dashboard')->with($notification);
                    }
                }
            }
        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                // dd($e->getResponse()->getBody()->getContents());
                $response = json_decode($e->getResponse()->getBody()->getContents());
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
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);

            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : login
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : void
     * Return : void
     */
    public function loginTenant()
    {
        // dd('hi');
        $subdomain = CommonHelper::decryptId(request()->cookie(GlobalVars::COOKIE_TENANT_KEY));
        if ($subdomain == null) {
            return \Redirect::route('front_index');
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-tenant-config';
            $call = $client->post($apiEndpoint, [
                'headers' => ['X_NEON' => config('app.api_key')],
                'form_params' => [
                    'subdomain' => $subdomain,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);

            Session()->put('tenant_info', $response['result']['tenant_info']);
            Session()->put('setting_info', $response['result']['setting_info']);
            Session()->put('tenant_short_name', $response['result']['tenant_short_name']);
            return view('login.tenant-index');

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                // dd($e->getResponse()->getBody()->getContents());
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('front_index')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_index')->with($notification);

            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /**
     * Function Name :  doLoginTenant
     * Purpose       :  This function use for login a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doLoginTenant(Request $request)
    {
        // dd($request->all());
        $Validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'usertype' => 'required',
        ]);
        // dd(request()->segments());
        $current_uri = request()->segments();
        $urlsubdomain = '';
        if (is_array($current_uri)) {
            $urlsubdomain = $current_uri[0];
        }
        // dd($urlsubdomain);
        $subdomain = session()->get('tenant_info')['subdomain'] ?? '';
        try {
            if ($Validator->fails()) {
                return \Redirect::route('tenant_login', $subdomain)->withErrors($Validator);
            } else {

                $client = new Client();
                if ($request->usertype == 'TA') {
                    $apiEndpoint = config('app.api_base_url') . '/auth';
                } else {
                    $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/auth';
                }
                // dd($apiEndpoint);
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'email' => $request->email,
                        'password' => $request->password,
                    ],
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);
                $request->session()->put('usertoken', $response['result']['token']);
                $request->session()->put('userl', $response['result']['credentials']);

                $publicKey = $request->session()->get('usertoken');
                // Auth::login($user, true);
                // dd($request->session()->all());

                //if ($request->usertype == 'TU') {
                $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
                $call = $client->post($apiEndpoint, [
                    'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                    //'body' => json_encode($data),
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                $boards = $response['result']['boards'];
                $arrBoards = array();
                $arrShortBoards = array();
                foreach ($boards as $rec) {
                    $arrBoards[$rec['board_id']] = $rec['board_name'];
                    $arrShortBoards[$rec['board_id']] = $rec['short_name'];
                }
                $request->session()->put('datalist_boards', $arrBoards);
                $request->session()->put('datalist_shortboards', $arrShortBoards);
                //}
                // dd($request->session()->get('datalist_boards'));

                if ($request->usertype == 'TA') {
                    $apiEndpoint = config('app.api_base_url') . '/auth/validate';
                } else {
                    $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/auth/validate';
                }
                // dd($apiEndpoint);
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'token' => $publicKey,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);

                if ($response['result']['user']['user_type'] == 'T' || $response['result']['user']['user_type'] == 'SW' || $response['result']['user']['user_type'] == 'PA' || $response['result']['user']['status'] == GlobalVars::INACTIVE_STATUS) {
                    session()->flush();
                    $notification = array(
                        'message' => 'You are not authorized to login here...',
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('tenant_login', $subdomain)->with($notification);
                }

                if ($request->usertype == 'TA') {
                    if ($urlsubdomain != $response['result']['tenant_info']['subdomain']) {
                        session()->flush();
                        $notification = array(
                            'message' => 'You are not authorized to login here...',
                            'alert-type' => 'error',
                        );
                        return \Redirect::route('tenant_login', $subdomain)->with($notification);

                    }
                    Session()->put('user', $response['result']['user']);
                    Session()->put('profile_info', $response['result']['profile_info']);
                    Session()->put('tenant_info', $response['result']['tenant_info']);
                    Session()->put('setting_info', $response['result']['setting_info']);
                    Session()->put('total_active_users_count', $response['result']['total_active_users_count']);

                } else if ($request->usertype == 'P') {
                    if ($urlsubdomain != $response['result']['tenant_info']['subdomain']) {
                        session()->flush();
                        $notification = array(
                            'message' => 'You are not authorized to login here...',
                            'alert-type' => 'error',
                        );
                        return \Redirect::route('tenant_login', $subdomain)->with($notification);

                    }
                    Session()->put('user', $response['result']['user']);
                    Session()->put('profile_info', $response['result']['profile_info']);
                    Session()->put('tenant_info', $response['result']['tenant_info']);
                    Session()->put('setting_info', $response['result']['setting_info']);
                    Session()->put('total_active_users_count', $response['result']['total_active_users_count']);
                } else {
                    // dd($request->usertype);
                    if ($request->usertype == $response['result']['user']['user_type']) {
                        Session()->put('user', $response['result']['user']);
                        Session()->put('profile_info', $response['result']['profile_info']);
                        Session()->put('total_active_users_count', $response['result']['total_active_users_count']);
                    } else {
                        session()->flush();
                        $notification = array(
                            'message' => 'You are not authorized to login here...',
                            'alert-type' => 'error',
                        );
                        return \Redirect::route('tenant_login', $subdomain)->with($notification);
                    }

                }
                $notification = array(
                    'message' => 'Login successful.',
                    'alert-type' => 'success',
                );
                // dd(Session()->all());
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);

                // if (!empty($response['result']['tenant_info'])) {
                //     $notification = array(
                //         'message' => 'School Login successful.',
                //         'alert-type' => 'success',
                //     );
                //     // dd(Session()->get('tenant_info')['subdomain']);
                //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                // } else {
                //     $notification = array(
                //         'message' => 'Portal Admin Login successful.',
                //         'alert-type' => 'success',
                //     );
                //     return \Redirect::route('pa_dashboard')->with($notification);
                // }
            }
        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                // dd($e->getResponse()->getBody()->getContents());
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tenant_login', $subdomain)->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_login', $subdomain)->with($notification);

            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : logout
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : void
     * Return : void
     */
    public function logout(Request $request)
    {
        try {
            $publicKey = $request->session()->get('usertoken');
            // dd($publicKey);
            $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
            // dd($subdomain);
            $request->session()->forget('usertoken');
            $request->session()->forget('userl');
            $request->session()->forget('user');
            $request->session()->forget('profile_info');
            $request->session()->forget('tenant_info');
            $request->session()->flush();
            if ($subdomain == null) {
                $apiEndpoint = config('app.api_base_url') . '/auth/logout';

                $client = new Client();
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'token' => $publicKey,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
            }
            $notification = array(
                'message' => 'Logout successful.',
                'alert-type' => 'success',
            );
            if ($subdomain != null) {
                return \Redirect::route('tenant_login', $subdomain)->with($notification);
            } else {
                return \Redirect::route('front_login')->with($notification);
            }
        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 400";
                    $response = json_decode($e->getResponse()->getBody()->getContents());
                    // dd($response->error->message);
                    $notification = array(
                        'message' => $response->error->message,
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('front_login')->with($notification);
                }
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response->error->message);
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                return \Redirect::route('front_index')->with($notification);
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_index')->with($notification);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /**
     * Function Name :  doForgotPassword
     * Purpose       :  This function use for forgot password for a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doForgotPassword(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        try {
            if ($Validator->fails()) {
                return \Redirect::route('front_login')->withErrors($Validator);
            } else {
                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/forgot-password';
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'email' => $request->email,
                    ],
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);

                $notification = array(
                    'message' => $response['result']['message'],
                    'alert-type' => 'success',
                );
                return \Redirect::route('front_login')->with($notification);
            }
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
                return \Redirect::route('front_login')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : resetPassword
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : token
     * Return : void
     */
    public function resetPassword($encryptToken)
    {
        try {
            $decryptToken = \Helpers::decryptId($encryptToken); //decrypt the token
            $tokenArr = explode("_", $decryptToken);
            $emailArr = explode("~", $tokenArr[0]);
            $email = $emailArr[0];
            $utype = $emailArr[1];
            $token = $tokenArr[1];

            $data['token'] = $encryptToken;

            return view('login.reset-password', $data);
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * Function Name :  doResetPassword
     * Purpose       :  This function use for forgot password for a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doResetPassword(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'reset_token' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
        ]);

        try {
            if ($Validator->fails()) {
                return \Redirect::route('front_login')->withErrors($Validator);
            } else {
                $decryptToken = \Helpers::decryptId($request->reset_token); //decrypt the token
                $tokenArr = explode("_", $decryptToken);
                $emailArr = explode("~", $tokenArr[0]);
                $email = $emailArr[0];
                $utype = $emailArr[1];
                $token = $tokenArr[1];
                // dd($utype);
                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/reset-password';
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'token' => $request->reset_token,
                        'password' => $request->password,
                        'confirm_password' => $request->confirm_password,
                    ],
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);

                $notification = array(
                    'message' => $response['result']['message'],
                    'alert-type' => 'success',
                );
                return \Redirect::route('front_login')->with($notification);
            }
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
                return \Redirect::route('reset_newpassword', $request->reset_token)->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('reset_newpassword', $request->reset_token)->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /**
     * Function Name :  doForgotPasswordTenant
     * Purpose       :  This function use for forgot password for a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doForgotPasswordTenant(Request $request)
    {
        // dd($request->all());
        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        $Validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        try {
            if ($Validator->fails()) {
                return \Redirect::route('tenant_login', $subdomain)->withErrors($Validator);
            } else {
                $client = new Client();

                if ($request->usertype == 'TA') {
                    $apiEndpoint = config('app.api_base_url') . '/forgot-password';
                } else {
                    $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/forgot-password';
                }
                // dd($apiEndpoint);
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'email' => $request->email,
                    ],
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);

                $notification = array(
                    'message' => $response['result']['message'],
                    'alert-type' => 'success',
                );
                return \Redirect::route('tenant_login', $subdomain)->with($notification);
            }
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
                return \Redirect::route('tenant_login', $subdomain)->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_login', $subdomain)->with($notification);
            // throw ($e);
        }
    }

    /*
     * Function name : resetPasswordTenant
     * Purpose :
     * Author  :
     * Created Date :
     * Modified date :
     * Params : token
     * Return : void
     */
    public function resetPasswordTenant($subdomain, $encryptToken)
    {
        // dd($subdomain);
        try {
            $decryptToken = \Helpers::decryptId($encryptToken); //decrypt the token
            // $decryptToken = CommonHelper::decryptId($encryptToken); //decrypt the token
            $tokenArr = explode("_", $decryptToken);
            $emailArr = explode("~", $tokenArr[0]);
            $email = $emailArr[0];
            $utype = $emailArr[1];
            $token = $tokenArr[1];
            // dd($utype);
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-tenant-config';
            $call = $client->post($apiEndpoint, [
                'headers' => ['X_NEON' => config('app.api_key')],
                'form_params' => [
                    'subdomain' => $subdomain,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);

            Session()->put('tenant_info', $response['result']['tenant_info']);
            Session()->put('setting_info', $response['result']['setting_info']);
            Session()->put('tenant_short_name', $response['result']['tenant_short_name']);

            $data['token'] = $encryptToken;
            $data['subdomain'] = $subdomain;

            return view('login.tenant-reset-password', $data);
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * Function Name :  doResetPasswordTenant
     * Purpose       :  This function use for forgot password for a valid user.
     * Author        :
     * Created Date  :
     * Modified date :
     * Input Params  :  \Illuminate\Http\Request $request
     * Return Value  :  if user is valid then redirect to dashboard otherwise return to login form
     */
    public function doResetPasswordTenant(Request $request)
    {
        // dd($request->all());
        $Validator = Validator::make($request->all(), [
            'reset_token' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
        ]);

        try {
            if ($Validator->fails()) {
                return \Redirect::route('front_login')->withErrors($Validator);
            } else {
                $decryptToken = \Helpers::decryptId($request->reset_token); //decrypt the token
                // $decryptToken = CommonHelper::decryptId($encryptToken); //decrypt the token
                $tokenArr = explode("_", $decryptToken);
                $emailArr = explode("~", $tokenArr[0]);
                $email = $emailArr[0];
                $utype = $emailArr[1];
                $token = $tokenArr[1];

                // dd($utype);

                $client = new Client();
                if ($utype == 'TA') {
                    $apiEndpoint = config('app.api_base_url') . '/reset-password';
                } else {
                    $apiEndpoint = config('app.api_base_url') . '/' . $request->subdomain . '/reset-password';
                }
                // echo $apiEndpoint;
                $form_params = [
                    'token' => $request->reset_token,
                    'password' => $request->password,
                    'confirm_password' => $request->confirm_password,
                ];
                // dd(json_encode($form_params));
                $call = $client->post($apiEndpoint, [
                    'form_params' => $form_params,
                ]);

                // dd($call->getBody()->getContents());
                $response = json_decode($call->getBody()->getContents(), true);

                $notification = array(
                    'message' => $response['result']['message'],
                    'alert-type' => 'success',
                );
                return \Redirect::route('tenant_login', $request->subdomain)->with($notification);
            }
        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tenant_login', $request->subdomain)->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_login', $request->subdomain)->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

}
