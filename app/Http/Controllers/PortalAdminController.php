<?php
/*****************************************************
# PortalAdminController
# Class name : PortalAdminController
# Author :
# Created Date : 24-01-2024
# Functionality : Portal admin dashboard related logics
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

class PortalAdminController extends Controller
{
    use GeneralMethods;
    /*
     * Function name : index
     * Purpose : portal admin dashboard
     * Author  :
     * Created Date : 24-01-2024
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
        return view('portaladmin.index');
    }

    /*
     * Function name : myaccount
     * Purpose : portal admin myaccount
     * Author  :
     * Created Date : 29-01-2024
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
                    return \Redirect::route('pa_myaccount')->with($notification);

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
                    return \Redirect::route('pa_myaccount')->with($notification);

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
                        return \Redirect::route('pa_myaccount')->with($notification);
                        //}
                    }
                    // You can check for whatever error status code you need

                } catch (Exception $e) {
                    $notification = array(
                        'message' => $e->getMessage(),
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('pa_myaccount')->with($notification);
                    // throw ($e);
                    // throw new \App\Exceptions\AdminException($e->getMessage());
                }
            }
        }

        return view('portaladmin.myaccount', compact('userInfo', 'profileInfo', 'tenantInfo'));
    }

    /*
     * Function name : getBoardListing
     * Purpose : portal admin board listing
     * Author  :
     * Created Date : 30-01-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getBoardListing(Request $request)
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
            $apiEndpoint = config('app.api_base_url') . '/get-boards' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['boards']['total'];
                $current_page = $response['result']['boards']['current_page'];
                $prev_page_url = $response['result']['boards']['prev_page_url'];
                $next_page_url = $response['result']['boards']['next_page_url'];

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

            return view(
                'portaladmin.boards.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
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
                    return \Redirect::route('pa_boardlist');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_boardlist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addBoard
     * Purpose : portal admin board add view
     * Author  :
     * Created Date : 30-01-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addBoard()
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
        $client = new Client();
        $apiEndpoint = config('app.api_base_url') . '/dropdown/countries';
        $call = $client->post($apiEndpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $publicKey],

        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        $data['countries'] = $response['result']['listing'];
        // dd($data['countries']);
        return view('portaladmin.boards.add', $data);
    }

    /*
     * Function name : saveBoard
     * Purpose : portal admin board save
     * Author  :
     * Created Date : 31-01-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveBoard(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/create-board';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "board_name" => $request->board_name ?? '',
                    "short_name" => $request->short_name ?? '',
                    "country_id" => $request->country_id ?? '',
                    "description" => $request->description ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_boardlist')->with($notification);

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
                return \Redirect::route('pa_boardlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_boardlist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editBoard
     * Purpose : portal admin board edit view
     * Author  : SM
     * Created Date : 01-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function editBoard($board_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-board-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "board_id" => $board_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['board_details'] = $response['result']['board_details'];
            $apiEndpoint = config('app.api_base_url') . '/dropdown/countries';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['countries'] = $response['result']['listing'];

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
                return \Redirect::route('pa_boardlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_boardlist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.boards.edit', $data);
    }

    /*
     * Function name : updateBoard
     * Purpose : portal admin board update
     * Author  : SM
     * Created Date : 01-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateBoard(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/update-board';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "board_id" => $request->board_id ?? '',
                    "board_name" => $request->board_name ?? '',
                    "short_name" => $request->short_name ?? '',
                    "country_id" => $request->country_id ?? '',
                    "description" => $request->description ?? '',
                    "status" => $request->status ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_boardlist')->with($notification);

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
                return \Redirect::route('pa_boardlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_boardlist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTrusteeListing
     * Purpose : portal admin trustee listing
     * Author  :
     * Created Date : 01-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTrusteeListing(Request $request)
    {
        // dd($request->all());
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
            $apiEndpoint = config('app.api_base_url') . '/get-trustees' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['trustees']['last_page'];
                $current_page = $response['result']['trustees']['current_page'];
                $prev_page_url = $response['result']['trustees']['prev_page_url'];
                $next_page_url = $response['result']['trustees']['next_page_url'];

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

            return view(
                'portaladmin.trustees.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
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
                    return \Redirect::route('pa_trusteelist');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_trusteelist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addTrustee
     * Purpose : portal admin trustee add view
     * Author  :
     * Created Date : 08-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addTrustee()
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
        return view('portaladmin.trustees.add');
    }

    /*
     * Function name : saveTrustee
     * Purpose : portal admin trustee save
     * Author  :
     * Created Date : 08-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveTrustee(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/create-trustee';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "trustee_name" => $request->trustee_name ?? '',
                    "email" => $request->email ?? '',
                    "password" => $request->password ?? '',
                    "phone" => $request->phone ?? '',
                    "address" => $request->address ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_trusteelist')->with($notification);

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
                return \Redirect::route('pa_trusteelist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_trusteelist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editTrustee
     * Purpose : portal admin trustee edit view
     * Author  : SM
     * Created Date : 08-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function editTrustee($user_id)
    {
        // print_r($id);die();
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-trustee-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['trustee_details'] = $response['result']['trustee_details'];

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
                return \Redirect::route('pa_trusteelist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_trusteelist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.trustees.edit', $data);
    }

    /*
     * Function name : updateTrustee
     * Purpose : portal admin trustee update
     * Author  : SM
     * Created Date : 08-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateTrustee(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/update-trustee';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $request->user_id ?? '',
                    "trustee_name" => $request->trustee_name ?? '',
                    "phone" => $request->phone ?? '',
                    "address" => $request->address ?? '',
                    "status" => $request->status ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_trusteelist')->with($notification);

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
                return \Redirect::route('pa_trusteelist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_trusteelist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getSchoolListing
     * Purpose : portal admin school listing
     * Author  :
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getSchoolListing(Request $request)
    {
        // dd($request->all());
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
        $search_country_id = $request->search_country_id ?? '';
        // if ($request->isMethod('post')) {
        //     dd($request->all());
        // }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-schools';

            $form_params = [
                "page" => $page ?? '',
                "search_text" => $search_text ?? '',
                "search_country_id" => $search_country_id ?? '',
            ];

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['schools']['last_page'];
                $current_page = $response['result']['schools']['current_page'];
                $prev_page_url = $response['result']['schools']['prev_page_url'];
                $next_page_url = $response['result']['schools']['next_page_url'];

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

            return view(
                'portaladmin.schools.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text', 'search_country_id',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
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
                    return \Redirect::route('pa_schoollist');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_schoollist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addTrusteeFromSchool
     * Purpose : portal admin trustee add view
     * Author  :
     * Created Date : 08-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addTrusteeFromSchool()
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
        return view('portaladmin.trustees.add_from_school');
    }

    /*
     * Function name : addSchool
     * Purpose : portal admin school add view
     * Author  :
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addSchool()
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/salutations';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['salutations'] = $response['result']['listing'];
            // dd($data['year_group_batch_types']);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/countries';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['countries'] = $response['result']['listing'];
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
                return \Redirect::route('pa_schoollist')->with($notification);

                // dd($response->error->message);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        // dd($data);
        return view('portaladmin.schools.add', $data);
    }

    /*
     * Function name : saveSchool
     * Purpose : portal admin school save
     * Author  :
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveSchool(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $cp_options = array();

            $reqCpName = $request->cp_name ?? array();
            $reqCpSalutation = $request->cp_salutation ?? array();
            $reqCpEmail = $request->cp_email ?? array();
            $reqCpPhone = $request->cp_phone ?? array();
            // dd($reqCpName);
            for ($i = 0; $i < count($reqCpName); $i++) {
                $ele = [
                    "salutation" => $reqCpSalutation[$i],
                    "name" => $reqCpName[$i],
                    "email" => $reqCpEmail[$i],
                    "phone" => $reqCpPhone[$i],
                ];
                array_push($cp_options, $ele);

            }

            $tpoc_options = array();

            $reqTpocName = $request->tpoc_name ?? array();
            $reqTpocSalutation = $request->tpoc_salutation ?? array();
            $reqTpocEmail = $request->tpoc_email ?? array();
            $reqTpocPhone = $request->tpoc_phone ?? array();
            // dd($reqTpocName);
            for ($i = 0; $i < count($reqTpocName); $i++) {
                $ele = [
                    "salutation" => $reqTpocSalutation[$i],
                    "name" => $reqTpocName[$i],
                    "email" => $reqTpocEmail[$i],
                    "phone" => $reqTpocPhone[$i],
                ];
                array_push($tpoc_options, $ele);

            }

            $csc_options = array();

            $reqCscName = $request->csc_name ?? array();
            $reqCscSalutation = $request->csc_salutation ?? array();
            $reqCscEmail = $request->csc_email ?? array();
            $reqCscPhone = $request->csc_phone ?? array();
            // dd($reqCscName);
            for ($i = 0; $i < count($reqCscName); $i++) {
                $ele = [
                    "salutation" => $reqCscSalutation[$i],
                    "name" => $reqCscName[$i],
                    "email" => $reqCscEmail[$i],
                    "phone" => $reqCscPhone[$i],
                ];
                array_push($csc_options, $ele);

            }

            $bc_options = array();

            $reqBcName = $request->bc_name ?? array();
            $reqBcSalutation = $request->bc_salutation ?? array();
            $reqBcEmail = $request->bc_email ?? array();
            $reqBcPhone = $request->bc_phone ?? array();
            // dd($reqBccName);
            for ($i = 0; $i < count($reqBcName); $i++) {
                $ele = [
                    "salutation" => $reqBcSalutation[$i],
                    "name" => $reqBcName[$i],
                    "email" => $reqBcEmail[$i],
                    "phone" => $reqBcPhone[$i],
                ];
                array_push($bc_options, $ele);

            }

            $formParams = [
                "school_name" => $request->school_name ?? '',
                "short_name" => $request->short_name ?? '',
                "subdomain" => $request->subdomain ?? '',
                "country_id" => $request->country_id ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "phone" => $request->phone ?? '',
                "address" => $request->address ?? '',
                "trustee_id" => $request->trustee_id ?? '',
                "logo" => $request->imagedata_logo ?? '',
                "background_image" => $request->imagedata_bg ?? '',
                "customer_name" => $request->customer_name ?? '',
                "company_address" => $request->company_address ?? '',
                "contact_persons" => $cp_options ?? '',
                "technical_poc" => $tpoc_options ?? '',
                "customer_service_contact" => $csc_options ?? '',
                "billing_contact" => $bc_options ?? '',
            ];
            // dd(json_encode($formParams));
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/create-school';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_schoollist')->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message ?? 'Unknown API exception',
                    'alert-type' => 'error',
                );

                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage() ?? 'Null exception',
                'alert-type' => 'error',
            );
            // throw ($e);
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editSchool
     * Purpose : portal admin school edit view
     * Author  : SM
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function editSchool($user_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-school-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['school_details'] = $response['result']['school_details'];
            $apiEndpoint = config('app.api_base_url') . '/dropdown/salutations';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['salutations'] = $response['result']['listing'];
            $apiEndpoint = config('app.api_base_url') . '/dropdown/countries';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['countries'] = $response['result']['listing'];
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
                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.schools.edit', $data);
    }

    /*
     * Function name : updateSchool
     * Purpose : portal admin school update
     * Author  : SM
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateSchool(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $cp_options = array();

            $reqCpName = $request->cp_name ?? array();
            $reqCpSalutation = $request->cp_salutation ?? array();
            $reqCpEmail = $request->cp_email ?? array();
            $reqCpPhone = $request->cp_phone ?? array();
            // dd($reqCpName);
            for ($i = 0; $i < count($reqCpName); $i++) {
                $ele = [
                    "salutation" => $reqCpSalutation[$i],
                    "name" => $reqCpName[$i],
                    "email" => $reqCpEmail[$i],
                    "phone" => $reqCpPhone[$i],
                ];
                array_push($cp_options, $ele);

            }

            $tpoc_options = array();

            $reqTpocName = $request->tpoc_name ?? array();
            $reqTpocSalutation = $request->tpoc_salutation ?? array();
            $reqTpocEmail = $request->tpoc_email ?? array();
            $reqTpocPhone = $request->tpoc_phone ?? array();
            // dd($reqTpocName);
            for ($i = 0; $i < count($reqTpocName); $i++) {
                $ele = [
                    "salutation" => $reqTpocSalutation[$i],
                    "name" => $reqTpocName[$i],
                    "email" => $reqTpocEmail[$i],
                    "phone" => $reqTpocPhone[$i],
                ];
                array_push($tpoc_options, $ele);

            }

            $csc_options = array();

            $reqCscName = $request->csc_name ?? array();
            $reqCscSalutation = $request->csc_salutation ?? array();
            $reqCscEmail = $request->csc_email ?? array();
            $reqCscPhone = $request->csc_phone ?? array();
            // dd($reqCscName);
            for ($i = 0; $i < count($reqCscName); $i++) {
                $ele = [
                    "salutation" => $reqCscSalutation[$i],
                    "name" => $reqCscName[$i],
                    "email" => $reqCscEmail[$i],
                    "phone" => $reqCscPhone[$i],
                ];
                array_push($csc_options, $ele);

            }

            $bc_options = array();

            $reqBcName = $request->bc_name ?? array();
            $reqBcSalutation = $request->bc_salutation ?? array();
            $reqBcEmail = $request->bc_email ?? array();
            $reqBcPhone = $request->bc_phone ?? array();
            // dd($reqBccName);
            for ($i = 0; $i < count($reqBcName); $i++) {
                $ele = [
                    "salutation" => $reqBcSalutation[$i],
                    "name" => $reqBcName[$i],
                    "email" => $reqBcEmail[$i],
                    "phone" => $reqBcPhone[$i],
                ];
                array_push($bc_options, $ele);

            }

            $formParams = [
                "user_id" => $request->user_id ?? '',
                "school_name" => $request->school_name ?? '',
                "short_name" => $request->short_name ?? '',
                "country_id" => $request->country_id ?? '',
                "phone" => $request->phone ?? '',
                "address" => $request->address ?? '',
                "phone" => $request->phone ?? '',
                "trustee_id" => $request->trustee_id ?? '',
                "logo" => $request->imagedata_logo ?? '',
                "background_image" => $request->imagedata_bg ?? '',
                "customer_name" => $request->customer_name ?? '',
                "company_address" => $request->company_address ?? '',
                "status" => $request->status ?? '',
                "contact_persons" => $cp_options ?? '',
                "technical_poc" => $tpoc_options ?? '',
                "customer_service_contact" => $csc_options ?? '',
                "billing_contact" => $bc_options ?? '',
            ];

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/update-school';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_schoollist')->with($notification);

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
                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : schoolSubscriptions
     * Purpose : portal admin school subscription list
     * Author  : SM
     * Created Date : 17-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function schoolSubscriptions($user_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        $data['user_id'] = $user_id;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-school-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['school_details'] = $response['result']['school_details'];
            // dd($data['school_details']['tenant_id']);
            $tenant_id = CommonHelper::encryptId($data['school_details']['tenant_id']);
            // dd($tenant_id);
            $apiEndpoint = config('app.api_base_url') . '/get-subscribed-plans';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "tenant_id" => $tenant_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['plans'] = $response['result']['plans'];
            // dd($data);

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
                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.schools.subscriptions', $data);
    }

    /*
     * Function name : subscribePlan
     * Purpose : portal admin school subscribe plan view
     * Author  : SM
     * Created Date : 17-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function subscribePlan($user_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        $data['user_id'] = $user_id;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-school-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['school_details'] = $response['result']['school_details'];
            $apiEndpoint = config('app.api_base_url') . '/get-plans-to-subscribe';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['plans'] = $response['result']['plans'];
            $apiEndpoint = config('app.api_base_url') . '/get-modules';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['module_list'] = $response['result']['list'];

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
                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.schools.subscribe', $data);
    }

    /*
     * Function name : planSubscribe
     * Purpose : portal admin school subscribe plan view
     * Author  : SM
     * Created Date : 17-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function planSubscribe($user_id, $plan_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        $data['user_id'] = $user_id;
        $data['plan_id'] = $plan_id;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-school-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $user_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['school_details'] = $response['result']['school_details'];
            $apiEndpoint = config('app.api_base_url') . '/get-subscription-plan-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "subscription_plan_id" => $plan_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['plan_details'] = $response['result']['plan_details'];

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
                return \Redirect::route('pa_schoollist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoollist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.schools.subscribe-plan', $data);
    }

    /*
     * Function name : doPlanSubscribe
     * Purpose : portal admin school subscribe plan save
     * Author  :
     * Created Date : 17-06-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function doPlanSubscribe(Request $request)
    {
        // dd($request->all());
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/subscribe-plan';
            $form_params = [
                "user_id" => $request->user_id ?? '',
                "tenant_id" => $request->tenant_id ?? '',
                "subscription_plan_id" => $request->subscription_plan_id ?? '',
                "start_date" => date('Y-m-d'),
            ];

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
            $this->refreshLoginData();
            return \Redirect::route('pa_schoolsubscriptions', \Helpers::encryptId($request->user_id))->with($notification);

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
                return \Redirect::route('pa_schoolsubscriptions', \Helpers::encryptId($request->user_id))->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_schoolsubscriptions', \Helpers::encryptId($request->user_id))->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : settings
     * Purpose : portal admin settings
     * Author  :
     * Created Date : 14-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function settings()
    {
        // dd($request->all());
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/pa/settings';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response['result']['settings']);

            return view('portaladmin.settings', compact('response', 'userInfo', 'profileInfo', 'tenantInfo'));

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('pa_settings');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_settings')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : doSettings
     * Purpose : portal admin save settings
     * Author  :
     * Created Date : 14-02-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function doSettings(Request $request)
    {
        // dd($request->all());
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

        try {
            $mailSettings = [
                'smtp_host' => $request->smtp_host ?? '',
                'smtp_port' => $request->smtp_port ?? '',
                'smtp_security' => $request->smtp_security ?? '',
                'smtp_username' => $request->smtp_username ?? '',
                'smtp_password' => $request->smtp_password ?? '',
            ];
            $mailSettings = json_encode($mailSettings);
            $mailSettings = CommonHelper::encryptId($mailSettings);

            $formParams = [
                "setting_id" => $request->setting_id ?? '',
                "tenant_id" => $userInfo->tenant_id ?? '',
                "system_title" => $request->system_title ?? '',
                "system_email" => $request->system_email ?? '',
                "address" => $request->address ?? '',
                "phone" => $request->phone ?? '',
                "footer_text" => $request->footer_text ?? '',
                "footer_link" => $request->footer_link ?? '',
                "mail_settings" => $mailSettings ?? '',
                "favicon" => $request->imagedata_favicon ?? '',
                "main_logo" => $request->imagedata_main_logo ?? '',
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/pa/set-settings';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_settings')->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('pa_settings');
                }
                // throw ($e);
                return \Redirect::route('front_logout')->withErrors($response->error->message ?? 'Unknown api exception');
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_settings')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getSubscriptionListing
     * Purpose : portal  Subscription listing
     * Author  :
     * Created Date : 13-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getSubscriptionListing(Request $request)
    {
        // dd($request->all());
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
            $apiEndpoint = config('app.api_base_url') . '/get-subscription-plans' . '?search_text=' . $search_text . '&page=' . $page;

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            //  dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['plans']['last_page'];
                $current_page = $response['result']['plans']['current_page'];
                $prev_page_url = $response['result']['plans']['prev_page_url'];
                $next_page_url = $response['result']['plans']['next_page_url'];

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

            return view(
                'portaladmin.subscription_plan.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
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
                    return \Redirect::route('pa_subscriptionlist');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_subscriptionlist')->withErrors($e->getMessage());
        }
        // return view('portaladmin.subscriptions.index');
    }

    /*
     * Function name : addSubscriptionPlan
     * Purpose : portal admin subscription plan add view
     * Author  :
     * Created Date : 14-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addSubscriptionPlan()
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
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-modules';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['module_list'] = $response['result']['list'];

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
                return \Redirect::route('pa_subscriptionplanlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_subscriptionplanlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('portaladmin.subscription_plan.add', $data);
    }

    /*
     * Function name : saveSubscriptionPlan
     * Purpose : portal admin subscription plan save
     * Author  :
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveSubscriptionPlan(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $features_available = '';
            $module_list = $request->module_list ?? '';
            if ($module_list != '') {
                $module_list = json_decode($module_list);
                $feature = array();
                foreach ($module_list as $key => $module) {
                    // dd($module->sub_modules);
                    foreach ($module->sub_modules as $subkey => $submodule) {
                        //dd($subkey . '==' . $request->input($subkey));
                        $feature[$subkey] = $request->input($subkey);
                        if ($feature[$subkey] == null) {
                            $feature[$subkey] = "0";
                        }
                    }
                }
                // dd($feature);
                $features_available = json_encode($feature);
            }

            $formParams = [
                "plan_name" => $request->plan_name ?? '',
                "base_price" => $request->base_price ?? '',
                "tax_percentage" => $request->tax_percentage ?? '',
                "validity_indays" => $request->validity_indays ?? '',
                "description" => $request->description ?? '',
                "features_available" => $features_available ?? '',
                "users_allowed" => $request->users_allowed ?? 0,
            ];
            // dd(json_encode($formParams));
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/create-subscription-plan';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_subscriptionplanlist')->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message ?? 'Unknown API exception',
                    'alert-type' => 'error',
                );

                return \Redirect::route('pa_subscriptionplanlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage() ?? 'Null exception',
                'alert-type' => 'error',
            );
            // throw ($e);
            return \Redirect::route('pa_subscriptionplanlist')->with($notification);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editSubscriptionPlan
     * Purpose : portal admin subscription plan edit view
     * Author  : SM
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function editSubscriptionPlan($subscription_plan_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-modules';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['module_list'] = $response['result']['list'];

            $apiEndpoint = config('app.api_base_url') . '/get-subscription-plan-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "subscription_plan_id" => $subscription_plan_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $data['plan_details'] = $response['result']['plan_details'];

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
                return \Redirect::route('pa_subscriptionplanlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_subscriptionplanlist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.subscription_plan.edit', $data);
    }

    /*
     * Function name : updateSubscriptionPlan
     * Purpose : portal admin subscription plan update
     * Author  : SM
     * Created Date : 09-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateSubscriptionPlan(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $features_available = '';
            $module_list = $request->module_list ?? '';
            if ($module_list != '') {
                $module_list = json_decode($module_list);
                $feature = array();
                foreach ($module_list as $key => $module) {
                    // dd($module->sub_modules);
                    foreach ($module->sub_modules as $subkey => $submodule) {
                        //dd($subkey . '==' . $request->input($subkey));
                        $feature[$subkey] = $request->input($subkey);
                        if ($feature[$subkey] == null) {
                            $feature[$subkey] = "0";
                        }
                    }
                }
                // dd($feature);
                $features_available = json_encode($feature);
            }

            $formParams = [
                "subscription_plan_id" => $request->subscription_plan_id ?? '',
                "plan_name" => $request->plan_name ?? '',
                "base_price" => $request->base_price ?? '',
                "tax_percentage" => $request->tax_percentage ?? '',
                "validity_indays" => $request->validity_indays ?? '',
                "description" => $request->description ?? '',
                "status" => $request->status ?? '',
                "features_available" => $features_available ?? '',
                "users_allowed" => $request->users_allowed ?? 0,
            ];
            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/update-subscription-plan';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            // print_r($response);die();
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_subscriptionplanlist')->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
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
                return \Redirect::route('pa_subscriptionplanlist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            throw ($e);
            return \Redirect::route('pa_subscriptionplanlist')->with($notification);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getCountryListing
     * Purpose : portal  Country listing
     * Author  :
     * Created Date : 25-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getCountryListing()
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-countries';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            return view(
                'portaladmin.country.index', compact('response'));

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('pa_countrylist');
                }
                return \Redirect::route('front_logout')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('pa_countrylist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : editCountry
     * Purpose : portal admin country edit view
     * Author  : SM
     * Created Date : 25-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function editCountry($country_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($data);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/get-country-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "country_id" => $country_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/get-currencies';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['currencies'] = $response['result']['list'];

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
                return \Redirect::route('pa_countrylist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_countrylist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('portaladmin.country.edit', $data);
    }

    /*
     * Function name : updateCountry
     * Purpose : portal admin country update
     * Author  : SM
     * Created Date : 25-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateCountry(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_login')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/update-country';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "country_id" => $request->country_id ?? '',
                    "name" => $request->name ?? '',
                    "code" => $request->code ?? '',
                    "currency_code" => $request->currency_code ?? '',
                    "status" => $request->status ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('pa_countrylist')->with($notification);

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
                return \Redirect::route('pa_countrylist')->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('pa_countrylist')->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

}
