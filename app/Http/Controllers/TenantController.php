<?php
/*****************************************************
# TenantController
# Class name : TenantController
# Author :
# Created Date : 24-01-2024
# Functionality : Tenant  related logics
/*****************************************************/
namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Traits\GeneralMethods;
use Exception;
use File;
use GlobalVars;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

class TenantController extends Controller
{
    use GeneralMethods;

    public function __construct()
    {
        //echo bcrypt('123456');
    }

    /*
     * Function name : index
     * Purpose : tenant admin dashboard
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
            return \Redirect::route('front_flush')->with($notification);
        }

        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        // dd(Session()->get('user'));
        try {
            switch (Session()->get('user')['role']) {
                case 'A':
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/home';

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        //'body' => json_encode($data),
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);

                    return view('tenant.index', compact('response'));
                    break;
                case 'S':
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime($startDate . ' + 7 days'));

                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/get-profile-completion';

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        //'body' => json_encode($data),
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    $data['profile_completion'] = $response['result']['completion'];
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/async';

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => [
                            "start_date" => $startDate,
                            "end_date" => $endDate,
                        ],
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);
                    $data['upcoming_tasks'] = $response['result']['listing'];
                    return view('tenant.index-student', $data);
                    break;
                case 'T':
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime($startDate . ' + 7 days'));
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/async';

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => [
                            "start_date" => $startDate,
                            "end_date" => $endDate,
                        ],
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);
                    $data['upcoming_tasks'] = $response['result']['listing'];
                    return view('tenant.index-teacher', $data);
                    break;
                case 'TA':
                    return view('tenant.index-teacher-assistant');
                    break;
                case 'P':
                    return view('tenant.index-parent');
                    break;
                default:
                    return view('tenant.default');
            }

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // dd($e->getResponse());
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";

                return \Redirect::route('tenant_login', $subdomain)->withErrors($response->error->message);
                // }
                // dd($e->getResponse());
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            return \Redirect::route('tenant_login', $subdomain)->withErrors($e->getMessage());
            // throw ($e);
        }
    }

    public function getChartOfsted(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        // dd(Session()->get('user'));
        try {

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/ofstead/get-finance-expenditure';

            $form_params = [
                "ofsted_show_group" => $request->ofsted_show_group ?? '',
                "ofsted_show_value" => $request->ofsted_show_value ?? '',
            ];

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);

            $ofsted = json_decode($call->getBody()->getContents(), true);
            $ofsted_xvalues = array();
            $ofsted_yvalues = array();
            // dd($ofsted);

            foreach ($ofsted['result']['listing'] as $rec) {
                // dd($rec);
                $ofsted_xvalues[] = $rec['xvalue'];
                $ofsted_yvalues[] = $rec['yvalue'];
            }
            // dd($ofsted_xvalues);

            $ofsted_xvalues = implode(',', $ofsted_xvalues);
            $ofsted_yvalues = implode(',', $ofsted_yvalues);

            $xlabel = "Absolute Total";
            switch ($request->ofsted_show_value) {
                case 'number_of_pupils':
                    $xlabel = "Per Pupils";
                    $yprefix = '£';
                    $ysuffix = 'k';
                    break;
                case 'number_of_teachers':
                    $xlabel = "Per Teachers";
                    $yprefix = '£';
                    $ysuffix = 'k';
                    break;
                default:
                    $xlabel = "Absolute Total";
                    $yprefix = '£';
                    $ysuffix = 'm';
            }
            $ylabel = "Total Expenditure";
            switch ($request->ofsted_show_group) {
                case 'staff_total':
                    $ylabel = "Total Staff";
                    break;
                case 'occupation_total':
                    $ylabel = "Occupation Total";
                    break;
                case 'premises_total':
                    $ylabel = "Premises Total";
                    break;
                case 'supplies_and_services_total':
                    $ylabel = "Supplies & Services Total";
                    break;
                case 'special_facilities_total':
                    $ylabel = "Special Facilities Total";
                    break;
                case 'cost_of_finance_total':
                    $ylabel = "Cost of finance total";
                    break;
                case 'community_expenditure_total':
                    $ylabel = "Community Expenditure Total";
                    break;
                case 'total_income':
                    $ylabel = "Total Income";
                    break;
                case 'grant_funding_total':
                    $ylabel = "Grant funding total";
                    break;
                case 'self_generated_funding_total':
                    $ylabel = "Self-generated funding total";
                    break;

                default:
                    $ylabel = "Total Expenditure";
            }

            $reponse = ['status' => true, 'statuscode' => '200', 'result' => ['message' => 'Ofstead finance exenditure fetched successfully.', 'ofsted_xvalues' => $ofsted_xvalues, 'ofsted_yvalues' => $ofsted_yvalues, 'xlabel' => $xlabel, 'ylabel' => $ylabel, 'yprefix' => $yprefix, 'ysuffix' => $ysuffix], 'error' => []];

            return response()->json($reponse, 200);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // throw ($e);
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // dd($e->getResponse());
                $responseapi = json_decode($e->getResponse()->getBody()->getContents());
                // if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";

                // return \Redirect::route('tenant_login', $subdomain)->withErrors($response->error->message);
                // }
                // dd($e->getResponse());
                $reponse = ['status' => false, 'statuscode' => '400', 'result' => [], 'error' => ['message' => $responseapi->error->message ?? 'error happened.']];
                return response()->json($reponse, 400);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            // return \Redirect::route('tenant_login', $subdomain)->withErrors($e->getMessage());
            $reponse = ['status' => false, 'statuscode' => '500', 'result' => [], 'error' => ['message' => $e->getMessage()]];
            return response()->json($reponse, 400);
        }
    }

    public function getChartTeacherAttendance(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        // dd(Session()->get('user'));
        try {

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/attendance-graph-data';

            // $form_params = [
            //     "ofsted_show_group" => $request->ofsted_show_group ?? '',
            //     "ofsted_show_value" => $request->ofsted_show_value ?? '',
            // ];

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                // 'form_params' => $form_params,
            ]);

            $ofsted = json_decode($call->getBody()->getContents(), true);
            $date_xvalues = array();
            $present_yvalues = array();
            $enroll_yvalues = array();
            $absent_yvalues = array();
            // dd($ofsted);

            foreach ($ofsted['result']['listing'] as $rec) {
                // dd($rec);
                $date_xvalues[] = $rec['attendance_date'];
                $present_yvalues[] = $rec['total_present'];
                $absent_yvalues[] = $rec['total_absent'];
                $enroll_yvalues[] = $rec['total_enrollment'];
            }
            // dd($ofsted_xvalues);

            $date_xvalues = implode(',', $date_xvalues);
            $present_yvalues = implode(',', $present_yvalues);
            $absent_yvalues = implode(',', $absent_yvalues);
            $enroll_yvalues = implode(',', $enroll_yvalues);

            $xlabel = "Date";
            // switch ($request->ofsted_show_value) {
            //     case 'number_of_pupils':
            //         $xlabel = "Per Pupils";
            //         $yprefix = '£';
            //         $ysuffix = 'k';
            //         break;
            //     case 'number_of_teachers':
            //         $xlabel = "Per Teachers";
            //         $yprefix = '£';
            //         $ysuffix = 'k';
            //         break;
            //     default:
            //         $xlabel = "Absolute Total";
            //         $yprefix = '£';
            //         $ysuffix = 'm';
            // }
            $ylabel = "Total Present";
            // switch ($request->ofsted_show_group) {
            //     case 'staff_total':
            //         $ylabel = "Total Staff";
            //         break;
            //     case 'occupation_total':
            //         $ylabel = "Occupation Total";
            //         break;
            //     case 'premises_total':
            //         $ylabel = "Premises Total";
            //         break;
            //     case 'supplies_and_services_total':
            //         $ylabel = "Supplies & Services Total";
            //         break;
            //     case 'special_facilities_total':
            //         $ylabel = "Special Facilities Total";
            //         break;
            //     case 'cost_of_finance_total':
            //         $ylabel = "Cost of finance total";
            //         break;
            //     case 'community_expenditure_total':
            //         $ylabel = "Community Expenditure Total";
            //         break;
            //     case 'total_income':
            //         $ylabel = "Total Income";
            //         break;
            //     case 'grant_funding_total':
            //         $ylabel = "Grant funding total";
            //         break;
            //     case 'self_generated_funding_total':
            //         $ylabel = "Self-generated funding total";
            //         break;

            //     default:
            //         $ylabel = "Total Expenditure";
            // }
            $yprefix = '';
            $ysuffix = '';
            $reponse = ['status' => true, 'statuscode' => '200', 'result' => ['message' => 'Attendance data fetched successfully.', 'date_xvalues' => $date_xvalues, 'present_yvalues' => $present_yvalues, 'absent_yvalues' => $absent_yvalues, 'enroll_yvalues' => $enroll_yvalues, 'xlabel' => $xlabel, 'ylabel' => $ylabel, 'yprefix' => $yprefix, 'ysuffix' => $ysuffix], 'error' => []];

            return response()->json($reponse, 200);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // throw ($e);
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // dd($e->getResponse());
                $responseapi = json_decode($e->getResponse()->getBody()->getContents());
                // if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";

                // return \Redirect::route('tenant_login', $subdomain)->withErrors($response->error->message);
                // }
                // dd($e->getResponse());
                $reponse = ['status' => false, 'statuscode' => '400', 'result' => [], 'error' => ['message' => $responseapi->error->message ?? 'error happened.']];
                return response()->json($reponse, 400);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            // return \Redirect::route('tenant_login', $subdomain)->withErrors($e->getMessage());
            $reponse = ['status' => false, 'statuscode' => '500', 'result' => [], 'error' => ['message' => $e->getMessage()]];
            return response()->json($reponse, 400);
        }
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
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        // dd($userInfo);
        if ($request->isMethod('post')) {
            // dd($tenantInfo);
            //Update profile information
            if (isset($_POST['btnUpdateProfile'])) {
                try {
                    $client = new Client();
                    if ($userInfo['user_type'] == 'TU' || $userInfo['user_type'] == 'P') {
                        $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/user/update';
                    } else {
                        $apiEndpoint = config('app.api_base_url') . '/user/update';
                    }
                    $form_params = [
                        "user_id" => $userInfo['user_id'],
                        "first_name" => $request->first_name ?? '',
                        "middle_name" => $request->middle_name ?? '',
                        "last_name" => $request->last_name ?? '',
                        "address" => $request->address ?? '',
                        "phone" => $request->phone ?? '',
                        "profile_image" => $request->imagedata_profile_image ?? '',
                    ];
                    // dd($apiEndpoint);
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
                    return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                        return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);
                        //}
                    }
                    // You can check for whatever error status code you need

                } catch (Exception $e) {
                    $notification = array(
                        'message' => $e->getMessage(),
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);
                    // throw ($e);
                    // throw new \App\Exceptions\AdminException($e->getMessage());
                }
            }

            //change password
            if (isset($_POST['btnChangePassword'])) {
                try {
                    $client = new Client();
                    // $apiEndpoint = config('app.api_base_url') . '/user/change-password';
                    if ($userInfo['user_type'] == 'TU' || $userInfo['user_type'] == 'P') {
                        $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/user/change-password';
                    } else {
                        $apiEndpoint = config('app.api_base_url') . '/user/change-password';
                    }
                    // dd($apiEndpoint);
                    $form_params = [
                        "current_password" => $request->current_password ?? '',
                        "new_password" => $request->new_password ?? '',
                        "confirm_password" => $request->confirm_password ?? '',
                    ];
                    // dd(json_encode($form_params));
                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    Session()->put('userl', $response['result']['credentials']);
                    // dd($response);
                    $notification = array(
                        'message' => 'Password changed successful.',
                        'alert-type' => 'success',
                    );
                    $this->refreshLoginData();
                    return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                        return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);
                        //}
                    }
                    // You can check for whatever error status code you need

                } catch (Exception $e) {
                    $notification = array(
                        'message' => $e->getMessage(),
                        'alert-type' => 'error',
                    );
                    return \Redirect::route('myaccount', Session()->get('tenant_info')['subdomain'])->with($notification);
                    // throw ($e);
                    // throw new \App\Exceptions\AdminException($e->getMessage());
                }
            }
        }
        if ($userInfo['user_type'] == 'TA' && $userInfo['role'] == 'A') {
            return view('tenant.myaccountta', compact('userInfo', 'profileInfo', 'tenantInfo'));
        } else {
            return view('tenant.myaccount', compact('userInfo', 'profileInfo', 'tenantInfo'));
        }
    }

    /*
     * Function name : settings
     * Purpose : tenant admin settings
     * Author  :
     * Created Date : 14-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function settings()
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/settings';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            return view('tenant.settings', compact('response', 'userInfo', 'profileInfo', 'tenantInfo'));

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('settings', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('settings', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : doSettings
     * Purpose : tenant admin save settings
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
            return \Redirect::route('front_flush')->with($notification);
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
                "logo" => $request->imagedata_logo ?? '',
                "background_image" => $request->imagedata_bg ?? '',
                "theme_color" => $request->theme_color ?? '',
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/set-settings';
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
            return \Redirect::route('settings', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('settings', Session()->get('tenant_info')['subdomain']);
                }
                // throw ($e);
                return \Redirect::route('front_flush')->withErrors($response->error->message ?? 'Unknown api exception');
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('settings', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getAcademicYearListing
     * Purpose : tenant admin academic year listing
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getAcademicYearListing(Request $request)
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
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/academic-years' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['academic_years']['last_page'];
                $current_page = $response['result']['academic_years']['current_page'];
                $prev_page_url = $response['result']['academic_years']['prev_page_url'];
                $next_page_url = $response['result']['academic_years']['next_page_url'];

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
                'tenant.academic_year.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_academicyearlist');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_academicyearlist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addAcademicYear
     * Purpose : tenant admin academic year add view
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addAcademicYear()
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
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        return view('tenant.academic_year.add');

    }

    /*
     * Function name : saveAcademicYear
     * Purpose : tenant admin academic year save
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveAcademicYear(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-academic-year';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "start_year" => $request->start_year ?? '',
                    "end_year" => $request->end_year ?? '',
                    "academic_year" => $request->academic_year ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editAcademicYear
     * Purpose : tenant admin academic year edit view
     * Author  : SM
     * Created Date : 22-02-2024
     * Modified date :
     * Params : academic_year_id
     * Return : void
     */
    public function editAcademicYear($subdomain, $academic_year_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($academic_year_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-academic-year-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "academic_year_id" => $academic_year_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

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
                return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.academic_year.edit', $data);
    }

    /*
     * Function name : updateAcademicYear
     * Purpose : tenant admin academic year update
     * Author  : SM
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateAcademicYear(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-academic-year';
            $form_params = [
                "academic_year_id" => $request->academic_year_id ?? '',
                "academic_year" => $request->academic_year ?? '',
                "start_year" => $request->start_year ?? '',
                "end_year" => $request->end_year ?? '',
                "status" => $request->status ?? '',
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
            return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getYearGroupListing
     * Purpose : tenant admin year group listing
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getYearGroupListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/year-groups' . '?search_text=' . $search_text . '&search_academic_year=' . $sayid . '&page=' . $page;

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
                $numOfpages = $response['result']['year_groups']['last_page'];
                $current_page = $response['result']['year_groups']['current_page'];
                $prev_page_url = $response['result']['year_groups']['prev_page_url'];
                $next_page_url = $response['result']['year_groups']['next_page_url'];

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
                'tenant.year_group.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text', 'sayid',
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
                    return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addYearGroup
     * Purpose : tenant admin year group add view
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addYearGroup()
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
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-batch-types';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['year_group_batch_types'] = $response['result']['batch_types'];
            // dd($data['year_group_batch_types']);

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
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

                // dd($response->error->message);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.year_group.add', $data);

    }

    /*
     * Function name : saveYearGroup
     * Purpose : tenant admin year group save
     * Author  :
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveYearGroup(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $batchTypes = json_decode($request->batchtypes);
            $one_one = 0;
            $group = 0;
            foreach ($batchTypes as $bt) {
                $chkname = "chk_" . CommonHelper::encryptId($bt->name);
                if ($bt->name == 'one:one' && isset($_REQUEST[$chkname])) {
                    $one_one = 1;
                }
                if ($bt->name == 'group' && isset($_REQUEST[$chkname])) {
                    $group = 1;
                }
            }

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-year-group';
            $form_params = [
                "name" => $request->name ?? '',
                "academic_year_id" => $request->academic_year_id ?? '',
                "one_one" => $one_one,
                "group" => $group,
            ];
            // print_r($request->all());
            // dd($form_params);
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
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editYearGroup
     * Purpose : tenant admin year group edit view
     * Author  : SM
     * Created Date : 22-02-2024
     * Modified date :
     * Params : academic_year_id
     * Return : void
     */
    public function editYearGroup($subdomain, $year_group_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($academic_year_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        // $checkAccess = $this->checkAccess('academics');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-year-group-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "year_group_id" => $year_group_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['year_group_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-batch-types';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['year_group_batch_types'] = $response['result']['batch_types'];

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
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

        return view('tenant.year_group.edit', $data);
    }

    /*
     * Function name : updateYearGroup
     * Purpose : tenant admin year group update
     * Author  : SM
     * Created Date : 22-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateYearGroup(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $batchTypes = json_decode($request->batchtypes);
            $one_one = 0;
            $group = 0;
            foreach ($batchTypes as $bt) {
                $chkname = "chk_" . CommonHelper::encryptId($bt->name);
                if ($bt->name == 'one:one' && isset($_REQUEST[$chkname])) {
                    $one_one = 1;
                }
                if ($bt->name == 'group' && isset($_REQUEST[$chkname])) {
                    $group = 1;
                }
            }

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-year-group';
            $form_params = [
                "year_group_id" => $request->year_group_id ?? '',
                "name" => $request->name ?? '',
                "academic_year_id" => $request->academic_year_id ?? '',
                "one_one" => $one_one,
                "group" => $group,
                "status" => $request->status ?? '',
            ];
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
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importYearGroup
     * Purpose : tenant admin year group import view
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importYearGroup()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.year_group.import');

    }

    /*
     * Function name : saveImportYearGroup
     * Purpose : tenant admin year group save import
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportYearGroup(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-year-groups';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'academic_year_id',
                        'contents' => $request->academic_year_id ?? '',
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getSubjectListing
     * Purpose : tenant admin subject listing
     * Author  :
     * Created Date : 26-02-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getSubjectListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        // dd($publicKey);
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('subject');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sbid = $request->sbid ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/subjects';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_board_id" => $sbid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['subject_list']['last_page'];
                $current_page = $response['result']['subject_list']['current_page'];
                $prev_page_url = $response['result']['subject_list']['prev_page_url'];
                $next_page_url = $response['result']['subject_list']['next_page_url'];

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
            $boards = Session()->get('datalist_shortboards');
            return view(
                'tenant.subject.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'sayid', 'sygid', 'boards', 'sbid'
                )
            );

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addSubject
     * Purpose : tenant admin subject add view
     * Author  :
     * Created Date : 26-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addSubject()
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
        $checkAccess = $this->checkAccess('subject');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['boards'] = $response['result']['boards'];
            // dd($data['boards']);

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
                return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);

                // dd($response->error->message);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_yeargrouplist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.subject.add', $data);

    }

    /*
     * Function name : saveSubject
     * Purpose : tenant admin year group save
     * Author  :
     * Created Date : 26-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveSubject(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-subject';
            $form_params = [
                "academic_year_id" => $request->academic_year_id ?? '',
                "board_id" => $request->board_id ?? '',
                "year_group_id" => $request->year_group_id,
                "subject_name" => $request->subject_name,
                "description" => $request->description,
                "subject_image" => $request->imagedata_subject_image ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editSubject
     * Purpose : tenant admin subject edit view
     * Author  : SM
     * Created Date : 26-02-2024
     * Modified date :
     * Params : subject_id
     * Return : details
     */
    public function editSubject($subdomain, $subject_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('subject');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-subject-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "subject_id" => $subject_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['boards'] = $response['result']['boards'];

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
                return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.subject.edit', $data);
    }

    /*
     * Function name : updateSubject
     * Purpose : tenant admin subject update
     * Author  : SM
     * Created Date : 26-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateSubject(Request $request)
    {
        // $request->replace($request->except('subject_image'));
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-subject';
            $form_params = [
                "subject_id" => $request->subject_id ?? '',
                "academic_year_id" => $request->academic_year_id ?? '',
                "board_id" => $request->board_id ?? '',
                "year_group_id" => $request->year_group_id,
                "subject_name" => $request->subject_name,
                "description" => $request->description,
                "subject_image" => $request->imagedata_subject_image ?? '',
                "status" => $request->status ?? '',
            ];
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
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importSubject
     * Purpose : tenant admin subject import view
     * Author  :
     * Created Date : 30-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importSubject()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $client = new Client();
        $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
        $call = $client->post($apiEndpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            //'body' => json_encode($data),
        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        $data['boards'] = $response['result']['boards'];
        return view('tenant.subject.import', $data);

    }

    /*
     * Function name : saveImportSubject
     * Purpose : tenant admin subject save import
     * Author  :
     * Created Date : 30-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportSubject(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-subjects';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'academic_year_id',
                        'contents' => $request->academic_year_id ?? '',
                    ],
                    [
                        'name' => 'year_group_id',
                        'contents' => $request->year_group_id ?? '',
                    ],
                    [
                        'name' => 'board_id',
                        'contents' => $request->board_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_subjectlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getLessonListing
     * Purpose : tenant admin lesson listing
     * Author  :
     * Created Date : 27-02-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getLessonListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/lessons';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            $boards = Session()->get('datalist_shortboards');
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['lesson_list']['last_page'];
                $current_page = $response['result']['lesson_list']['current_page'];
                $prev_page_url = $response['result']['lesson_list']['prev_page_url'];
                $next_page_url = $response['result']['lesson_list']['next_page_url'];

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
                'tenant.lesson.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'boards', 'sayid', 'sygid', 'ssid'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addLesson
     * Purpose : tenant admin lesson add view
     * Author  :
     * Created Date : 27-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addLesson()
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
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.lesson.add');

    }

    /*
     * Function name : saveLesson
     * Purpose : tenant admin lesson year save
     * Author  :
     * Created Date : 27-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveLesson(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-lesson';
            $form_params = [
                "subject_id" => $request->subject_id ?? '',
                "lesson_name" => $request->lesson_name ?? '',
                "lesson_number" => $request->lesson_number ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editLesson
     * Purpose : tenant admin lesson edit view
     * Author  : SM
     * Created Date : 27-02-2024
     * Modified date :
     * Params : lesson_id
     * Return : details
     */
    public function editLesson($subdomain, $lesson_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "lesson_id" => $lesson_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lesson_details'] = $response['result']['details'];
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
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.lesson.edit', $data);
    }

    /*
     * Function name : updateLesson
     * Purpose : tenant admin lesson update
     * Author  : SM
     * Created Date : 27-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateLesson(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-lesson';
            $form_params = [
                "lesson_id" => $request->lesson_id ?? '',
                "subject_id" => $request->subject_id ?? '',
                "lesson_name" => $request->lesson_name ?? '',
                "lesson_number" => $request->lesson_number ?? '',
                "status" => $request->status ?? '',
            ];
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
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importLesson
     * Purpose : tenant admin lesson import view
     * Author  :
     * Created Date : 30-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importLesson()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.lesson.import');

    }

    /*
     * Function name : saveImportLesson
     * Purpose : tenant admin lesson year save import
     * Author  :
     * Created Date : 30-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportLesson(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-lessons';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $request->subject_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTopicListing
     * Purpose : tenant admin lesson listing
     * Author  :
     * Created Date : 18-07-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTopicListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';
        $slid = $request->slid ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/topics';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_lesson_id" => $slid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            // dd(json_encode($formParams));
            $response = json_decode($call->getBody()->getContents(), true);
            $boards = Session()->get('datalist_shortboards');
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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

            return view(
                'tenant.topic.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'boards', 'sayid', 'sygid', 'ssid', 'slid'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addTopic
     * Purpose : tenant admin lesson topic add view
     * Author  :
     * Created Date : 18-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addTopic()
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
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.topic.add');

    }

    /*
     * Function name : saveTopic
     * Purpose : tenant admin lesson topic save
     * Author  :
     * Created Date : 18-07-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function saveTopic(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-topic';

            $options = array();

            $reqOptions = $request->sub_topic;
            for ($i = 0; $i < count($reqOptions); $i++) {
                $ele = [
                    "sub_topic" => $reqOptions[$i],
                ];
                array_push($options, $ele);

            }
            $form_params = [
                "subject_id" => $request->subject_id ?? '',
                "lesson_id" => $request->lesson_id ?? '',
                "topic" => $request->topic ?? '',
                "sub_topics" => $options,
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
            $this->refreshLoginData();
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editTopic
     * Purpose : tenant admin topic edit view
     * Author  : SM
     * Created Date : 18-07-2024
     * Modified date :
     * Params : lesson_id
     * Return : details
     */
    public function editTopic($subdomain, $topic_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-topic-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "topic_id" => $topic_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            // dd($data['details']['sub_topics'][0]['sub_topic']);

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
                return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_lessonlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.topic.edit', $data);
    }

    /*
     * Function name : updateTopic
     * Purpose : tenant admin topic update
     * Author  : SM
     * Created Date : 18-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateTopic(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-topic';
            $options = array();

            $reqOptions = $request->sub_topic;
            $reqOptionIds = $request->sub_topic_id;

            for ($i = 0; $i < count($reqOptions); $i++) {
                $ele = [
                    "sub_topic_id" => $reqOptionIds[$i] ?? '',
                    "sub_topic" => $reqOptions[$i],
                ];
                array_push($options, $ele);

            }
            $form_params = [
                "topic_id" => $request->topic_id ?? '',
                "subject_id" => $request->subject_id ?? '',
                "lesson_id" => $request->lesson_id ?? '',
                "topic" => $request->topic ?? '',
                "status" => $request->status ?? GlobalVars::ACTIVE_STATUS,
                "sub_topics" => $options,
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
            $this->refreshLoginData();
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            throw ($e);
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);

            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importTopic
     * Purpose : tenant admin topic import view
     * Author  :
     * Created Date : 22-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importTopic()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.topic.import');

    }

    /*
     * Function name : saveImportTopic
     * Purpose : tenant admin topic save import
     * Author  :
     * Created Date : 22-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportTopic(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-topics';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $request->subject_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_topiclist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherDashboard
     * Purpose : tenant admin (teacher) dashbaord view
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherDashboard()
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
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.teacher.dashboard');
    }

    /*
     * Function name : addLessonTeacher
     * Purpose : tenant admin lesson add view
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addLessonTeacher()
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
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.teacher.lesson-add');

    }

    /*
     * Function name : saveLessonTeacher
     * Purpose : tenant admin lesson year save
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveLessonTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-lesson-teacher';
            $form_params = [
                "subject_id" => $request->subject_id ?? '',
                "lesson_name" => $request->lesson_name ?? '',
                "lesson_number" => $request->lesson_number ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editLessonTeacher
     * Purpose : tenant admin lesson edit view
     * Author  : SM
     * Created Date : 27-06-2024
     * Modified date :
     * Params : lesson_id
     * Return : details
     */
    public function editLessonTeacher($subdomain, $lesson_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('lesson_plan');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "lesson_id" => $lesson_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lesson_details'] = $response['result']['details'];
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
                return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.lesson-edit', $data);
    }

    /*
     * Function name : updateLesson
     * Purpose : tenant admin lesson update
     * Author  : SM
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateLessonTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-lesson-teacher';
            $form_params = [
                "lesson_id" => $request->lesson_id ?? '',
                "subject_id" => $request->subject_id ?? '',
                "lesson_name" => $request->lesson_name ?? '',
                "lesson_number" => $request->lesson_number ?? '',
                "status" => $request->status ?? '',
            ];
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
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importLessonTeacher
     * Purpose : tenant admin lesson import view
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importLessonTeacher()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.teacher.lesson-import');

    }

    /*
     * Function name : saveImportLessonTeacher
     * Purpose : tenant admin lesson year save import
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportLessonTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-lessons';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $request->subject_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_alllessons', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getStudentListing
     * Purpose : tenant admin lesson listing
     * Author  :
     * Created Date : 04-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getStudentListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('student');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/students';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd(json_encode($formParams));
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['students']['last_page'];
                $current_page = $response['result']['students']['current_page'];
                $prev_page_url = $response['result']['students']['prev_page_url'];
                $next_page_url = $response['result']['students']['next_page_url'];

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
                'tenant.student.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'sayid', 'sygid', 'ssid'
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
                    return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addStudent
     * Purpose : tenant admin lesson add view
     * Author  :
     * Created Date : 04-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addStudent()
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
        $checkAccess = $this->checkAccess('student');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-batch-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['batch_types'] = $response['result']['batch_types'];
            // dd($data);

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $formParams = [

                "status" => 'Active',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
            $data['shortboards'] = Session()->get('datalist_shortboards');

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
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.add', $data);

    }

    /*
     * Function name : saveStudent
     * Purpose : tenant admin lesson year save
     * Author  :
     * Created Date : 05-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveStudent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-student';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }

            $takeCommute = 0;
            $takeTransport = 0;
            $commute_transport = $request->commute_transport ?? '';
            if ($commute_transport != '') {
                if ($commute_transport == 1) {
                    $takeCommute = 1;
                    $takeTransport = 0;
                }
                if ($commute_transport == 2) {
                    $takeCommute = 0;
                    $takeTransport = 1;
                }
            }

            $form_params = [
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "gender" => $request->gender ?? '',
                "batch_type_id" => $request->batch_type_id ?? null,
                "phone" => $request->phone ?? '',
                "address" => $request->address ?? '',
                "parent_name" => $request->parent_name ?? '',
                "parent_phone" => $request->parent_phone ?? '',
                "parent_email" => $request->parent_email ?? '',
                "have_sensupport_healthcare_plan" => $request->have_sensupport_healthcare_plan ?? 'N',
                "first_lang_not_eng" => $request->first_lang_not_eng ?? 'N',
                "freeschool_eligible" => $request->freeschool_eligible ?? 'N',
                "take_commute" => $takeCommute,
                "take_transport" => $takeTransport,

                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
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
            $this->refreshLoginData();
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editStudent
     * Purpose : tenant admin student edit view
     * Author  : SM
     * Created Date : 05-03-2024
     * Modified date :
     * Params : student_id
     * Return : details
     */
    public function editStudent($subdomain, $student_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('student');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-student-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "student_id" => $student_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['student_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-batch-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['batch_types'] = $response['result']['batch_types'];
            // dd($data);

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
            $data['shortboards'] = Session()->get('datalist_shortboards');
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
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.student.edit', $data);
    }

    /*
     * Function name : updateStudent
     * Purpose : tenant admin lesson update
     * Author  : SM
     * Created Date : 27-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateStudent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-student';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }

            $takeCommute = 0;
            $takeTransport = 0;
            $commute_transport = $request->commute_transport ?? '';
            if ($commute_transport != '') {
                if ($commute_transport == 1) {
                    $takeCommute = 1;
                    $takeTransport = 0;
                }
                if ($commute_transport == 2) {
                    $takeCommute = 0;
                    $takeTransport = 1;
                }
            }

            $form_params = [
                "student_id" => $request->student_id ?? '',
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "gender" => $request->gender ?? '',
                "batch_type_id" => $request->batch_type_id ?? null,
                "phone" => $request->phone ?? '',
                "address" => $request->address ?? '',
                "parent_name" => $request->parent_name ?? '',
                "parent_phone" => $request->parent_phone ?? '',
                "parent_email" => $request->parent_email ?? '',

                "have_sensupport_healthcare_plan" => $request->have_sensupport_healthcare_plan ?? 'N',
                "first_lang_not_eng" => $request->first_lang_not_eng ?? 'N',
                "freeschool_eligible" => $request->freeschool_eligible ?? 'N',
                "take_commute" => $takeCommute,
                "take_transport" => $takeTransport,

                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
                "status" => $request->status ?? 'Active',
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
            $this->refreshLoginData();
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherListing
     * Purpose : tenant admin teacher listing
     * Author  :
     * Created Date : 04-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTeacherListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teachers';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['teacher_list']['last_page'];
                $current_page = $response['result']['teacher_list']['current_page'];
                $prev_page_url = $response['result']['teacher_list']['prev_page_url'];
                $next_page_url = $response['result']['teacher_list']['next_page_url'];

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
                'tenant.teacher.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file', 'sayid', 'sygid', 'ssid'
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
                    return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addTeacher
     * Purpose : tenant admin teacher add view
     * Author  :
     * Created Date : 04-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addTeacher()
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
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];
            $data['shortboards'] = Session()->get('datalist_shortboards');
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $formParams = [

                "status" => 'Active',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-departments';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['department_list'] = $response['result']['dropdown_list'];
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
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.add', $data);

    }

    /*
     * Function name : saveTeacher
     * Purpose : tenant admin teacher save
     * Author  :
     * Created Date : 05-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-teacher';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }
            $form_params = [
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "ni_number" => $request->ni_number ?? '',
                "department_id" => $request->department_id ?? '',
                "address" => $request->address ?? '',
                "about" => $request->about ?? '',
                "is_qualified_faculty" => $request->is_qualified_faculty ?? 0,
                "end_date_id" => $request->end_date_id ?? null,
                "end_date_dbs" => $request->end_date_dbs ?? null,
                "id_file" => $request->imagedata_id_file ?? '',
                "dbs_certificate_file" => $request->imagedata_dbs_certificate_file ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
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
            $this->refreshLoginData();
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editTeacher
     * Purpose : tenant admin student edit view
     * Author  : SM
     * Created Date : 05-03-2024
     * Modified date :
     * Params : teacher_id
     * Return : details
     */
    public function editTeacher($subdomain, $teacher_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-teacher-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "teacher_id" => $teacher_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['teacher_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
            // dd($data);
            $data['shortboards'] = Session()->get('datalist_shortboards');
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-departments';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['department_list'] = $response['result']['dropdown_list'];
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
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.edit', $data);
    }

    /*
     * Function name : updateTeacher
     * Purpose : tenant admin teacher update
     * Author  : SM
     * Created Date : 27-02-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-teacher';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }
            $form_params = [
                "teacher_id" => $request->teacher_id ?? '',
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "ni_number" => $request->ni_number ?? '',
                "department_id" => $request->department_id ?? '',
                "address" => $request->address ?? '',
                "about" => $request->about ?? '',
                "is_qualified_faculty" => $request->is_qualified_faculty ?? 0,
                "end_date_id" => $request->end_date_id ?? null,
                "end_date_dbs" => $request->end_date_dbs ?? null,
                "id_file" => $request->imagedata_id_file ?? '',
                "dbs_certificate_file" => $request->imagedata_dbs_certificate_file ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
                "status" => $request->status ?? 'Active',
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
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getDepartmentListing
     * Purpose : tenant admin depatment listing
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getDepartmentListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        // $checkAccess = $this->checkAccess('user');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/departments' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['listing']['last_page'];
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

            return view(
                'tenant.department.index', compact(
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
                    return \Redirect::route('ta_departmentlist');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_departmentlist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addDepartment
     * Purpose : tenant admin department add view
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addDepartment()
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
        // $checkAccess = $this->checkAccess('user');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        return view('tenant.department.add');

    }

    /*
     * Function name : saveDepartment
     * Purpose : tenant admin department save
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveDepartment(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-department';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "department_name" => $request->department_name ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editDepartment
     * Purpose : tenant admin department edit view
     * Author  : SM
     * Created Date : 06-03-2024
     * Modified date :
     * Params : deparment_id
     * Return : void
     */
    public function editDepartment($subdomain, $department_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($academic_year_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        // $checkAccess = $this->checkAccess('user');
        // if ($checkAccess == false) {
        //     $notification = array(
        //         'message' => 'You are not authorized to access this section.',
        //         'alert-type' => 'error',
        //     );
        //     return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        // }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-department-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "department_id" => $department_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

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
                return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.department.edit', $data);
    }

    /*
     * Function name : updateDepartment
     * Purpose : tenant admin department update
     * Author  : SM
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateDepartment(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-department';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "department_id" => $request->department_id ?? '',
                    "department_name" => $request->department_name ?? '',
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
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importDepartment
     * Purpose : tenant admin department] import view
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importDepartment()
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
        $checkAccess = $this->checkAccess('import');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        return view('tenant.department.import');

    }

    /*
     * Function name : saveImportDepartment
     * Purpose : tenant admin department save import
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportDepartment(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-departments';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_departmentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherAssistantListing
     * Purpose : tenant admin teacher assistant listing
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTeacherAssistantListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher-assistants';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['teacher_assistant_list']['last_page'];
                $current_page = $response['result']['teacher_assistant_list']['current_page'];
                $prev_page_url = $response['result']['teacher_assistant_list']['prev_page_url'];
                $next_page_url = $response['result']['teacher_assistant_list']['next_page_url'];

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
                'tenant.teacher_assistant.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file', 'sayid', 'sygid', 'ssid'
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
                    return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addTeacherAssistant
     * Purpose : tenant admin teacher assistant add view
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addTeacherAssistant()
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
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $formParams = [

                "status" => 'Active',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];

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
                return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher_assistant.add', $data);

    }

    /*
     * Function name : saveTeacherAssistant
     * Purpose : tenant admin teacher assistant save
     * Author  :
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveTeacherAssistant(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-teacher-assistant';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }
            $form_params = [
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "ni_number" => $request->ni_number ?? '',
                "address" => $request->address ?? '',
                "about" => $request->about ?? '',
                "end_date_id" => $request->end_date_id ?? null,
                "end_date_dbs" => $request->end_date_dbs ?? null,
                "id_file" => $request->imagedata_id_file ?? '',
                "dbs_certificate_file" => $request->imagedata_dbs_certificate_file ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
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
            $this->refreshLoginData();
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editTeacherAssistant
     * Purpose : tenant admin teacher assistant edit view
     * Author  : SM
     * Created Date : 06-03-2024
     * Modified date :
     * Params : teacher_assistant_id
     * Return : details
     */
    public function editTeacherAssistant($subdomain, $teacher_assistant_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('teacher');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-teacher-assistant-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "teacher_assistant_id" => $teacher_assistant_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['teacher_assistant_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
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
                return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher_assistant.edit', $data);
    }

    /*
     * Function name : updateTeacherAssistant
     * Purpose : tenant admin teacher assistant update
     * Author  : SM
     * Created Date : 06-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateTeacherAssistant(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-teacher-assistant';
            $subject_id = null;
            if (isset($request->subject_id) && count($request->subject_id) > 0) {
                $subject_id = implode(',', $request->subject_id);
            }
            $form_params = [
                "teacher_assistant_id" => $request->teacher_assistant_id ?? '',
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',

                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "ni_number" => $request->ni_number ?? '',
                "address" => $request->address ?? '',
                "about" => $request->about ?? '',
                "end_date_id" => $request->end_date_id ?? null,
                "end_date_dbs" => $request->end_date_dbs ?? null,
                "id_file" => $request->imagedata_id_file ?? '',
                "dbs_certificate_file" => $request->imagedata_dbs_certificate_file ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "subject_id" => $subject_id,
                "status" => $request->status ?? 'Active',
            ];
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
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherassistantlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getEmployeeListing
     * Purpose : tenant admin employee listing
     * Author  :
     * Created Date : 07-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getEmployeeListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('employee');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/employees' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['item_list']['last_page'];
                $current_page = $response['result']['item_list']['current_page'];
                $prev_page_url = $response['result']['item_list']['prev_page_url'];
                $next_page_url = $response['result']['item_list']['next_page_url'];

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
                'tenant.employee.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addEmployee
     * Purpose : tenant admin employee add view
     * Author  :
     * Created Date : 07-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addEmployee()
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
        $checkAccess = $this->checkAccess('employee');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-departments';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['department_list'] = $response['result']['dropdown_list'];

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
                return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.employee.add', $data);

    }

    /*
     * Function name : saveEmployee
     * Purpose : tenant admin employee save
     * Author  :
     * Created Date : 07-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveEmployee(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-employee';
            $form_params = [
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "department_id" => $request->department_id ?? '',
                "address" => $request->address ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editEmployee
     * Purpose : tenant admin employee edit view
     * Author  : SM
     * Created Date : 07-03-2024
     * Modified date :
     * Params : employee_id
     * Return : details
     */
    public function editEmployee($subdomain, $employee_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('employee');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-employee-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "employee_id" => $employee_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['employee_details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-departments';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['department_list'] = $response['result']['dropdown_list'];
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
                return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.employee.edit', $data);
    }

    /*
     * Function name : updateEmployee
     * Purpose : tenant admin employee update
     * Author  : SM
     * Created Date : 07-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateEmployee(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-employee';

            $form_params = [
                "employee_id" => $request->employee_id ?? '',
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "department_id" => $request->department_id ?? '',
                "address" => $request->address ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "status" => $request->status ?? 'Active',
            ];
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
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_employeelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : studentMyCourse
     * Purpose : tenant user (student) mycourse view
     * Author  :
     * Created Date : 11-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentMyCourse()
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/my-courses';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['details'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.my-courses', $data);

    }

    /*
     * Function name : studentMyCoursePlan
     * Purpose : tenant user (student) mycourse plan view
     * Author  :
     * Created Date : 14-05-2024
     * Modified date :
     * Params : subject_id, user_token
     * Return : void
     */
    public function studentMyCoursePlan($subdomain, $subject_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/get-subjectid-lessons';
            $form_params = [
                "subject_id" => $subject_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['subject_info'] = $response['result']['subject_info'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;

            // dd($data);
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.my-course-plan', $data);

    }

    /*
     * Function name : teacherQuizes
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 13-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherQuizes(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $request->request->set('examination_type', 'Q');
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_type" => 'Q',
                    "search_text" => $search_text ?? '',
                    "page" => $page ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.quizes', $data);

    }

    /*
     * Function name : teacherAddQuiz
     * Purpose : tenant user (teacher) add quiz view
     * Author  :
     * Created Date : 13-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAddQuiz()
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

        return view('tenant.teacher.add_quiz');
    }

    /*
     * Function name : teacherSaveQuiz
     * Purpose : tenant user (teacher) save quiz
     * Author  :
     * Created Date : 13-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherSaveQuiz(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/create';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_type" => 'Q',
                    "name" => $request->name ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherEditQuiz
     * Purpose : tenant admin quiz edit view
     * Author  : SM
     * Created Date : 14-03-2024
     * Modified date :
     * Params : examination_id
     * Return : details
     */
    public function teacherEditQuiz($subdomain, $examination_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_id" => $examination_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_details'] = $response['result']['details'];
            // dd($data);

            $apiEndpoint = config('app.api_base_url') . '/dropdown/examination-status';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['exam_status'] = $response['result']['exam_status'];

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
                return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.edit_quiz', $data);
    }

    /*
     * Function name : teacherAddQuizQuestion
     * Purpose : tenant user (teacher) add quiz question view
     * Author  :
     * Created Date : 15-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAddQuizQuestion($subdomain, $examination_id, $page_id)
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
        $data['examination_id'] = $examination_id;
        $data['page_id'] = $page_id;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_id" => $examination_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_details'] = $response['result']['details'];
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
                //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.add_quiz_question', $data);
    }

    /*
     * Function name : teacherSaveQuizQuestion
     * Purpose : tenant user (teacher) save quiz question
     * Author  :
     * Created Date : 13-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherSaveQuizQuestion(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $timeInSecond = $request->time_inseconds ?? '0';
            $timeInSecond = $timeInSecond * 60;

            $examination_id = CommonHelper::decryptId($request->examination_id);
            $page_id = CommonHelper::decryptId($request->page_id);

            $examination_question_id = null;
            if ($request->examination_question_id != null) {
                $examination_question_id = CommonHelper::decryptId($request->examination_question_id);
            }

            $question_id = null;
            if ($request->question_id != null) {
                $question_id = $request->question_id;
            }

            $options = array();

            $reqOptions = $request->qoptions;
            $reqOptionsCorrect = $request->qcorrect;
            for ($i = 0; $i < count($reqOptions); $i++) {
                $ele = [
                    "option_value" => $reqOptions[$i],
                    "is_correct" => $reqOptionsCorrect[$i],
                ];
                array_push($options, $ele);

            }

            $formParams = [
                "examination_id" => $examination_id ?? '',
                "page_id" => $page_id ?? '',
                "question_type" => $request->question_type ?? '',
                "year_group_id" => $request->year_group_id ?? '',
                "subject_id" => $request->subject_id ?? '',
                "lesson_id" => $request->lesson_id ?? '',
                "topic_id" => $request->topic_id ?? '',
                "sub_topic_id" => $request->sub_topic_id ?? '',
                "tc" => $request->tc ?? 0,
                "ms" => $request->ms ?? 0,
                "ps" => $request->ps ?? 0,
                "at" => $request->at ?? 0,
                "question" => $request->question ?? '',
                "question_category_id" => $request->question_category_id ?? null,
                "level" => $request->level ?? '',
                "require_file_upload" => $request->require_file_upload ?? '',
                "source" => 'Q',
                "time_inseconds" => $request->time_inseconds ?? 0,
                "point" => $request->point ?? 0,
                "examination_question_id" => $examination_question_id ?? null,
                "question_id" => $question_id ?? null,
                "options" => $options,
            ];

            if ($request->question_type == "text") {
                $formParams = [
                    "examination_id" => $examination_id ?? '',
                    "page_id" => $page_id ?? '',
                    "question_type" => $request->question_type ?? '',
                    "year_group_id" => $request->year_group_id ?? '',
                    "subject_id" => $request->subject_id ?? '',
                    "lesson_id" => $request->lesson_id ?? '',
                    "topic_id" => $request->topic_id ?? '',
                    "sub_topic_id" => $request->sub_topic_id ?? '',
                    "tc" => $request->tc ?? 0,
                    "ms" => $request->ms ?? 0,
                    "ps" => $request->ps ?? 0,
                    "at" => $request->at ?? 0,
                    "question" => $request->question ?? '',
                    "question_category_id" => $request->question_category_id ?? null,
                    "level" => $request->level ?? '',
                    "require_file_upload" => $request->require_file_upload ?? '',
                    "source" => 'Q',
                    "time_inseconds" => $request->time_inseconds ?? 0,
                    "point" => $request->point ?? 0,
                    "examination_question_id" => $examination_question_id ?? null,
                    "question_id" => $question_id ?? null,
                ];

            }

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/question/create-or-update';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $request->examination_id])->with($notification);

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
                return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $request->examination_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $request->examination_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherImportQuizQuestion
     * Purpose : tenant user (teacher) import quiz question view
     * Author  :
     * Created Date : 25-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherImportQuizQuestion($subdomain, $examination_id, $page_id)
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
        $data['examination_id'] = $examination_id;
        $data['page_id'] = $page_id;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_id" => $examination_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_details'] = $response['result']['details'];
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
                //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.import_quiz_question', $data);
    }

    /*
     * Function name : teacherSaveImportQuizQuestion
     * Purpose : tenant teacher save import quiz question
     * Author  :
     * Created Date : 14-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherSaveImportQuizQuestion(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $examination_id = $request->examination_id ?? '';
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/import-quiz-questions';

            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();

                $page_id = $request->page_id ?? '';

                $year_group_id = $request->year_group_id ?? '';
                $subject_id = $request->subject_id ?? '';
                $lesson_id = $request->lesson_id ?? '';
                $topic_id = $request->topic_id ?? '';
                $sub_topic_id = $request->sub_topic_id ?? '';

                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'page_id',
                        'contents' => $page_id ?? '',
                    ],
                    [
                        'name' => 'examination_id',
                        'contents' => $examination_id ?? '',
                    ],
                    [
                        'name' => 'year_group_id',
                        'contents' => $year_group_id ?? '',
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $subject_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $lesson_id ?? '',
                    ],
                    [
                        'name' => 'topic_id',
                        'contents' => $topic_id ?? '',
                    ],
                    [
                        'name' => 'sub_topic_id',
                        'contents' => $sub_topic_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $examination_id])->with($notification);

            }
            // print_r($request->all());
            // dd($multipart);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $examination_id])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_editquiz', [Session()->get('tenant_info')['subdomain'], $examination_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_quizes', [Session()->get('tenant_info')['subdomain'], $examination_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherEditQuizQuestion
     * Purpose : tenant user (teacher) add quiz question view
     * Author  :
     * Created Date : 19-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherEditQuizQuestion($subdomain, $examination_id, $page_id, $examination_question_id)
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
        $data['examination_id'] = $examination_id;
        $data['examination_question_id'] = $examination_question_id;
        $data['page_id'] = $page_id;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_id" => $examination_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/question/get-by-examination_question_id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_question_id" => $examination_question_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_question_details'] = $response['result']['details'];
            $data['examination_question'] = json_decode($response['result']['details']['examination_question']['question_info']);
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
                //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            //return \Redirect::route('tut_quizes', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.edit_quiz_question', $data);
    }

    /*
     * Function name : teacherMyCourse
     * Purpose : tenant user (student) mycourse view
     * Author  :
     * Created Date : 11-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherMyCourse()
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['details'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.my-courses', $data);

    }

    /*
     * Function name : getTeacherLessonListing
     * Purpose : tenant admin teacher lesson listing
     * Author  :
     * Created Date : 26-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTeacherLessonListing($subdomain, $subject_id)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/lessons';

            $form_params = [
                "subject_id" => $subject_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $boards = Session()->get('datalist_shortboards');
            return view(
                'tenant.teacher.lessons', compact('response', 'boards', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('tut_lessons', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_lessons', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getTeacherAllLessonListing
     * Purpose : tenant teacher lesson listing
     * Author  :
     * Created Date : 24-06-2024
     * Modified date :
     * Return : list
     */
    public function getTeacherAllLessonListing()
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/all-lessons';

            // $form_params = [
            //     "subject_id" => $subject_id ?? '',
            // ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                // 'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $boards = Session()->get('datalist_shortboards');
            return view(
                'tenant.teacher.all-lessons', compact('response', 'boards', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('tut_lessons', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_lessons', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getTeacherStudentListing
     * Purpose : tenant admin teacher student listing
     * Author  :
     * Created Date : 26-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTeacherStudentListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/students' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['details']['last_page'];
                $current_page = $response['result']['details']['current_page'];
                $prev_page_url = $response['result']['details']['prev_page_url'];
                $next_page_url = $response['result']['details']['next_page_url'];

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
                'tenant.teacher.students', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('tut_students', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_students', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getTeacherTaListing
     * Purpose : tenant admin teacher's teacher assistant listing
     * Author  :
     * Created Date : 26-03-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getTeacherTaListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/teacher-assistants' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['details']['last_page'];
                $current_page = $response['result']['details']['current_page'];
                $prev_page_url = $response['result']['details']['prev_page_url'];
                $next_page_url = $response['result']['details']['next_page_url'];

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
                'tenant.teacher.teacher-assistants', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('tut_teacherassistant', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_teacherassistant', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : studentQuizes
     * Purpose : tenant user (student) quizes view
     * Author  :
     * Created Date : 27-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentQuizes(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $formParams = [
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'Q',
            ];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examinations';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($call);
            $data['listing'] = $response['result']['listing'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.quizes', $data);

    }

    /*
     * Function name : studentAssesments
     * Purpose : tenant user (student) assesments view
     * Author  :
     * Created Date : 08-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentAssesments(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $formParams = [
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'A',
            ];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examinations';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.assesments', $data);

    }

    /*
     * Function name : studentStartQuiz
     * Purpose : tenant user (student) quizes view
     * Author  :
     * Created Date : 27-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentStartQuiz(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        // $request->request->set('examination_id', 'T2N4ZmdQeDJjaHNIZzR4S1A1OHc1UT09');

        try {
            $client = new Client();
            $formParams = [
                "examination_id" => $request->examination_id ?? '',
            ];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examination-questions';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // throw ($e);
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.start_quiz', $data);

    }

    /*
     * Function name : studentStartAssesment
     * Purpose : tenant user (student) quizes view
     * Author  :
     * Created Date : 09-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentStartAssesment(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        // $request->request->set('examination_id', 'T2N4ZmdQeDJjaHNIZzR4S1A1OHc1UT09');

        try {
            $client = new Client();
            $formParams = [
                "examination_id" => $request->examination_id ?? '',
            ];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examination-questions';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                // throw ($e);
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $notification = array(
                    'message' => $response->error->message,
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.start_assesment', $data);

    }

    /*
     * Function name : studentSaveQuiz
     * Purpose : tenant user (student) quize save
     * Author  :
     * Created Date : 01-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentSaveQuiz(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            // dd($request->all());

            $examination_id = $request->examination_id;
            $total_marks = $request->total_marks;
            $start_time = $request->quiz_begintime;
            $end_time = date('Y-m-d H:i:s');
            $total_quiztime = explode(':', $request->total_quiztime);
            $hr = $total_quiztime[0] ?? 0;
            $min = $total_quiztime[1] ?? 0;
            $sec = $total_quiztime[3] ?? 0;
            $total_time_in_mins = (($hr * 60) + $min + ($sec / 60));

            $to_time = strtotime($end_time);
            $from_time = strtotime($start_time);
            $time_taken_inmins = round(abs($to_time - $from_time) / 60, 2);

            $options = array();

            $questid = $request->questid;
            $quest_starttime = $request->quest_starttime;
            $quest_endtime = $request->quest_endtime;
            for ($i = 0; $i < count($questid); $i++) {
                $ans_to_time = strtotime($quest_endtime[$i]);
                $ans_from_time = strtotime($quest_starttime[$i]);
                $ans_time_taken_inmins = round(abs($ans_to_time - $ans_from_time) / 60, 2);

                $answer = $request->input('ans_' . $questid[$i]);
                // dd($answer);
                $attachment_file = null;
                if ($request->hasFile('file_' . $questid[$i])) {
                    $attachmentArr = array();
                    $attachment = $request->file('file_' . $questid[$i]);
                    // dd($attachment);
                    foreach ($attachment as $attach_file) {
                        $attachment_ext = $attach_file->getClientOriginalExtension();
                        $attachment_mimetype = GlobalVars::EXT_MIMETYPE['.' . $attachment_ext];

                        $attachment_file = base64_encode(file_get_contents($attach_file));
                        $attachment_file = 'data: ' . $attachment_mimetype . ';base64,' . $attachment_file;
                        array_push($attachmentArr, $attachment_file);
                    }
                    $attachment_file = $attachmentArr;
                    // dd($attachment_file);
                }

                $ele = [
                    "examination_question_id" => $questid[$i],
                    "answer" => $answer,
                    "attachment_file" => $attachment_file,
                    "time_taken_inmins" => $ans_time_taken_inmins,
                ];
                array_push($options, $ele);

            }

            $formParams = [
                "examination_id" => $examination_id ?? '',
                "start_time" => $start_time ?? '',
                "end_time" => $end_time ?? '',
                "total_time_in_mins" => $total_time_in_mins ?? '',
                "time_taken_inmins" => $time_taken_inmins ?? '',
                "total_marks" => $total_marks ?? '',
                "options" => $options,
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examination-save';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tus_quizes', [Session()->get('tenant_info')['subdomain']])->with($notification);

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
                return \Redirect::route('tus_quizes', [Session()->get('tenant_info')['subdomain']])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_quizes', [Session()->get('tenant_info')['subdomain']])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : studentSaveAssessment
     * Purpose : tenant user (student) quize save
     * Author  :
     * Created Date : 01-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentSaveAssessment(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            // dd($request->all());

            $examination_id = $request->examination_id;
            $total_marks = $request->total_marks;
            $start_time = $request->quiz_begintime;
            $end_time = date('Y-m-d H:i:s');
            $total_quiztime = explode(':', $request->total_quiztime);
            $hr = $total_quiztime[0] ?? 0;
            $min = $total_quiztime[1] ?? 0;
            $sec = $total_quiztime[3] ?? 0;
            $total_time_in_mins = (($hr * 60) + $min + ($sec / 60));

            $to_time = strtotime($end_time);
            $from_time = strtotime($start_time);
            $time_taken_inmins = round(abs($to_time - $from_time) / 60, 2);

            $options = array();

            $questid = $request->questid;
            $answers = $request->answer;
            $quest_starttime = $request->quest_starttime;
            $quest_endtime = $request->quest_endtime;

            for ($i = 0; $i < count($questid); $i++) {
                $ans_to_time = strtotime($quest_endtime[$i]);
                $ans_from_time = strtotime($quest_starttime[$i]);
                $ans_time_taken_inmins = round(abs($ans_to_time - $ans_from_time) / 60, 2);

                $answer = $answers[$i];
                // dd($answer);

                $attachment_file = null;
                if ($request->hasFile('file_' . $questid[$i])) {

                    // $attachment = $request->file('file_' . $questid[$i]);
                    // $attachment_ext = $attachment->getClientOriginalExtension();
                    // $attachment_mimetype = GlobalVars::EXT_MIMETYPE['.' . $attachment_ext];

                    // $attachment_file = base64_encode(file_get_contents($request->file('file_' . $questid[$i])));
                    // $attachment_file = 'data: ' . $attachment_mimetype . ';base64,' . $attachment_file;
                    $attachmentArr = array();
                    $attachment = $request->file('file_' . $questid[$i]);
                    // dd($attachment);
                    foreach ($attachment as $attach_file) {
                        $attachment_ext = $attach_file->getClientOriginalExtension();
                        $attachment_mimetype = GlobalVars::EXT_MIMETYPE['.' . $attachment_ext];

                        $attachment_file = base64_encode(file_get_contents($attach_file));
                        $attachment_file = 'data: ' . $attachment_mimetype . ';base64,' . $attachment_file;
                        array_push($attachmentArr, $attachment_file);
                    }
                    $attachment_file = $attachmentArr;
                    // dd($attachment_file);
                }

                $ele = [
                    "examination_question_id" => $questid[$i],
                    "answer" => $answer,
                    "attachment_file" => $attachment_file,
                    "time_taken_inmins" => $ans_time_taken_inmins,
                ];
                array_push($options, $ele);

            }

            $formParams = [
                "examination_id" => $examination_id ?? '',
                "start_time" => $start_time ?? '',
                "end_time" => $end_time ?? '',
                "total_time_in_mins" => $total_time_in_mins ?? '',
                "time_taken_inmins" => $time_taken_inmins ?? '',
                "total_marks" => $total_marks ?? '',
                "options" => $options,
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examination-save';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tus_assesments', [Session()->get('tenant_info')['subdomain']])->with($notification);

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
                return \Redirect::route('tus_assesments', [Session()->get('tenant_info')['subdomain']])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_assesments', [Session()->get('tenant_info')['subdomain']])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherReviewQuizes
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 03-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewQuizes(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $examination_type = $request->examination_type ?? 'Q';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations-for-review';
            // dd($apiEndpoint);
            $formParams = [
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'Q',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.exam_submitted', $data);

    }

    /*
     * Function name : teacherReviewAssessments
     * Purpose : tenant user (teacher) assessment view
     * Author  :
     * Created Date : 03-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewAssessments(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $examination_type = $request->examination_type ?? 'A';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations-for-review';
            $formParams = [
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'A',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        // dd($data);
        return view('tenant.teacher.exam_submitted', $data);

    }

    /*
     * Function name : teacherReviewQuiz
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 03-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewQuiz($subdomain, $user_result_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-examination-submission-info';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_result_id" => $user_result_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['user_result_id'] = $user_result_id;
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
                return \Redirect::route('tut_quiz_submitted', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_quiz_submitted', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.review_quiz_submission', $data);

    }

    /*
     * Function name : teacherReviewAssessment
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 10-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewAssessment($subdomain, $user_result_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-examination-submission-info';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_result_id" => $user_result_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['user_result_id'] = $user_result_id;
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
                return \Redirect::route('tut_assessment_submitted', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_assessment_submitted', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.review_assessment_submission', $data);

    }

    /*
     * Function name : teacherReviewQuizSave
     * Purpose : tenant user (teacher) quiz review save
     * Author  :
     * Created Date : 04-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewQuizSave(Request $request)
    {
        // dd($request->all());

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            // dd($request->all());

            $user_result_id = $request->user_result_id;

            $options = array();

            $examination_question_id = $request->examination_question_id;

            for ($i = 0; $i < count($examination_question_id); $i++) {
                $ele = [
                    "examination_question_id" => $examination_question_id[$i] ?? '',
                    "is_correct" => $request->is_correct[$i] ?? '0',
                    "marks_given" => $request->ansmarks_obtained[$i] ?? '0',
                    "reviewer_comments" => $request->remarks[$i] ?? '',
                ];
                array_push($options, $ele);
            }

            $formParams = [
                "user_result_id" => $user_result_id ?? '',
                "options" => $options,
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examination-review-save';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_quiz_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);

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
                return \Redirect::route('tut_quiz_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_quiz_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherReviewAssessmentSave
     * Purpose : tenant user (teacher) assessment review save
     * Author  :
     * Created Date : 10-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewAssessmentSave(Request $request)
    {
        // dd($request->all());

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            // dd($request->all());

            $user_result_id = $request->user_result_id;

            $options = array();

            $examination_question_id = $request->examination_question_id;

            for ($i = 0; $i < count($examination_question_id); $i++) {
                $ele = [
                    "examination_question_id" => $examination_question_id[$i] ?? '',
                    "is_correct" => $request->is_correct[$i] ?? '0',
                    "marks_given" => $request->ansmarks_obtained[$i] ?? '0',
                    "reviewer_comments" => $request->remarks[$i] ?? '',
                ];
                array_push($options, $ele);
            }

            $formParams = [
                "user_result_id" => $user_result_id ?? '',
                "options" => $options,
            ];

            // dd(json_encode($formParams));

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examination-review-save';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_assessment_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);

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
                return \Redirect::route('tut_assessment_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_assessment_submitted', [Session()->get('tenant_info')['subdomain']])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherQuizesReviewed
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 03-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherQuizesReviewed(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $examination_type = $request->examination_type ?? 'Q';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations-reviewed';
            // dd($apiEndpoint);
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
                "examination_type" => $examination_type,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.reviews', $data);

    }

    /*
     * Function name : teacherAssessmentsReviewed
     * Purpose : tenant user (teacher) assessments view
     * Author  :
     * Created Date : 10-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAssessmentsReviewed(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $examination_type = $request->examination_type ?? 'A';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations-reviewed';
            // dd($apiEndpoint);
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'A',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.reviews', $data);

    }

    /*
     * Function name : studentQuizesReviewed
     * Purpose : tenant user (student) quizes view
     * Author  :
     * Created Date : 03-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentQuizesReviewed(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';
            $examination_type = $request->examination_type ?? 'Q';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examinations-reviewed';
            // dd($apiEndpoint);
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
                "examination_type" => $examination_type,
            ];

            // dd(json_encode($formParams));

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.reviews', $data);

    }

    /*
     * Function name : studentAssessmentsReviewed
     * Purpose : tenant user (student) assessments view
     * Author  :
     * Created Date : 10-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentAssessmentsReviewed(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';
            $examination_type = $request->examination_type ?? 'A';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/consumer-examinations-reviewed';
            // dd($apiEndpoint);
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $request->search_text ?? '',
                "page" => $request->page ?? '1',
                "examination_type" => $request->examination_type ?? 'A',
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.reviews', $data);

    }

    /*
     * Function name : studentReviewedAnswers
     * Purpose : tenant user (student) quizes view
     * Author  :
     * Created Date : 04-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReviewedAnswers($subdomain, $user_result_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-examination-submission-info';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_result_id" => $user_result_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['user_result_id'] = $user_result_id;
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
                return \Redirect::route('tus_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.student.reviewed_answers', $data);

    }

    /*
     * Function name : teacherAssesments
     * Purpose : tenant user (teacher) quizes view
     * Author  :
     * Created Date : 05-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAssesments(Request $request)
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
        try {
            $page = $request->page ?? '1';
            $search_text = $request->search_text ?? '';
            $request->request->set('examination_type', 'Q');
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/creator-examinations';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_type" => 'A',
                    "search_text" => $search_text ?? '',
                    "page" => $page ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['listing']['last_page'];
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
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.assesments', $data);

    }

    /*
     * Function name : teacherAddAssesment
     * Purpose : tenant user (teacher) add quiz view
     * Author  :
     * Created Date : 05-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAddAssesment()
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

        return view('tenant.teacher.add_assesment_new');
    }

    /*
     * Function name : teacherSaveAssesment
     * Purpose : tenant user (teacher) save quiz
     * Author  :
     * Created Date : 06-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherSaveAssesment(Request $request)
    {
        // dd($request->input('subquestion1'));
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $questions = array();
            $reqQuestions = $request->question;
            $cntQ = 1;
            for ($i = 0; $i < count($reqQuestions); $i++) {
                $arrSubQuestions = array();
                if ($request->question_type[$i] != 'linked') {
                    $ele = [
                        "examination_question_id" => $request->examination_question_id[$i] ?? null,
                        "question_id" => $request->question_id[$i] ?? null,
                        "question" => $reqQuestions[$i] ?? null,
                        "question_type" => $request->question_type[$i] ?? null,
                        "level" => $request->level[$i] ?? null,
                        "require_file_upload" => $request->require_file_upload[$i] ?? '0',
                        "point" => $request->point[$i] ?? null,
                        "time_inseconds" => $request->time_inseconds[$i] ?? 0,
                        "topic_id" => $request->topic_id[$i] ?? 0,
                        "sub_topic_id" => $request->sub_topic_id[$i] ?? 0,
                        "tc" => $request->tc[$i] ?? 0,
                        "ms" => $request->ms[$i] ?? 0,
                        "ps" => $request->ps[$i] ?? 0,
                        "at" => $request->at[$i] ?? 0,
                    ];
                    // array_push($questions, $ele);
                } else {
                    // dd($i);
                    $ele = [
                        "examination_question_id" => $request->examination_question_id[$i] ?? null,
                        "question_id" => $request->question_id[$i] ?? null,
                        "question" => $reqQuestions[$i] ?? null,
                        "question_type" => $request->question_type[$i] ?? null,
                        "level" => $request->level[$i] ?? null,
                        "require_file_upload" => $request->require_file_upload[$i] ?? '0',
                        "point" => $request->point[$i] ?? null,
                        "time_inseconds" => $request->time_inseconds[$i] ?? 0,
                        "topic_id" => $request->topic_id[$i] ?? 0,
                        "sub_topic_id" => $request->sub_topic_id[$i] ?? 0,
                        "tc" => $request->tc[$i] ?? 0,
                        "ms" => $request->ms[$i] ?? 0,
                        "ps" => $request->ps[$i] ?? 0,
                        "at" => $request->at[$i] ?? 0,
                    ];

                    $subQuestions = $request->input('subquestion' . $i) ?? null;
                    if ($subQuestions == null) {
                        $subQuestions = array();
                    }

                    // dd($request->input('subquestion' . $i));
                    for ($j = 0; $j < count($subQuestions); $j++) {
                        $elesub = [
                            "examination_question_id" => $request->input('examination_question_id' . $i)[$j] ?? null,
                            "question_id" => $request->input('question_id' . $i)[$j] ?? null,
                            "question" => $subQuestions[$j] ?? null,
                            "question_type" => $request->input('question_type' . $i)[$j] ?? null,
                            "level" => $request->input('level' . $i)[$j] ?? null,
                            "require_file_upload" => $request->input('require_file_upload' . $i)[$j] ?? null,
                            "point" => $request->input('point' . $i)[$j] ?? null,
                            "time_inseconds" => $request->input('time_inseconds' . $i)[$j] ?? 0,
                            "topic_id" => $request->topic_id[$i] ?? 0,
                            "sub_topic_id" => $request->sub_topic_id[$i] ?? 0,
                            "tc" => $request->tc[$i] ?? 0,
                            "ms" => $request->ms[$i] ?? 0,
                            "ps" => $request->ps[$i] ?? 0,
                            "at" => $request->at[$i] ?? 0,
                        ];
                        array_push($arrSubQuestions, $elesub);
                    }

                }
                $ele['subquestions'] = $arrSubQuestions;
                array_push($questions, $ele);
                $cntQ++;
            }
            // dd($questions);
            $examination_id = $request->examination_id ?? null;
            if ($examination_id != null) {
                $examination_id = CommonHelper::decryptId($request->examination_id);
            }
            $form_params = [
                "examination_type" => 'A',
                "examination_id" => $examination_id,
                "name" => $request->assesment_name ?? '',
                "examination_status" => $request->examination_status ?? '',
                "year_group_id" => $request->year_group_id ?? '',
                "subject_id" => $request->subject_id ?? '',
                "lesson_id" => $request->lesson_id ?? '',
                "questions" => $questions ?? array(),
            ];
            // dd($form_params);
            // dd(json_encode($form_params));
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/create-with-questions-new';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            // dd($call->getBody());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_assesments', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_assesments', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_assesments', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherAddAssesment
     * Purpose : tenant user (teacher) add quiz view
     * Author  :
     * Created Date : 05-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherEditAssesment($subdomain, $examination_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        $data['examination_id'] = $examination_id;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "examination_id" => $examination_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['examination_details'] = $examination_details = $response['result']['details'];
            // dd($data);

            $apiEndpoint = config('app.api_base_url') . '/dropdown/examination-status';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['exam_status'] = $response['result']['exam_status'];

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
                return \Redirect::route('tut_assesments', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_assesments', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.edit_assesment_new', $data);
    }

    /*
     * Function name : getUserResultListing
     * Purpose : tenant exam results view
     * Author  :
     * Created Date : 10-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getUserResultListing(Request $request)
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
        try {
            $search_text = $request->search_text ?? '';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';
            $examination_type = $request->examination_type ?? '';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/results';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "examination_type" => $examination_type,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['response'] = $response;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.results', $data);

    }

    /*
     * Function name : studentStudyGroup
     * Purpose : tenant user (student) mycourse view
     * Author  :
     * Created Date : 16-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentStudyGroup(Request $request)
    {
        // dd(Session()->get('tenant_info')['subdomain']);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $user = Session()->get('user');
        if ($user['user_type'] != 'TU' || $user['role'] != 'S') {
            // dd($user['role']);
            $notification = array(
                'message' => 'You are not authorized.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        $view = $request->v ?? null;
        if ($view != null) {
            $view = CommonHelper::decryptId($view);
        }
        $data['url_view'] = $view;
        $status = $request->s ?? null;
        if ($status != null) {
            $status = CommonHelper::decryptId($status);
        } else {
            $status = GlobalVars::ACTIVE_STATUS;
        }

        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/study-groups';
            // dd($apiEndpoint);
            $form_params = [
                "view" => $view,
                "status" => $status,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            // dd(json_encode($form_params));
            $response = json_decode($call->getBody()->getContents(), true);
            $data['listing'] = $response['result']['listing'];
            $data['studygroupsin'] = $response['result']['studygroupsin'];
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['active_status'] = GlobalVars::ACTIVE_STATUS;
            $data['inactive_status'] = GlobalVars::INACTIVE_STATUS;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        // dd($data);
        if ($view == 'external') {
            return view('tenant.student.external_study_groups', $data);

        } else {
            return view('tenant.student.study_groups', $data);
        }

    }

    /*
     * Function name : addStudentStudyGroup
     * Purpose : tenant user (student) study group add view
     * Author  :
     * Created Date : 16-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addStudentStudyGroup()
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

        return view('tenant.student.add_study_group');

    }

    /*
     * Function name : saveStudentStudyGroup
     * Purpose : tenant user (student) study group save
     * Author  :
     * Created Date : 16-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveStudentStudyGroup(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/create';

            $form_params = [
                "name" => $request->name ?? '',
                "description" => $request->description ?? '',
                "group_image" => $request->imagedata_group_image ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editStudentStudyGroup
     * Purpose : tenant user (student) edit study group vie
     * Author  : SM
     * Created Date : 16-04-2024
     * Modified date :
     * Params : study_group_id
     * Return : details
     */
    public function editStudentStudyGroup($subdomain, $study_group_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-studygroup-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "study_group_id" => $study_group_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            // dd($data);

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
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.student.edit_study_group', $data);
    }

    /*
     * Function name : updateStudentStudyGroup
     * Purpose : tenant admin StudentStudyGroup update
     * Author  : SM
     * Created Date : 16-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateStudentStudyGroup(Request $request)
    {
        // $request->replace($request->except('subject_image'));
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/update';
            $form_params = [
                "study_group_id" => $request->study_group_id ?? '',
                "name" => $request->name ?? '',
                "description" => $request->description ?? '',
                "group_image" => $request->imagedata_group_image ?? '',
                "status" => $request->status ?? '',
            ];
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
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : viewStudentStudyGroup
     * Purpose : tenant user (student) study group view
     * Author  :
     * Created Date : 17-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function viewStudentStudyGroup(Request $request)
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
        // dd(Session()->get('user'));
        $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
        $data['active_status'] = GlobalVars::ACTIVE_STATUS;
        $data['inactive_status'] = GlobalVars::INACTIVE_STATUS;
        $study_group_id = $request->study_group_id ?? null;
        $data['study_group_id'] = $study_group_id;
        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? null;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/view';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "study_group_id" => $study_group_id,
                    "page" => $page,
                    "search_text" => $search_text,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['content_list'] = $response['result']['content_list'];
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['content_list']['last_page'];
                $current_page = $response['result']['content_list']['current_page'];
                $prev_page_url = $response['result']['content_list']['prev_page_url'];
                $next_page_url = $response['result']['content_list']['next_page_url'];

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
            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;

            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;

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
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.view_study_group', $data);

    }

    /*
     * Function name : addContentStudentStudyGroup
     * Purpose : tenant user (student) study group add content
     * Author  :
     * Created Date : 17-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addContentStudentStudyGroup(Request $request)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/add-content';
            $form_params = [
                "study_group_id" => $request->study_group_id ?? '',
                "content" => $request->content ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('tus_viewstudygroup', [Session()->get('tenant_info')['subdomain'], $request->study_group_id])->with($notification);

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
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.view_study_group', $data);

    }

    /*
     * Function name : inviteToStudentStudyGroup
     * Purpose : tenant user (student) study group add view
     * Author  :
     * Created Date : 19-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function inviteToStudentStudyGroup($subdomain, $study_group_id)
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

        return view('tenant.student.inviteto_study_group', compact('study_group_id'));

    }

    /*
     * Function name : saveInviteeStudentStudyGroup
     * Purpose : tenant user (student) study group save
     * Author  :
     * Created Date : 19-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveInviteeStudentStudyGroup(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/studygroup/invite';

            $form_params = [
                "study_group_id" => $request->study_group_id ?? '',
                "name" => $request->name ?? '',
                "email" => $request->email ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('tus_viewstudygroup', [Session()->get('tenant_info')['subdomain'], $request->study_group_id])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_studygroups', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : getGradeListing
     * Purpose : tenant admin grade listing
     * Author  :
     * Created Date : 29-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getGradeListing(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $boards = Session()->get('datalist_shortboards');
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/grades' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['listing']['last_page'];
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

            return view(
                'tenant.grade.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text', 'boards',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_academicyearlist');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_academicyearlist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addGrade
     * Purpose : tenant admin academic year add view
     * Author  :
     * Created Date : 29-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addGrade()
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
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['boards'] = $response['result']['boards'];
            // dd($data['boards']);

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
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

                // dd($response->error->message);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.grade.add', $data);

    }

    /*
     * Function name : saveGrade
     * Purpose : tenant admin grade save
     * Author  :
     * Created Date : 29-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveGrade(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-grade';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "grade" => $request->grade ?? '',
                    "board_id" => $request->board_id ?? '',
                    "min_value" => $request->min_value ?? '',
                    "max_value" => $request->max_value ?? '',
                    "effective_date" => $request->effective_date ?? '',
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editGrade
     * Purpose : tenant admin grade edit view
     * Author  : SM
     * Created Date : 29-04-2024
     * Modified date :
     * Params : grade_id
     * Return : void
     */
    public function editGrade($subdomain, $grade_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($academic_year_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-grade-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "grade_id" => $grade_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['boards'] = $response['result']['boards'];

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
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.grade.edit', $data);
    }

    /*
     * Function name : updateGrade
     * Purpose : tenant admin grade update
     * Author  : SM
     * Created Date : 29-04-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateGrade(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-grade';
            $form_params = [
                "grade_id" => $request->grade_id ?? '',
                "grade" => $request->grade ?? '',
                "board_id" => $request->board_id ?? '',
                "min_value" => $request->min_value ?? '',
                "max_value" => $request->max_value ?? '',
                "effective_date" => $request->effective_date ?? '',
                "status" => $request->status ?? '',
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
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importGrade
     * Purpose : tenant admin grade import view
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importGrade()
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
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/boards';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['boards'] = $response['result']['boards'];
            // dd($data['boards']);

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
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

                // dd($response->error->message);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.grade.import', $data);

    }

    /*
     * Function name : saveImportGrade
     * Purpose : tenant admin grade save import
     * Author  :
     * Created Date : 31-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportGrade(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-grades';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'board_id',
                        'contents' => $request->board_id ?? '',
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_gradelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getParentListing
     * Purpose : tenant admin parent listing
     * Author  :
     * Created Date : 16-05-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getParentListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        $no_file = GlobalVars::API_NO_FILE;
        $yes_file = GlobalVars::API_YES_FILE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parents';

            $formParams = [
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['item_list']['last_page'];
                $current_page = $response['result']['item_list']['current_page'];
                $prev_page_url = $response['result']['item_list']['prev_page_url'];
                $next_page_url = $response['result']['item_list']['next_page_url'];

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
                'tenant.parent.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'no_file', 'yes_file'
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
                    return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addParent
     * Purpose : tenant admin parent add view
     * Author  :
     * Created Date : 16-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addParent()
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
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

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
                return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.parent.add', $data);

    }

    /*
     * Function name : saveParent
     * Purpose : tenant admin parent save
     * Author  :
     * Created Date : 07-03-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveParent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-parent';
            $form_params = [
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "email" => $request->email ?? '',
                "password" => $request->password ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "address" => $request->address ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editParent
     * Purpose : tenant admin parent edit view
     * Author  : SM
     * Created Date : 16-05-2024
     * Modified date :
     * Params : employee_id
     * Return : details
     */
    public function editParent($subdomain, $parent_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-parent-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_id" => $parent_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            // dd($data);
            $apiEndpoint = config('app.api_base_url') . '/dropdown/genders';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['genders'] = $response['result']['genders'];

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
                return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.parent.edit', $data);
    }

    /*
     * Function name : updateParent
     * Purpose : tenant admin parent update
     * Author  : SM
     * Created Date : 16-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateParent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-parent';

            $form_params = [
                "user_id" => $request->parent_id ?? '',
                "first_name" => $request->first_name ?? '',
                "last_name" => $request->last_name ?? '',
                "phone" => $request->phone ?? '',
                "gender" => $request->gender ?? '',
                "address" => $request->address ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
                "status" => $request->status ?? 'Active',
            ];
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
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_parentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getChildListing
     * Purpose : tenant parent child listing
     * Author  :
     * Created Date : 21-05-2024
     * Modified date :
     * Params : request
     * Return : list
     */
    public function getChildListing(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        // dd($publicKey);
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $sayid = $request->sayid ?? '';
        $sygid = $request->sygid ?? '';
        $ssid = $request->ssid ?? '';
        $no_image = GlobalVars::API_NO_IMAGE;
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parent/children';

            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "page" => $page,
            ];
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $numOfpages = '';
            $current_page = '';
            $prev_page_url = '';
            $next_page_url = '';

            if ($response['status']) {
                $numOfpages = $response['result']['item_list']['last_page'];
                $current_page = $response['result']['item_list']['current_page'];
                $prev_page_url = $response['result']['item_list']['prev_page_url'];
                $next_page_url = $response['result']['item_list']['next_page_url'];

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
                'tenant.parent.children', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image', 'sayid', 'sygid', 'ssid'
                )
            );

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addChild
     * Purpose : tenant parent child add view
     * Author  :
     * Created Date : 21-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addChild()
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
        return view('tenant.parent.add-child');

    }

    /*
     * Function name : saveChild
     * Purpose : tenant admin parent save
     * Author  :
     * Created Date : 21-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveChild(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/generate-child-link';
            $form_params = [
                "child_email" => $request->email ?? '',
                "child_code" => $request->code ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : verifyChild
     * Purpose : tenant parent child add view
     * Author  :
     * Created Date : 21-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function verifyChild($subdomain, $token)
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
        $data = array();
        $tokend = \Helpers::decryptId($token);
        $fexplode = explode('^', $tokend);
        // dd($fexplode);
        $parent_user_id = null;
        $studentstr = null;
        $studentarr = null;
        $otp = null;
        $student_id = null;
        $student_email = null;
        if (is_array($fexplode)) {
            $parent_user_id = $fexplode[0];
            $studentstr = explode('#', $fexplode[1]);
            // dd($studentstr);
            if (is_array($studentstr)) {
                $otp = $studentstr[1];
                $studentarr = explode('~', $studentstr[0]);
                // dd($studentarr);
                if (is_array($studentarr)) {
                    $student_id = $studentarr[1];
                    $student_email = $studentarr[0];
                }
            }
        }
        // dd($student_id);
        if ($student_id != null) {
            $student_id = \Helpers::encryptId($student_id);
            try {

                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-student-by-id';
                $call = $client->post($apiEndpoint, [
                    'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                    'form_params' => [
                        "student_id" => $student_id,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $data['student_details'] = $response['result']['details'];
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
                    return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
                    //}
                }
                // You can check for whatever error status code you need

            } catch (Exception $e) {
                $notification = array(
                    'message' => $e->getMessage(),
                    'alert-type' => 'error',
                );
                return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
                // throw ($e);
            }

            return view('tenant.parent.verify-child', $data);
        } else {
            $notification = array(
                'message' => 'Unable to fetch details. /n Please try after some time.',
                'alert-type' => 'error',
            );
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
        }

    }

    /*
     * Function name : validateChild
     * Purpose : tenant admin parent save
     * Author  :
     * Created Date : 21-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function validateChild(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/validate-child';
            $form_params = [
                "child_email" => $request->email ?? '',
                "child_code" => $request->code ?? '',
                "otp" => $request->otp ?? '',
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
            $this->refreshLoginData();
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('p_children', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherMyLibrary
     * Purpose : tenant user (teacher) mylibrary view
     * Author  :
     * Created Date : 23-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherMyLibrary()
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['details'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['shortboards'] = Session()->get('datalist_shortboards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.my-library', $data);

    }

    /*
     * Function name : teacherMyLibraryContent
     * Purpose : tenant user (teacher) mylibrary lesson view
     * Author  :
     * Created Date : 23-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherMyLibraryContent($subdomain, $subject_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-subjectid-lessons';
            $form_params = [
                "subject_id" => $subject_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['subject_info'] = $response['result']['subject_info'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/supported-content-types';

            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            $data['library_content_types'] = $response['result']['listing'] ?? '';

            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.my-library-content', $data);

    }

    /*
     * Function name : teacherAddLibraryContent
     * Purpose : tenant user (teacher) mylibrary lesson content add
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherAddLibraryContent($subdomain, $lesson_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/dropdown/library-content-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lib_content_types'] = $response['result']['listing'];
            $data['type'] = request()->query('type') ?? '';

            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            // dd($data);

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.add_library_content', $data);

    }

    /*
     * Function name : teacherSaveLibraryContent
     * Purpose : tenant admin teacherSaveLibraryContent save
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherSaveLibraryContent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/upload-to-library';

            $client = new Client();
            if ($request->hasFile('content_file')) {
                $content_file_path = $request->file('content_file')->getPathname();
                $content_file_mime = $request->file('content_file')->getmimeType();
                $content_file_org = $request->file('content_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'content_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                ];
            } else {
                $multipart = [
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                ];

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherLibraryContentByType
     * Purpose : tenant user (teacher) mylibrary lesson content type view
     * Author  :
     * Created Date : 27-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherLibraryContentByType($subdomain, $lesson_id, $content_type)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-content-by-lessonntype';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
                "content_type" => $content_type ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['lesson_info'] = $response['result']['lesson_info'];
            $data['content_type'] = $content_type;
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['lib_content_types'] = $lib_content_types = GlobalVars::LIBRARY_CONTENT_TYPES;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.my-library-content-list', $data);

    }

    /*
     * Function name : viewLibraryFile
     * Purpose : tenant user (teacher) view library file
     * Author  :
     * Created Date : 28-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function viewLibraryFile($subdomain, $library_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-library-item';
            $form_params = [
                "library_id" => $library_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.file-view-library', $data);
    }

    /*
     * Function name : studentMyLibrary
     * Purpose : tenant user (student) mylibrary view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentMyLibrary()
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/my-courses';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['details'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['shortboards'] = Session()->get('datalist_shortboards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.my-library', $data);

    }

    /*
     * Function name : studentMyLibraryContent
     * Purpose : tenant user (student) mylibrary lesson view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentMyLibraryContent($subdomain, $subject_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-subjectid-lessons';
            $form_params = [
                "subject_id" => $subject_id ?? '',
                "status" => GlobalVars::ACTIVE_STATUS,
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['subject_info'] = $response['result']['subject_info'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.my-library-content', $data);

    }

    /*
     * Function name : studentLibraryContentByType
     * Purpose : tenant user (student) mylibrary lesson content type view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentLibraryContentByType($subdomain, $lesson_id, $content_type)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-content-by-lessonntype';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
                "content_type" => $content_type ?? '',
                "status" => GlobalVars::ACTIVE_STATUS,
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['lesson_info'] = $response['result']['lesson_info'];
            $data['content_type'] = $content_type;
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['lib_content_types'] = $lib_content_types = GlobalVars::LIBRARY_CONTENT_TYPES;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.student.my-library-content-list', $data);

    }

    /*
     * Function name : schoolLibrary
     * Purpose : tenant user (SA) library view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function schoolLibrary()
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['subject_list'] = $response['result']['subject_list'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['shortboards'] = Session()->get('datalist_shortboards');
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.library', $data);

    }

    /*
     * Function name : libraryContent
     * Purpose : tenant user (ta) library lesson view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function libraryContent($subdomain, $subject_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-subjectid-lessons';
            $form_params = [
                "subject_id" => $subject_id ?? '',
                "status" => GlobalVars::ACTIVE_STATUS,
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['subject_info'] = $response['result']['subject_info'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.library-content', $data);

    }

    /*
     * Function name : libraryContentByType
     * Purpose : tenant user (ta) mylibrary lesson content type view
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function libraryContentByType($subdomain, $lesson_id, $content_type)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/library/get-content-by-lessonntype';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
                "content_type" => $content_type ?? '',
                "status" => GlobalVars::ACTIVE_STATUS,
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['lesson_info'] = $response['result']['lesson_info'];
            $data['content_type'] = $content_type;
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            $data['lib_content_types'] = $lib_content_types = GlobalVars::LIBRARY_CONTENT_TYPES;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.library-content-list', $data);

    }

    /*
     * Function name : taAddLibraryContent
     * Purpose : tenant user (admin) mylibrary lesson content add
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function taAddLibraryContent($subdomain, $lesson_id)
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
        try {
            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/dropdown/library-content-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lib_content_types'] = $response['result']['listing'];
            $data['type'] = request()->query('type') ?? '';

            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            // dd($data);

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.add_library_content', $data);

    }

    /*
     * Function name : taSaveLibraryContent
     * Purpose : tenant admin taSaveLibraryContent save
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function taSaveLibraryContent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/upload-to-library';

            $client = new Client();
            if ($request->hasFile('content_file')) {
                $content_file_path = $request->file('content_file')->getPathname();
                $content_file_mime = $request->file('content_file')->getmimeType();
                $content_file_org = $request->file('content_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'content_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                ];
            } else {
                $multipart = [
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                ];

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_librarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : taEditLibraryContent
     * Purpose : tenant user (teacher) mylibrary lesson content edit
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function taEditLibraryContent($subdomain, $library_id)
    {
        // dd($library_id);

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $data['library_id'] = $library_id;

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-library-item';
            $form_params = [
                "library_id" => $library_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response['result']['details']['lesson_id']);
            $lesson_id = $response['result']['details']['lesson_id'];
            $lesson_id = CommonHelper::encryptId($lesson_id);

            $data['libdetails'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/dropdown/library-content-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lib_content_types'] = $response['result']['listing'];
            $data['type'] = request()->query('type') ?? '';

            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            // dd($data);
            $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.edit_library_content', $data);

    }

    /*
     * Function name : taUpdateLibraryContent
     * Purpose : tenant admin taUpdateLibraryContent save
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function taUpdateLibraryContent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-library-item';

            $client = new Client();
            if ($request->hasFile('content_file')) {
                $content_file_path = $request->file('content_file')->getPathname();
                $content_file_mime = $request->file('content_file')->getmimeType();
                $content_file_org = $request->file('content_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'content_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'library_id',
                        'contents' => $request->library_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                    [
                        'name' => 'status',
                        'contents' => $request->status ?? '',
                    ],
                ];
            } else {
                $multipart = [
                    [
                        'name' => 'library_id',
                        'contents' => $request->library_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                    [
                        'name' => 'status',
                        'contents' => $request->status ?? '',
                    ],
                ];

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_librarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_librarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_librarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : teacherEditLibraryContent
     * Purpose : tenant user (teacher) mylibrary lesson content edit
     * Author  :
     * Created Date : 24-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherEditLibraryContent($subdomain, $library_id)
    {
        // dd($library_id);

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $data['library_id'] = $library_id;

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-library-item';
            $form_params = [
                "library_id" => $library_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response['result']['details']['lesson_id']);
            $lesson_id = $response['result']['details']['lesson_id'];
            $lesson_id = CommonHelper::encryptId($lesson_id);

            $data['libdetails'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-lesson-by-id';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

            $apiEndpoint = config('app.api_base_url') . '/dropdown/library-content-types';
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                //'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['lib_content_types'] = $response['result']['listing'];
            $data['type'] = request()->query('type') ?? '';

            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            // dd($data);
            $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.teacher.edit_library_content', $data);

    }

    /*
     * Function name : teacherUpdateLibraryContent
     * Purpose : tenant admin teacherUpdateLibraryContent save
     * Author  :
     * Created Date : 29-05-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherUpdateLibraryContent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-library-item';

            $client = new Client();
            if ($request->hasFile('content_file')) {
                $content_file_path = $request->file('content_file')->getPathname();
                $content_file_mime = $request->file('content_file')->getmimeType();
                $content_file_org = $request->file('content_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'content_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'library_id',
                        'contents' => $request->library_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                    [
                        'name' => 'status',
                        'contents' => $request->status ?? '',
                    ],
                ];
            } else {
                $multipart = [
                    [
                        'name' => 'library_id',
                        'contents' => $request->library_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                    [
                        'name' => 'content_type',
                        'contents' => $request->content_type ?? '',
                    ],
                    [
                        'name' => 'title',
                        'contents' => $request->title ?? '',
                    ],
                    [
                        'name' => 'content_url',
                        'contents' => $request->content_url ?? '',
                    ],
                    [
                        'name' => 'status',
                        'contents' => $request->status ?? '',
                    ],
                ];

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            // dd($call->getBody()->getContents());
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_mylibrarycontent', [Session()->get('tenant_info')['subdomain'], $request->subject_id])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getOfsteadFinanceListing
     * Purpose : tenant admin ofstead finance listing
     * Author  :
     * Created Date : 04-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getOfsteadFinanceListing(Request $request)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/ofstead/finance' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['listing']['last_page'];
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

            return view(
                'tenant.ofstead.finance.index', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_ofinancelist');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_ofinancelist')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : importOfsteadFinance
     * Purpose : tenant admin ofstead finance import view
     * Author  :
     * Created Date : 04-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importOfsteadFinance()
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

        return view('tenant.ofstead.finance.import');

    }

    /*
     * Function name : saveImportOfsteadFinance
     * Purpose : tenant admin ofstead finance save import
     * Author  :
     * Created Date : 04-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportOfsteadFinance(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/ofstead/import-finance';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'academic_year_id',
                        'contents' => $request->academic_year_id ?? '',
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_ofinancelist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_ofinancelist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_ofinancelist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_ofinancelist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : viewOfsteadFinanceYear
     * Purpose : tenant admin ofstead finance year view
     * Author  :
     * Created Date : 05-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function viewOfsteadFinanceYear($subdomain, $year)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/ofstead/get-finance-year';

            $form_params = [
                "year" => $year ?? '',
            ];
            // dd($apiEndpoint);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response['result']['listing']);
            $data['listing'] = $response['result']['listing'];
            $data['yrval'] = CommonHelper::decryptId($year);
            return view(
                'tenant.ofstead.finance.view', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_ofinancelist');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_ofinancelist')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherAttendanceListing
     * Purpose : tenant (teacher) attendance listing
     * Author  :
     * Created Date : 10-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherAttendanceListing(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $search_academic_year_id = $request->search_academic_year_id ?? '';
        $search_year_group_id = $request->search_year_group_id ?? '';
        $search_subject_id = $request->search_subject_id ?? '';
        $search_lesson_id = $request->search_lesson_id ?? '';
        $search_date_from = $request->search_date_from ?? null;
        $search_date_to = $request->search_date_to ?? null;

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/attendances';

            $form_params = [
                "page" => $page,
                "search_text" => $search_text,
                "search_academic_year_id" => $search_academic_year_id,
                "search_year_group_id" => $search_year_group_id,
                "search_subject_id" => $search_subject_id,
                "search_lesson_id" => $search_lesson_id,
                "search_date_from" => $search_date_from,
                "search_date_to" => $search_date_to,
            ];

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

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
                $numOfpages = $response['result']['listing']['last_page'];
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

            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['search_academic_year_id'] = $search_academic_year_id;
            $data['search_year_group_id'] = $search_year_group_id;
            $data['search_subject_id'] = $search_subject_id;
            $data['search_lesson_id'] = $search_lesson_id;
            $data['search_date_from'] = $search_date_from;
            $data['search_date_to'] = $search_date_to;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
            $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

            return view('tenant.attendance.creator_index', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getTeacherAttendanceListingFilter
     * Purpose : tenant (teacher) attendance listing filter view
     * Author  :
     * Created Date : 10-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherAttendanceListingFilter(Request $request)
    {
        // dd($request->fp);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $fp = $request->fp ?? '';
        if ($fp != '') {
            $fp = CommonHelper::decryptId($request->fp);
            $fp = json_decode($fp);
            // dd($fp->page);
        }
        $search_text = $fp->search_text ?? '';
        $search_academic_year_id = $fp->search_academic_year_id ?? '';
        $search_year_group_id = $fp->search_year_group_id ?? '';
        $search_subject_id = $fp->search_subject_id ?? '';
        $search_lesson_id = $fp->search_lesson_id ?? '';
        $search_date_from = $fp->search_date_from ?? null;
        $search_date_to = $fp->search_date_to ?? null;

        $data['search_text'] = $search_text;
        $data['search_academic_year_id'] = $search_academic_year_id;
        $data['search_year_group_id'] = $search_year_group_id;
        $data['search_subject_id'] = $search_subject_id;
        $data['search_lesson_id'] = $search_lesson_id;
        $data['search_date_from'] = $search_date_from;
        $data['search_date_to'] = $search_date_to;

        return view('tenant.attendance.filter', $data);

    }

    /*
     * Function name : getStudentAttendanceListing
     * Purpose : tenant (student) attendance listing
     * Author  :
     * Created Date : 11-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getStudentAttendanceListing(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';
        $search_academic_year_id = $request->search_academic_year_id ?? '';
        $search_year_group_id = $request->search_year_group_id ?? '';
        $search_subject_id = $request->search_subject_id ?? '';
        $search_lesson_id = $request->search_lesson_id ?? '';
        $search_date_from = $request->search_date_from ?? null;
        $search_date_to = $request->search_date_to ?? null;

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/user/attendances';

            $form_params = [
                "page" => $page,
                "search_text" => $search_text,
                "search_academic_year_id" => $search_academic_year_id,
                "search_year_group_id" => $search_year_group_id,
                "search_subject_id" => $search_subject_id,
                "search_lesson_id" => $search_lesson_id,
                "search_date_from" => $search_date_from,
                "search_date_to" => $search_date_to,
            ];

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

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
                $numOfpages = $response['result']['listing']['last_page'];
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

            $data['numOfpages'] = $numOfpages;
            $data['current_page'] = $current_page;
            $data['response'] = $response;
            $data['prev_page'] = $prev_page;
            $data['next_page'] = $next_page;
            $data['search_text'] = $search_text;
            $data['search_academic_year_id'] = $search_academic_year_id;
            $data['search_year_group_id'] = $search_year_group_id;
            $data['search_subject_id'] = $search_subject_id;
            $data['search_lesson_id'] = $search_lesson_id;
            $data['search_date_from'] = $search_date_from;
            $data['search_date_to'] = $search_date_to;
            $data['has_next_page'] = $has_next_page;
            $data['has_previous_page'] = $has_previous_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['next_page_url'] = $next_page_url;
            $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

            return view('tenant.attendance.user_attendance', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getStudentAttendanceListingFilter
     * Purpose : tenant (student) attendance listing filter view
     * Author  :
     * Created Date : 10-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getStudentAttendanceListingFilter(Request $request)
    {
        // dd($request->fp);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $fp = $request->fp ?? '';
        if ($fp != '') {
            $fp = CommonHelper::decryptId($request->fp);
            $fp = json_decode($fp);
            // dd($fp->page);
        }
        $search_text = $fp->search_text ?? '';
        $search_academic_year_id = $fp->search_academic_year_id ?? '';
        $search_year_group_id = $fp->search_year_group_id ?? '';
        $search_subject_id = $fp->search_subject_id ?? '';
        $search_lesson_id = $fp->search_lesson_id ?? '';
        $search_date_from = $fp->search_date_from ?? null;
        $search_date_to = $fp->search_date_to ?? null;

        $data['search_text'] = $search_text;
        $data['search_academic_year_id'] = $search_academic_year_id;
        $data['search_year_group_id'] = $search_year_group_id;
        $data['search_subject_id'] = $search_subject_id;
        $data['search_lesson_id'] = $search_lesson_id;
        $data['search_date_from'] = $search_date_from;
        $data['search_date_to'] = $search_date_to;

        return view('tenant.attendance.user_filter', $data);

    }

    /*
     * Function name : importTeacherAttendance
     * Purpose : tenant (teacher) attendance import view
     * Author  :
     * Created Date : 11-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importTeacherAttendance()
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

        return view('tenant.attendance.import');

    }

    /*
     * Function name : saveImportTeacherAttendance
     * Purpose : tenant (teacher) attendance import save
     * Author  :
     * Created Date : 11-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportTeacherAttendance(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-attendance';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $request->subject_id ?? '',
                    ],
                    [
                        'name' => 'lesson_id',
                        'contents' => $request->lesson_id ?? '',
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherAttendanceUserListing
     * Purpose : tenant (teacher) attendance user listing
     * Author  :
     * Created Date : 10-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherAttendanceUserListing($subdomain, $attendance_date, $lesson_id)
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        // dd($userInfo);

        // $attendance_date = $request->attendance_date ?? '';
        // $lesson_id = $request->lesson_id ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/attendance/view-list';

            $form_params = [
                "attendance_date" => $attendance_date ?? '',
                "lesson_id" => $lesson_id ?? '',
            ];
            // dd(json_encode($form_params));
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['boards'] = Session()->get('datalist_boards');
            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
            // dd($response);

            return view('tenant.attendance.creator_attnusers', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_attendances', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : addAttendance
     * Purpose : tenant admin ofstead finance year view
     * Author  :
     * Created Date : 07-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addAttendance()
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
        $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE_AVAILABLE;
        return view('tenant.attendance.add', $data);
    }

    /*
     * Function name : saveAttendance
     * Purpose : tenant admin ofstead finance year view
     * Author  :
     * Created Date : 07-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveAttendance(Request $request)
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
        try {
            // dd($request->all());

            $user_details = array();
            $reqUserId = $request->user_id;
            for ($i = 0; $i < count($reqUserId); $i++) {
                $uid = $reqUserId[$i] ?? null;
                $ele = [
                    "user_id" => $uid,
                    "is_present" => $request->input('attendance_' . $uid) ?? 0,
                    "remarks" => $request->input('comment_' . $uid) ?? null,
                ];
                if ($ele['is_present']) {
                    $ele['is_present'] = 1;
                }

                array_push($user_details, $ele);
            }
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/attendance/create-or-update';
            $form_params = [
                "subject_id" => $request->subject_id ?? '',
                "lesson_id" => $request->lesson_id ?? '',
                "attendance_date" => $request->attendance_date ?? null,
                "user_details" => $user_details ?? array(),
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
            $this->refreshLoginData();
            return \Redirect::route('tut_attendance_add', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('tut_attendance_add', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_attendance_add', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
    }

    /*
     * Function name : importStudent
     * Purpose : tenant admin student import view
     * Author  :
     * Created Date : 14-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importStudent()
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
        $client = new Client();
        $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-batch-types';
        // dd($apiEndpoint);
        $call = $client->post($apiEndpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            //'body' => json_encode($data),
        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        // dd($response);
        $data['batch_types'] = $response['result']['batch_types'];

        $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
        // dd($apiEndpoint);
        $formParams = [
            "status" => 'Active',
        ];
        $call = $client->post($apiEndpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            'form_params' => $formParams,
        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        // dd($response);
        $data['subject_list'] = $response['result']['subject_list'];
        $data['shortboards'] = Session()->get('datalist_shortboards');
        return view('tenant.student.import', $data);

    }

    /*
     * Function name : saveImportStudent
     * Purpose : tenant admin student save import
     * Author  :
     * Created Date : 14-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportStudent(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-students';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $subject_id = null;
                if (isset($request->subject_id) && count($request->subject_id) > 0) {
                    $subject_id = implode(',', $request->subject_id);
                }
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $subject_id ?? '',
                    ],
                    [
                        'name' => 'batch_type_id',
                        'contents' => $request->batch_type_id ?? 1,
                    ],
                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_studentlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : importTeacher
     * Purpose : tenant admin teacher import view
     * Author  :
     * Created Date : 14-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function importTeacher()
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
        $client = new Client();

        $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/dropdown/get-all-subjects';
        // dd($apiEndpoint);
        $formParams = [
            "status" => 'Active',
        ];
        $call = $client->post($apiEndpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            'form_params' => $formParams,
        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        // dd($response);
        $data['subject_list'] = $response['result']['subject_list'];
        $data['shortboards'] = Session()->get('datalist_shortboards');
        return view('tenant.teacher.import', $data);

    }

    /*
     * Function name : saveImportTeacher
     * Purpose : tenant admin teacher save import
     * Author  :
     * Created Date : 14-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveImportTeacher(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAllowedUsers = $this->checkAllowedUsers();
        if ($checkAllowedUsers == false) {
            $notification = array(
                'message' => 'You have reached maximum user count as per subscription taken.',
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/import-teachers';
            if ($request->hasFile('import_file')) {
                $content_file_path = $request->file('import_file')->getPathname();
                $content_file_mime = $request->file('import_file')->getmimeType();
                $content_file_org = $request->file('import_file')->getClientOriginalName();
                $subject_id = null;
                if (isset($request->subject_id) && count($request->subject_id) > 0) {
                    $subject_id = implode(',', $request->subject_id);
                }
                $multipart = [
                    [
                        'name' => 'import_file',
                        'filename' => $content_file_org,
                        'contents' => fopen($content_file_path, 'r'),
                    ],
                    [
                        'name' => 'subject_id',
                        'contents' => $subject_id ?? '',
                    ],

                ];
            } else {
                $notification = array(
                    'message' => 'No file to import.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);

            }
            // print_r($request->all());
            // dd($body);
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'multipart' => $multipart,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $notification = array(
                'message' => $response['result']['message'],
                'alert-type' => 'success',
            );
            $this->refreshLoginData();
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 400";
                $response = json_decode($e->getResponse()->getBody()->getContents());
                // dd($response);
                $notification = array(
                    'message' => $response->error->message ?? 'api exception happened.',
                    'alert-type' => 'error',
                );
                // dd($response->error->message);
                return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_teacherlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getParentUserQuizResultListing
     * Purpose : tenant parent exam results view
     * Author  :
     * Created Date : 18-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getParentUserQuizResultListing(Request $request)
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
        try {
            $search_text = $request->search_text ?? '';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';
            $studid = $request->studid ?? '';
            $examination_type = 'Q';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parent/examination/results';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_student_id" => $studid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "examination_type" => $examination_type,
            ];

            // dd(json_encode($formParams));

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['response'] = $response;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['studid'] = $studid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.parent.quiz-results', $data);

    }

    /*
     * Function name : getParentUserAssessmentResultListing
     * Purpose : tenant parent exam results view
     * Author  :
     * Created Date : 18-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getParentUserAssessmentResultListing(Request $request)
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
        try {
            $search_text = $request->search_text ?? '';
            $sayid = $request->sayid ?? '';
            $sygid = $request->sygid ?? '';
            $ssid = $request->ssid ?? '';
            $studid = $request->studid ?? '';
            $examination_type = 'A';

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parent/examination/results';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $formParams = [
                "search_academic_year_id" => $sayid,
                "search_student_id" => $studid,
                "search_year_group_id" => $sygid,
                "search_subject_id" => $ssid,
                "search_text" => $search_text,
                "examination_type" => $examination_type,
            ];

            // dd(json_encode($formParams));

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['response'] = $response;
            $data['search_text'] = $search_text;
            $data['sayid'] = $sayid;
            $data['studid'] = $studid;
            $data['sygid'] = $sygid;
            $data['ssid'] = $ssid;
            $data['examination_type'] = $examination_type;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.parent.assessment-results', $data);

    }

    /*
     * Function name : getParentUserAttendanceListing
     * Purpose : tenant parent exam results view
     * Author  :
     * Created Date : 19-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getParentUserAttendanceListing(Request $request)
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
        try {
            $search_academic_year_id = $request->search_academic_year_id ?? '';
            $search_year_group_id = $request->search_year_group_id ?? '';
            $search_subject_id = $request->search_subject_id ?? '';
            $search_lesson_id = $request->search_lesson_id ?? '';
            $search_student = $request->search_student ?? '';
            $search_date_from = $request->search_date_from ?? null;
            $search_date_to = $request->search_date_to ?? null;

            $client = new Client();

            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/parent/attendances';
            // dd($apiEndpoint);
            //. '?search_text=' . $search_text . '&page=' . $page
            $formParams = [
                "search_academic_year_id" => $search_academic_year_id,
                "search_student" => $search_student,
                "search_year_group_id" => $search_year_group_id,
                "search_subject_id" => $search_subject_id,
                "search_lesson_id" => $search_lesson_id,
                "search_date_from" => $search_date_from,
                "search_date_to" => $search_date_to,
            ];

            $form_params = CommonHelper::encryptId(json_encode($formParams));

            // dd(json_encode($formParams));

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $formParams,
            ]);

            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['response'] = $response;
            $data['search_student'] = $search_student;
            $data['search_academic_year_id'] = $search_academic_year_id;
            $data['search_year_group_id'] = $search_year_group_id;
            $data['search_subject_id'] = $search_subject_id;
            $data['search_lesson_id'] = $search_lesson_id;
            $data['search_date_from'] = $search_date_from;
            $data['search_date_to'] = $search_date_to;
            $data['form_params'] = $form_params;
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }
        return view('tenant.parent.attendance', $data);

    }

    /*
     * Function name : getParentUserAttendanceListingFilter
     * Purpose : tenant (parent) attendance listing filter view
     * Author  :
     * Created Date : 19-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getParentUserAttendanceListingFilter(Request $request)
    {
        // dd($request->fp);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $fp = $request->fp ?? '';
        if ($fp != '') {
            $fp = CommonHelper::decryptId($request->fp);
            $fp = json_decode($fp);
            // dd($fp->page);
        }
        $search_student = $fp->search_student ?? '';
        $search_academic_year_id = $fp->search_academic_year_id ?? '';
        $search_year_group_id = $fp->search_year_group_id ?? '';
        $search_subject_id = $fp->search_subject_id ?? '';
        $search_lesson_id = $fp->search_lesson_id ?? '';
        $search_date_from = $fp->search_date_from ?? null;
        $search_date_to = $fp->search_date_to ?? null;

        $data['search_student'] = $search_student;
        $data['search_academic_year_id'] = $search_academic_year_id;
        $data['search_year_group_id'] = $search_year_group_id;
        $data['search_subject_id'] = $search_subject_id;
        $data['search_lesson_id'] = $search_lesson_id;
        $data['search_date_from'] = $search_date_from;
        $data['search_date_to'] = $search_date_to;

        return view('tenant.parent.attendance-filter', $data);

    }

    /*
     * Function name : parentReviewedAnswers
     * Purpose : tenant user (parent) quizes view
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function parentReviewedAnswers($subdomain, $user_result_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-examination-submission-info';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_result_id" => $user_result_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['user_result_id'] = $user_result_id;
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
                return \Redirect::route('p_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('p_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.parent.reviewed_answers', $data);

    }

    /*
     * Function name : teacherReviewedAnswers
     * Purpose : tenant user (parent) quizes view
     * Author  :
     * Created Date : 27-06-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherReviewedAnswers($subdomain, $user_result_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;

        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/examination/get-examination-submission-info';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "user_result_id" => $user_result_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];
            $data['user_result_id'] = $user_result_id;
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
                return \Redirect::route('tut_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_reviewed_answers', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.teacher.reviewed_answers', $data);

    }

    /*
     * Function name : editStudentCoverImage
     * Purpose : tenant admin student edit view
     * Author  : SM
     * Created Date : 05-07-2024
     * Modified date :
     * Params : student_id
     * Return : details
     */
    public function editStudentCoverImage($subdomain, $student_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('user');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-student-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "student_id" => $student_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['student_details'] = $response['result']['details'];
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.student.edit-cover-image', $data);
    }

    /*
     * Function name : editStudentProfileImage
     * Purpose : tenant admin student edit view
     * Author  : SM
     * Created Date : 05-07-2024
     * Modified date :
     * Params : student_id
     * Return : details
     */
    public function editStudentProfileImage($subdomain, $student_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($subject_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $checkAccess = $this->checkAccess('user');
        if ($checkAccess == false) {
            $notification = array(
                'message' => 'You are not authorized to access this section.',
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
        }
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-student-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "student_id" => $student_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['student_details'] = $response['result']['details'];
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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
        }

        return view('tenant.student.edit-profile-image', $data);
    }

    /*
     * Function name : updateStudentCoverImage
     * Purpose : tenant user (student) cover image update
     * Author  : SM
     * Created Date : 05-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateStudentCoverImage(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/update-cover-image';

            $form_params = [
                "student_id" => $request->student_id ?? '',
                "cover_picture" => $request->imagedata_cover_picture ?? '',
            ];
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
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : updateStudentProfileImage
     * Purpose : tenant user (student) profile image update
     * Author  : SM
     * Created Date : 05-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function updateStudentProfileImage(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/update-profile-image';

            $form_params = [
                "student_id" => $request->student_id ?? '',
                "profile_image" => $request->imagedata_profile_image ?? '',
            ];
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
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherSkillmap
     * Purpose : tenant (teacher) skllmap
     * Author  :
     * Created Date : 24-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherSkillmap(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['listing'] = array();
        if ($request->isMethod('post')) {
            $data['year_group_id'] = $year_group_id = $request->year_group_id ?? '';
            $data['subject_id'] = $subject_id = $request->subject_id ?? '';
            $data['lesson_id'] = $lesson_id = $request->lesson_id ?? '';

            if ($lesson_id != '') {
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/get-lessonid-skillmap';

                    $form_params = [
                        "lesson_id" => $lesson_id,
                    ];

                    // dd(json_encode($form_params));
                    // dd($apiEndpoint);

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);

                    $data['listing'] = $response['result']['listing'];

                    $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

                } catch (RequestException $e) {
                    // throw ($e);
                    // Catch all 4XX errors
                    // To catch exactly error 400 use
                    if ($e->hasResponse()) {
                        $response = json_decode($e->getResponse()->getBody()->getContents());
                        if ($e->getResponse()->getStatusCode() == '400') {
                            // echo "Got response 401";
                            $this->refreshLoginData();
                            return \Redirect::route('tut_skillmap', Session()->get('tenant_info')['subdomain']);
                        }
                        return \Redirect::route('front_flush')->withErrors($response->error->message);
                    }
                    // You can check for whatever error status code you need

                } catch (\Exception $e) {
                    //buy a beer
                    // throw ($e);
                    return \Redirect::route('tut_skillmap', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
                }
            }
        }

        return view('tenant.teacher.skillmap', $data);

    }

    /*
     * Function name : getTeacherAdaptiveLearn
     * Purpose : tenant (teacher) adaptive learning
     * Author  :
     * Created Date : 24-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherAdaptiveLearn(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['listing'] = array();
        if ($request->isMethod('post')) {
            $data['year_group_id'] = $year_group_id = $request->year_group_id ?? '';
            $data['subject_id'] = $subject_id = $request->subject_id ?? '';
            $data['lesson_id'] = $lesson_id = $request->lesson_id ?? '';
            $data['user_id'] = $user_id = $request->user_id ?? '';

            if ($lesson_id != '') {
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/get-student-lessonid-skillmap';

                    $form_params = [
                        "lesson_id" => $lesson_id,
                        "user_id" => $user_id,
                    ];

                    // dd(json_encode($form_params));
                    // dd($apiEndpoint);

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);

                    $data['listing'] = $response['result']['listing'];

                    $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

                } catch (RequestException $e) {
                    // throw ($e);
                    // Catch all 4XX errors
                    // To catch exactly error 400 use
                    if ($e->hasResponse()) {
                        $response = json_decode($e->getResponse()->getBody()->getContents());
                        if ($e->getResponse()->getStatusCode() == '400') {
                            // echo "Got response 401";
                            $this->refreshLoginData();
                            return \Redirect::route('tut_adaptivelearn', Session()->get('tenant_info')['subdomain']);
                        }
                        return \Redirect::route('front_flush')->withErrors($response->error->message);
                    }
                    // You can check for whatever error status code you need

                } catch (\Exception $e) {
                    //buy a beer
                    // throw ($e);
                    return \Redirect::route('tut_adaptivelearn', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
                }
            }
        }

        return view('tenant.teacher.student-skillmap', $data);

    }

    /*
     * Function name : getStudentSkillmap
     * Purpose : tenant (student) skillmap
     * Author  :
     * Created Date : 24-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getStudentSkillmap(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['listing'] = array();
        if ($request->isMethod('post')) {
            $data['year_group_id'] = $year_group_id = $request->year_group_id ?? '';
            $data['subject_id'] = $subject_id = $request->subject_id ?? '';
            $data['lesson_id'] = $lesson_id = $request->lesson_id ?? '';

            if ($lesson_id != '') {
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/get-lessonid-skillmap';

                    $form_params = [
                        "lesson_id" => $lesson_id,
                    ];

                    // dd(json_encode($form_params));
                    // dd($apiEndpoint);

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);

                    $data['listing'] = $response['result']['listing'];

                    $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

                } catch (RequestException $e) {
                    // throw ($e);
                    // Catch all 4XX errors
                    // To catch exactly error 400 use
                    if ($e->hasResponse()) {
                        $response = json_decode($e->getResponse()->getBody()->getContents());
                        if ($e->getResponse()->getStatusCode() == '400') {
                            // echo "Got response 401";
                            $this->refreshLoginData();
                            return \Redirect::route('tus_skillmap', Session()->get('tenant_info')['subdomain']);
                        }
                        return \Redirect::route('front_flush')->withErrors($response->error->message);
                    }
                    // You can check for whatever error status code you need

                } catch (\Exception $e) {
                    //buy a beer
                    // throw ($e);
                    return \Redirect::route('tus_skillmap', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
                }
            }
        }

        return view('tenant.student.skillmap', $data);

    }

    /*
     * Function name : getTeacherStudentTarget
     * Purpose : tenant (teacher) student target
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherStudentTarget(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        $page = $request->page ?? '1';
        $search_text = $request->search_text ?? '';

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/targets' . '?search_text=' . $search_text . '&page=' . $page;

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
                $numOfpages = $response['result']['listing']['last_page'];
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
                'tenant.teacher.targets', compact(
                    'numOfpages', 'current_page', 'response', 'prev_page', 'next_page', 'search_text',
                    'has_next_page', 'has_previous_page', 'prev_page_url', 'next_page_url', 'no_image'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tut_starget');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_starget')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherStudentTargetAdd
     * Purpose : tenant (teacher) student target add
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherAddStudentTarget(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['listing'] = array();

        return view('tenant.teacher.add-target', $data);

    }

    /*
     * Function name : getTeacherSaveStudentTarget
     * Purpose : tenant (teacher) student target save
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherSaveStudentTarget(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {
            // $userInfo = Session::get('user');
            // $profileInfo = Session::get('profile_info');
            // $tenantInfo = Session::get('tenant_info');

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/create-target';

            $targets = array();
            $reqSubjectIds = $request->subject_id ?? array();
            $reqTarget = $request->target ?? array();
            $reqTargetDate = $request->target_date ?? array();
            for ($i = 0; $i < count($reqSubjectIds); $i++) {
                $subject_id = $reqSubjectIds[$i] ?? null;
                $target = $reqTarget[$i] ?? null;
                $target_date = $reqTargetDate[$i] ?? null;
                $ele = [
                    "subject_id" => $subject_id,
                    "target" => $target,
                    "target_date" => $target_date,
                ];

                array_push($targets, $ele);
            }

            $form_params = [
                "user_id" => $request->user_id ?? '',
                "set_date" => $request->set_date ?? null,
                "year_group_id" => $request->year_group_id ?? null,
                "targets" => $targets ?? array(),
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
            return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : editTeacherStudentTarget
     * Purpose : tenant admin student target edit view
     * Author  : SM
     * Created Date : 30-07-2024
     * Modified date :
     * Params : target_id
     * Return : void
     */
    public function editTeacherStudentTarget($subdomain, $target_id)
    {
        $data['status'] = GlobalVars::GENERAL_RECORD_STATUS;
        // dd($academic_year_id);
        // dd(request()->segments());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-target-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "target_id" => $target_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

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
                return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.teacher.edit-target', $data);
    }

    /*
     * Function name : teacherUpdateStudentTarget
     * Purpose : tenant (teacher) student target update
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherUpdateStudentTarget(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/update-target';

            $targets = array();
            $reqSubjectIds = $request->subject_id ?? array();
            $reqTarget = $request->target ?? array();
            $reqTargetDate = $request->target_date ?? array();
            for ($i = 0; $i < count($reqSubjectIds); $i++) {
                $subject_id = $reqSubjectIds[$i] ?? null;
                $target = $reqTarget[$i] ?? null;
                $target_date = $reqTargetDate[$i] ?? null;
                $ele = [
                    "subject_id" => $subject_id,
                    "target" => $target,
                    "target_date" => $target_date,
                ];

                array_push($targets, $ele);
            }

            $form_params = [
                "target_id" => $request->target_id ?? '',
                "year_group_id" => $request->year_group_id ?? null,
                "user_id" => $request->user_id ?? '',
                "set_date" => $request->set_date ?? null,
                "targets" => $targets ?? array(),
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
            return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_starget', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : getStudentTargets
     * Purpose : tenant (student) student target
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getStudentTargets(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/consumer/targets';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $listing = $response['result']['listing'];

            $no_image = GlobalVars::API_NO_IMAGE;
            return view(
                'tenant.student.my-targets', compact(
                    'listing', 'no_image'
                )
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_starget');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_starget')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : getTeacherCourseStatus
     * Purpose : tenant (teacher) course comletion status
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getTeacherCourseStatus(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/course-status';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

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
                return \Redirect::route('tut_coursestatus', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_coursestatus', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

        return view('tenant.teacher.course_status', $data);

    }

    /*
     * Function name : teacherMyRating
     * Purpose : tenant (teacher) my rating
     * Author  :
     * Created Date : 09-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherMyRating(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/rating/consumer-by-lesson';

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

            return view('tenant.teacher.my-rating', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : shareTeacherRating
     * Purpose : tenant (student) teacher rating
     * Author  :
     * Created Date : 1-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function shareTeacherRating(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/user/all-attendances-for-rating';

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

            return view('tenant.student.teacher-rating', $data);

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_teacherrating');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_teacherrating')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : addViewTeacherRating
     * Purpose : tenant (student) teacher rating add/view
     * Author  :
     * Created Date : 09-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function addViewTeacherRating($subdomain, $teacher_id, $lesson_id)
    {
        // dd($teacher_id);
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/rating/fetch-by-lesson';
            $form_params = [
                "lesson_id" => $lesson_id ?? '',
                "teacher_id" => $teacher_id ?? '',
            ];
            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['rating'] = $response['result']['rating'];
            $data['lesson_info'] = $response['result']['lesson_info'];
            $data['teacher_info'] = $response['result']['teacher_info'];

            return view('tenant.student.add-view-rating', $data);

        } catch (RequestException $e) {
            throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                //if ($e->getResponse()->getStatusCode() == '400') {
                // echo "Got response 401";
                $this->refreshLoginData();
                return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain']);
                //}
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            throw ($e);
            return \Redirect::route('tus_attendances', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : saveStudentsTeacherRating
     * Purpose : tenant (student) save teacher and content rating
     * Author  :
     * Created Date : 09-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function saveStudentsTeacherRating(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/rating/create';

            $form_params = [
                "creator_rating" => $request->creator_rating ?? '',
                "content_rating" => $request->content_rating ?? null,
                "creator_remarks" => $request->creator_remarks ?? '',
                "content_remarks" => $request->content_remarks ?? null,
                "subject_id" => $request->subject_id ?? null,
                "lesson_id" => $request->lesson_id ?? null,
                "creator_id" => $request->creator_id ?? null,
                "academic_year_id" => $request->academic_year_id ?? null,
                "year_group_id" => $request->year_group_id ?? null,
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
            return \Redirect::route('tus_teacherrating', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tus_teacherrating', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            // throw ($e);
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_teacherrating', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : ntpSupport
     * Purpose : tenant (student) NTP Support rating
     * Author  :
     * Created Date : 2-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function ntpSupport(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.ntp-support'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_ntpsupport');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_ntpsupport')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentDashboard
     * Purpose : tenant (student) Student Dashboard
     * Author  :
     * Created Date : 2-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentDashboard(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.dashboard'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_studentdashboard');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_studentdashboard')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentReportcard
     * Purpose : tenant (student) Student Report Card
     * Author  :
     * Created Date : 2-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReportcard(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.report-card'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_studentreportcard');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_studentreportcard')->withErrors($e->getMessage());
        }

    }
    /*
     * Function name : getStudentCourseStatus
     * Purpose : tenant (student) course comletion status
     * Author  :
     * Created Date : 29-07-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getStudentCourseStatus(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/student/course-status';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

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
                return \Redirect::route('tus_coursestatus', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_coursestatus', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

        return view('tenant.student.course_status', $data);

    }

    /*
     * Function name : taInbox
     * Purpose : tenant (admin) inbox
     * Author  :
     * Created Date : 1-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function taInbox(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        /*  $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');*/

        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/message/get-user-messages';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE;

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
                return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.inbox', $data);
    }

    /*
     * Function name : taAddMessage
     * Purpose : tenant admin add message
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function taAddMessage()
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

        $data['created_for'] = [
            'All' => 'All Teachers & Students',
            'All Teachers' => 'All Teachers',
            'All Students' => 'All Students',
            'Specifc Teachers' => 'Specifc Teachers',
            'Specifc Students' => 'Specifc Students',
        ];

        return view('tenant.add-message', $data);
    }

    /*
     * Function name : taSendMessage
     * Purpose : tenant admin send message
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function taSendMessage(Request $request)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/message/create';

            $form_params = [
                "subject" => $request->subject ?? '',
                "message" => $request->message ?? '',
                "created_for" => $request->created_for ?? '',
                "users" => $request->users ?? array(),
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
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : viewMessage
     * Purpose : tenant view message
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params : message_id
     * Return : void
     */
    public function viewMessage($subdomain, $message_id)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/message/get-user-message-details';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "message_id" => $message_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['details'] = $response['result']['details'];

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
                // return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
                throw ($e);
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            // return \Redirect::route('ta_academicyearlist', Session()->get('tenant_info')['subdomain'])->with($notification);
            // return false;
            throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.view-message', $data);
    }

    /*
     * Function name : tusInbox
     * Purpose : tenant (student) inbox
     * Author  :
     * Created Date : 1-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tusInbox(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        /*  $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');*/

        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/message/get-user-messages';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE;

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
                return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.student.inbox', $data);
    }

    /*
     * Function name : tutInbox
     * Purpose : tenant (teacher) inbox
     * Author  :
     * Created Date : 1-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tutInbox(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        /*  $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');*/

        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/message/get-user-messages';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                //'body' => json_encode($data),
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

            $data['no_image'] = $no_image = GlobalVars::API_NO_IMAGE;

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
                return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.teacher.inbox', $data);
    }

    /*
     * Function name : viewStudentTaskCalendar
     * Purpose : tenant view task calendar
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function viewStudentTaskCalendar(Request $request)
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
        if ($request->ajax()) {
            try {

                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/async';

                $call = $client->post($apiEndpoint, [
                    'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                    'form_params' => [
                        'start_date' => $request->start,
                        'end_date' => $request->end,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $listing = $response['result']['listing'];
                $data = array();

                if (count($listing) > 0) {
                    foreach ($listing as $record) {
                        $ele = [
                            'title' => $record['task'],
                            'start' => $record['start_date'] . 'T10:00:00',
                            'end' => $record['end_date'] . 'T12:00:00',
                            'id' => $record['task_id'],
                        ];
                        array_push($data, $ele);
                    }
                }

                // $data = Event::whereDate('start', '>=', $request->start)
                // ->whereDate('end', '<=', $request->end)
                // ->get(['id', 'title', 'start', 'end']);

                return response()->json($data);

            } catch (RequestException $e) {
                // Catch all 4XX errors
                // To catch exactly error 400 use
                if ($e->hasResponse()) {
                    //if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 400";

                    // $response = json_decode($e->getResponse()->getBody()->getContents());
                    // $notification = array(
                    //     'message' => $response->error->message,
                    //     'alert-type' => 'error',
                    // );
                    // // dd($response->error->message);
                    // return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);

                    //}
                }
                // You can check for whatever error status code you need

            } catch (Exception $e) {
                // $notification = array(
                //     'message' => $e->getMessage(),
                //     'alert-type' => 'error',
                // );
                // return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                // throw ($e);
                // throw new \App\Exceptions\AdminException($e->getMessage());
            }

        }
        return view('tenant.student.calendar');
    }

    /*
     * Function name : viewTeacherTaskCalendar
     * Purpose : tenant view task calendar
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function viewTeacherTaskCalendar(Request $request)
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
        if ($request->ajax()) {
            try {

                $client = new Client();
                $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/async';

                $call = $client->post($apiEndpoint, [
                    'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                    'form_params' => [
                        'start_date' => $request->start,
                        'end_date' => $request->end,
                    ],
                ]);
                $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $listing = $response['result']['listing'];
                $data = array();

                if (count($listing) > 0) {
                    foreach ($listing as $record) {
                        $color = '#428f88';
                        if ($record['task_type'] == 'H') {
                            $color = '#3d73dd';
                        }
                        $ele = [
                            'title' => $record['task'],
                            'start' => $record['start_date'] . 'T10:00:00',
                            'end' => $record['end_date'] . 'T12:00:00',
                            'id' => $record['task_id'],
                            'color' => $color,
                        ];
                        array_push($data, $ele);
                    }
                }

                // $data = Event::whereDate('start', '>=', $request->start)
                // ->whereDate('end', '<=', $request->end)
                // ->get(['id', 'title', 'start', 'end']);

                return response()->json($data);

            } catch (RequestException $e) {
                // Catch all 4XX errors
                // To catch exactly error 400 use
                if ($e->hasResponse()) {
                    //if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 400";

                    // $response = json_decode($e->getResponse()->getBody()->getContents());
                    // $notification = array(
                    //     'message' => $response->error->message,
                    //     'alert-type' => 'error',
                    // );
                    // // dd($response->error->message);
                    // return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);

                    //}
                }
                // You can check for whatever error status code you need

            } catch (Exception $e) {
                // $notification = array(
                //     'message' => $e->getMessage(),
                //     'alert-type' => 'error',
                // );
                // return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
                // throw ($e);
                // throw new \App\Exceptions\AdminException($e->getMessage());
            }

        }
        return view('tenant.teacher.calendar');
    }

    /*
     * Function name : teacherAddTask
     * Purpose : tenant teacher add task
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function teacherAddTask()
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

        $data['created_for'] = [
            'All Students' => 'All Students',
            'Specifc Students' => 'Specifc Students',
        ];
        $data['task_type'] = [
            // 'N' => 'Perssonal Note',
            'Q' => 'Assign Quizes',
            'A' => 'Assign Assessments',
            'H' => 'Assign Homework',
        ];
        $data['shortboards'] = Session()->get('datalist_shortboards');
        return view('tenant.teacher.add-task', $data);
    }

    /*
     * Function name : teacherSaveTask
     * Purpose : tenant teacher save task
     * Author  : SM
     * Created Date : 06-08-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function teacherSaveTask(Request $request)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/create';

            $form_params = [
                "start_date" => $request->start_date ?? '',
                "end_date" => $request->end_date ?? '',
                "task_type" => $request->task_type ?? '',
                "task" => $request->task ?? '',
                "created_for" => $request->created_for ?? '',
                "users" => $request->users ?? array(),
                "examinations" => $request->mexam ?? array(),
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
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : studentSignals
     * Purpose : tenant (student) Student Signals
     * Author  :
     * Created Date : 5-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentSignals(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.signals'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_studentsignals');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_studentsignals')->withErrors($e->getMessage());
        }

    }
    /*
     * Function name : studentAnalyticStudio
     * Purpose : tenant (student) Student Analytic Studio
     * Author  :
     * Created Date : 5-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentAnalyticStudio(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.analytic'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_analyticstudio');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_analyticstudio')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : teacherViewTask
     * Purpose : tenant teacher view task
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function teacherViewTask($subdomain, $task_id)
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

        $data['created_for'] = [
            'All Students' => 'All Students',
            'Specifc Students' => 'Specifc Students',
        ];
        $data['task_type'] = [
            // 'N' => 'Perssonal Note',
            'Q' => 'Assign Quizes',
            'A' => 'Assign Assessments',
            'H' => 'Assign Homework',
        ];
        $data['shortboards'] = Session()->get('datalist_shortboards');
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "task_id" => $task_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['task_details'] = $response['result']['details'];
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
                return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.teacher.view-task', $data);
    }
    /*
     * Function name : teacherEditTask
     * Purpose : tenant teacher eddit task
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function teacherEditTask($subdomain, $task_id)
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

        $data['created_for'] = [
            'All Students' => 'All Students',
            'Specifc Students' => 'Specifc Students',
        ];
        $data['task_type'] = [
            // 'N' => 'Perssonal Note',
            'Q' => 'Assign Quizes',
            'A' => 'Assign Assessments',
            'H' => 'Assign Homework',
        ];
        $data['shortboards'] = Session()->get('datalist_shortboards');
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get-by-id';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "task_id" => $task_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['task_details'] = $response['result']['details'];
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
                return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.teacher.edit-task', $data);
    }

    /*
     * Function name : teacherUpdateTask
     * Purpose : tenant teacher update task
     * Author  : SM
     * Created Date : 06-08-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function teacherUpdateTask(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/update';

            $form_params = [
                "task_id" => $request->task_id ?? '',
                "start_date" => $request->start_date ?? '',
                "end_date" => $request->end_date ?? '',
                "task_type" => $request->task_type ?? '',
                "task" => $request->task ?? '',
                "created_for" => $request->created_for ?? '',
                "users" => $request->users ?? array(),
                "examinations" => $request->mexam ?? array(),
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
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);

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
                return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }
    /*
     * Function name : viewTeacherTaskCalendar
     * Purpose : tenant view task calendar
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function viewTeacherHomework(Request $request)
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
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/homework-created';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

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
                return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);

                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('ta_inbox', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

        return view('tenant.teacher.homework', $data);
    }

    /*
     * Function name : viewStudentHomework
     * Purpose : tenant view home task list student
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function viewStudentHomework(Request $request)
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
        try {

            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/consumer/homework';

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],

            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];

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
                return \Redirect::route('tus_homework', Session()->get('tenant_info')['subdomain'])->with($notification);

                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tus_homework', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

        return view('tenant.student.homework', $data);
    }

    /*
     * Function name : teacherViewTaskStudents
     * Purpose : tenant teacher view task
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function teacherViewTaskStudents($subdomain, $task_id)
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

        $data['shortboards'] = Session()->get('datalist_shortboards');
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/task/get/consumers';
            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "task_id" => $task_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['task_details'] = $response['result']['details'];
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
                return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_calendar', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }
        return view('tenant.teacher.view-task-students', $data);
    }

    /*
     * Function name : aiHelp
     * Purpose : tenant taiHelp
     * Author  : SM
     * Created Date : 02-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function aiHelp()
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
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');

        // $data['ai_url']="http://www.google.com?token=".$publicKey.'&type='.$userInfo['user_type'];
        $queryStr = "?token=" . $publicKey . '&type=' . $userInfo['user_type'] . '&subdomain=' . Session()->get('tenant_info')['subdomain'];
        $data['ai_url'] = config('app.aihelp_base_url') . $queryStr;

        return view('tenant.aihelp', $data);
    }

    /*
     * Function name : studentLeaderboard
     * Purpose : tenant (student) Student Leaderboard
     * Author  :
     * Created Date : 7-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentLeaderboard(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-leaderboard'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_leaderboard');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_leaderboard')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentAchievement
     * Purpose : tenant (student) Student Achievement
     * Author  :
     * Created Date : 7-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentAchievement(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-achievement'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_achievement');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_achievement')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentReward
     * Purpose : tenant (student) Student Reward
     * Author  :
     * Created Date : 7-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReward(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-reward'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_reward');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_reward')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentTarget
     * Purpose : tenant (student) Student Target
     * Author  :
     * Created Date : 7-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentTarget(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-target'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_target');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_target')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentBehavioral
     * Purpose : tenant (student) Student Behavioral
     * Author  :
     * Created Date : 8-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentBehavioral(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-behavioral'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_behavioral');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_behavioral')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : deliveryRating
     * Purpose : tenant teacher delivery rating
     * Created Date : 08-08-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function deliveryRating($subdomain, $lesson_id)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/rating/consumer-by-lesson-id';

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "lesson_id" => $lesson_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['no_image'] = GlobalVars::API_NO_IMAGE;
            return view('tenant.teacher.delivery-rating', $data);

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
                return \Redirect::route('tut_deliveryrating', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_deliveryrating', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : studentReportcardDetail
     * Purpose : tenant (student) Student ReportcardDetail
     * Author  :
     * Created Date : 9-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReportcardDetail(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-reportcarddetail'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_reportcarddetail');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_reportcarddetail')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : contentRating
     * Purpose : tenant teacher content rating
     * Created Date : 08-08-2024
     * Modified date :
     * Params : Request
     * Return : void
     */
    public function contentRating($subdomain, $lesson_id)
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

        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/rating/consumer-by-lesson-id';

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                'form_params' => [
                    "lesson_id" => $lesson_id,
                ],
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);
            $data['listing'] = $response['result']['listing'];
            $data['no_image'] = GlobalVars::API_NO_IMAGE;
            return view('tenant.teacher.content-rating', $data);

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
                return \Redirect::route('tut_deliveryrating', Session()->get('tenant_info')['subdomain'])->with($notification);
                //}
            }
            // You can check for whatever error status code you need

        } catch (Exception $e) {
            $notification = array(
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            );
            return \Redirect::route('tut_deliveryrating', Session()->get('tenant_info')['subdomain'])->with($notification);
            // throw ($e);
            // throw new \App\Exceptions\AdminException($e->getMessage());
        }

    }

    /*
     * Function name : studentReportcardType
     * Purpose : tenant (student) Student ReportcardType
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReportcardType(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-reportcardtype'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_reportcardtype');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_reportcardtype')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : studentReportcardYear
     * Purpose : tenant (student) Student ReportcardYear
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function studentReportcardYear(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.student-reportcardyear'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_reportcardyear');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_reportcardyear')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getLunchMenu
     * Purpose : tenant (student) Student LunchMenu
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getLunchMenu(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.lunch-menu'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_lunchmenu');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_lunchmenu')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getLunchMealActivity
     * Purpose : tenant (student) Student LunchMealActivity
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getLunchMealActivity(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.lunch-meal-activity'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_lunchmealactivity');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_lunchmealactivity')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : tuspastoralcare
     * Purpose : tenant (student) Student Pastoral Care
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tuspastoralcare(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.pastoral-care'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_pastoralcare');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_pastoralcare')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : tuseshop
     * Purpose : tenant (student) Student EShop
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tuseshop(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.eshop'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_eshop');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_eshop')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : tuseshopdesc
     * Purpose : tenant (student) Student EShop Description
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tuseshopdesc(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.eshop-description'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_eshopdesc');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_eshopdesc')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : tuseshopsuccess
     * Purpose : tenant (student) Student EShop Success
     * Author  :
     * Created Date : 12-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function tuseshopsuccess(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.eshop-success'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_eshopsuccess');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_eshopsuccess')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getOrderHistory
     * Purpose : tenant (student) Student Eshop Order History
     * Author  :
     * Created Date : 13-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getOrderHistory(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.order-history'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_orderhistory');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_orderhistory')->withErrors($e->getMessage());
        }
    }
    /*
     * Function name : getOrderHistoryMore
     * Purpose : tenant (student) Student Eshop Order History View More
     * Author  :
     * Created Date : 13-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getOrderHistoryMore(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.order-history-more'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_orderhistorymore');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_orderhistorymore')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getCart
     * Purpose : tenant (student) Student Cart
     * Author  :
     * Created Date : 13-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getCart(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.cart'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_cart');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_cart')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getCartAddress
     * Purpose : tenant (student) Student Cart Address
     * Author  :
     * Created Date : 13-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getCartAddress(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.cart-address'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_cartaddress');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_cartaddress')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : getCartAddressFill
     * Purpose : tenant (student) Student Cart Address
     * Author  :
     * Created Date : 13-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function getCartAddressFill(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.student.cart-address-fill'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_cartaddressfill');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_cartaddressfill')->withErrors($e->getMessage());
        }
    }

    /*
     * Function name : teacherKPI
     * Purpose : tenant (teacher) kpi
     * Author  :
     * Created Date : 5-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function teacherKPI(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.teacher.analytic'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tut_kpicharts');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tut_kpicharts')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : adminKPI
     * Purpose : tenant (admin) kpi
     * Author  :
     * Created Date : 5-08-2024
     * Modified date :
     * Params : void
     * Return : void
     */
    public function adminKPI(Request $request)
    {

        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }

        try {
            return view(
                'tenant.analytic'
            );

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '401') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('ta_analyticstudio');
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('ta_analyticstudio')->withErrors($e->getMessage());
        }

    }

    /*
     * Function name : studentTestGenerator
     * Purpose : tenant view lesson list student
     * Author  : SM
     * Created Date : 22-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function studentTestGenerator(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['listing'] = array();
        if ($request->isMethod('post')) {
            $data['year_group_id'] = $year_group_id = $request->year_group_id ?? '';
            $data['subject_id'] = $subject_id = $request->subject_id ?? '';

            if ($subject_id != '') {
                try {
                    $client = new Client();
                    $apiEndpoint = config('app.api_base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/get-subjectid-lessons-with-quescount';

                    $form_params = [
                        "subject_id" => $subject_id,
                    ];

                    // dd(json_encode($form_params));
                    // dd($apiEndpoint);

                    $call = $client->post($apiEndpoint, [
                        'headers' => ['Authorization' => 'Bearer ' . $publicKey],
                        'form_params' => $form_params,
                    ]);
                    $response = json_decode($call->getBody()->getContents(), true);
                    // dd($response);

                    $data['listing'] = $response['result']['listing'];

                    $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

                } catch (RequestException $e) {
                    // throw ($e);
                    // Catch all 4XX errors
                    // To catch exactly error 400 use
                    if ($e->hasResponse()) {
                        $response = json_decode($e->getResponse()->getBody()->getContents());
                        if ($e->getResponse()->getStatusCode() == '400') {
                            // echo "Got response 401";
                            $this->refreshLoginData();
                            return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain']);
                        }
                        return \Redirect::route('front_flush')->withErrors($response->error->message);
                    }
                    // You can check for whatever error status code you need

                } catch (\Exception $e) {
                    //buy a beer
                    // throw ($e);
                    return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
                }
            }
        }

        return view('tenant.student.test_generator', $data);
    }

    /*
     * Function name : studentTestGeneratorProceed
     * Purpose : tenant student test generator proceed
     * Author  : SM
     * Created Date : 22-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function studentTestGeneratorProceed(Request $request)
    {
        // dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['year_group_id'] = $year_group_id = $request->hd_year_group_id ?? '';
        $data['subject_id'] = $subject_id = $request->hd_subject_id ?? '';
        $lesson_ids = $request->lesson_id ?? array();
        $data['lesson_ids'] = implode(',',$lesson_ids);
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url')  . '/dropdown/portal-question-levels';

            // $form_params = [
            //     "subject_id" => $subject_id,
            // ];

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $publicKey,
                    'X_NEON'=> config('app.api_key'),
                ],
                // 'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['question_levels'] = $response['result']['question_levels'];

            // $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
       

        return view('tenant.student.proceed_test_generator', $data);
    }

    /*
     * Function name : studentTestGeneratorSave
     * Purpose : tenant student test generator save
     * Author  : SM
     * Created Date : 22-08-2024
     * Modified date :
     * Params :
     * Return : void
     */
    public function studentTestGeneratorSave(Request $request)
    {
        dd($request->all());
        $checkToken = $this->checkToken();
        $publicKey = Session()->get('usertoken');
        if ($publicKey == '' || $checkToken == false) {
            $notification = array(
                'message' => 'Session expired. Please login again..',
                'alert-type' => 'error',
            );
            return \Redirect::route('front_flush')->with($notification);
        }
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $data['year_group_id'] = $year_group_id = $request->hd_year_group_id ?? '';
        $data['subject_id'] = $subject_id = $request->hd_subject_id ?? '';
        $lesson_ids = $request->lesson_id ?? array();
        $data['lesson_ids'] = implode(',',$lesson_ids);
        try {
            $client = new Client();
            $apiEndpoint = config('app.api_base_url')  . '/dropdown/portal-question-levels';

            // $form_params = [
            //     "subject_id" => $subject_id,
            // ];

            // dd(json_encode($form_params));
            // dd($apiEndpoint);

            $call = $client->post($apiEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $publicKey,
                    'X_NEON'=> config('app.api_key'),
                ],
                // 'form_params' => $form_params,
            ]);
            $response = json_decode($call->getBody()->getContents(), true);
            // dd($response);

            $data['question_levels'] = $response['result']['question_levels'];

            // $data['form_params'] = CommonHelper::encryptId(json_encode($form_params));

        } catch (RequestException $e) {
            // throw ($e);
            // Catch all 4XX errors
            // To catch exactly error 400 use
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                if ($e->getResponse()->getStatusCode() == '400') {
                    // echo "Got response 401";
                    $this->refreshLoginData();
                    return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain']);
                }
                return \Redirect::route('front_flush')->withErrors($response->error->message);
            }
            // You can check for whatever error status code you need

        } catch (\Exception $e) {
            //buy a beer
            // throw ($e);
            return \Redirect::route('tus_testgen', Session()->get('tenant_info')['subdomain'])->withErrors($e->getMessage());
        }
       

        return view('tenant.student.proceed_test_generator', $data);
    }
}
