<?php

namespace App\Traits;

use App\Helpers\CommonHelper;
use App\Models\Tenant;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Session;

trait GeneralMethods
{

    public function assignBreadcrumb()
    {
        $this->breadcrumb = $breadcrumb = [
            'LISTPAGE' =>
            [
                ['label' => $this->management . ' List', 'url' => 'THIS'],
            ],
            'CREATEPAGE' =>
            [
                ['label' => $this->management . ' List', 'url' => \URL::route($this->listUrl)],
                ['label' => 'Add', 'url' => 'THIS'],
            ],
            'EDITPAGE' =>
            [
                ['label' => $this->management . ' List', 'url' => \URL::route($this->listUrl)],
                ['label' => 'Edit', 'url' => 'THIS'],
            ],
            'VIEWPAGE' =>
            [
                ['label' => $this->management . ' List', 'url' => \URL::route($this->listUrl)],
                ['label' => $this->management . ' View', 'url' => 'THIS'],
            ],
            'CHANGEPASSWORDPAGE' =>
            [
                ['label' => $this->management . ' List', 'url' => \URL::route($this->listUrl)],
                ['label' => $this->management . ' Change Password', 'url' => 'THIS'],
            ],
        ];
    }

    public function assignShareVariables()
    {
        \View::share([
            'management' => $this->management,
            'modelName' => $this->modelName,
            'breadcrumb' => $this->breadcrumb,
            'routePrefix' => $this->routePrefix,
            'urlPrefix' => isset($this->urlPrefix) ? $this->urlPrefix : '',
            'controllerName' => $this->controllerName,
        ]);
        // Declare variables as per current method
        if (\Route::current()->getActionMethod() == 'index') {
            \View::share(['pageType' => 'List']);
        } elseif (\Route::current()->getActionMethod() == 'add') {
            \View::share(['pageType' => 'Add']);
        } elseif (\Route::current()->getActionMethod() == 'edit') {
            \View::share(['pageType' => 'Edit']);
        } elseif (\Route::current()->getActionMethod() == 'import' || \Route::current()->getActionMethod() == 'storecsv') {
            \View::share(['pageType' => 'List']);
        } elseif (\Route::current()->getActionMethod() == 'view') {
            \View::share(['pageType' => 'View']);
        } elseif (\Route::current()->getActionMethod() == 'changePassword') {
            \View::share(['pageType' => 'Change Password']);
        }
    }

    public function getTenantId()
    {
        $current_uri = request()->segments();
        // dd($current_uri[1]);
        if (count($current_uri) > 1 && $current_uri[0] == 'api') {
            $subdomain = $current_uri[1];
        } else {
            $subdomain = $current_uri[0] ?? '';
        }
        // $subdomain = explode('.', request()->getHost())[0];
        $tenantId = "";
        $tenant = Tenant::where('subdomain', '=', $subdomain)->select('tenant_id')->first();
        if (!empty($tenant)) {
            $tenantId = $tenant->tenant_id;
        }

        return $tenantId;
    }

    public function refreshLoginData()
    {
        $userl = Session()->get('userl');
        $userInfo = Session::get('user');
        $profileInfo = Session::get('profile_info');
        $tenantInfo = Session::get('tenant_info');
        $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
        // dd($tenantInfo);
        $credentialsDeCoded = CommonHelper::decryptId($userl);
        $credentials = json_decode($credentialsDeCoded);
        $client = new Client();
        // $apiEndpoint = config('app.api_base_url') . '/auth';
        if ($userInfo['user_type'] == 'TU' || $userInfo['user_type'] == 'P') {
            $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/auth';
        } else {
            $apiEndpoint = config('app.api_base_url') . '/auth';
        }
        $call = $client->post($apiEndpoint, [
            'form_params' => [
                'email' => $credentials->email,
                'password' => $credentials->password,
            ],
        ]);

        // dd($call->getBody()->getContents());
        $response = json_decode($call->getBody()->getContents(), true);
        Session()->put('usertoken', $response['result']['token']);
        Session()->put('userl', $response['result']['credentials']);
        // Auth::login($user, true);
        // dd(Session()->all());

        $publicKey = Session()->get('usertoken');

        // $apiEndpoint = config('app.api_base_url') . '/auth/validate';
        if ($userInfo['user_type'] == 'TU' || $userInfo['user_type'] == 'P') {
            $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/auth/validate';
        } else {
            $apiEndpoint = config('app.api_base_url') . '/auth/validate';
        }
        $call = $client->post($apiEndpoint, [
            'form_params' => [
                'token' => $publicKey,
            ],
        ]);
        $response = json_decode($call->getBody()->getContents(), true);
        // dd($response);
        Session()->put('user', $response['result']['user']);
        Session()->put('profile_info', $response['result']['profile_info']);
        Session()->put('tenant_info', $response['result']['tenant_info']);
        Session()->put('setting_info', $response['result']['setting_info']);
        Session()->put('total_active_users_count', $response['result']['total_active_users_count']);
    }

    public function checkAccess($module)
    {
        $response = false;
        try {
            $publicKey = Session()->get('usertoken');
            $userInfo = Session()->get('user');
            $profileInfo = Session()->get('profile_info');
            $tenantInfo = Session()->get('tenant_info');
            $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
            // dd($publicKey);
            if ($publicKey == '') {
                $response = false;
            } else {
                if ($tenantInfo['features_available'] != '') {
                    $features_available = json_decode($tenantInfo['features_available']);
                    // dd($features_available->$module);
                    if ($features_available->$module) {
                        $response = true;
                    } else {
                        $response = true;
                        // $response = false;
                    }
                } else {
                    $response = true;
                    // $response = false;
                }
                return $response;

            }
        } catch (\Exception $e) {
            // throw ($e);
            return $response;
        }
    }

    public function checkAllowedUsers()
    {
        $response = false;
        try {
            $publicKey = Session()->get('usertoken');
            $userInfo = Session()->get('user');
            $profileInfo = Session()->get('profile_info');
            $tenantInfo = Session()->get('tenant_info');
            $total_active_users_count = Session()->get('total_active_users_count');
            $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
            $users_allowed = Session()->get('tenant_info')['users_allowed'] ?? 0;
            // dd($publicKey);
            if ($publicKey == '') {
                $response = false;
            } else {
                if ($tenantInfo['users_allowed'] > $total_active_users_count) {
                    $response = true;
                } else {
                    $response = false;
                }
                return $response;

            }
        } catch (\Exception $e) {
            // throw ($e);
            return $response;
        }
    }

    public function checkToken()
    {
        $response = false;
        try {
            $publicKey = Session()->get('usertoken');
            $userInfo = Session()->get('user');
            $profileInfo = Session()->get('profile_info');
            $tenantInfo = Session()->get('tenant_info');
            $subdomain = Session()->get('tenant_info')['subdomain'] ?? '';
            // dd($publicKey);
            if ($publicKey == '') {
                $response = false;
            } else {

                $client = new Client();
                if ($userInfo['user_type'] == 'TU' || $userInfo['user_type'] == 'P') {
                    $apiEndpoint = config('app.api_base_url') . '/' . $subdomain . '/auth/validate';
                } else {
                    $apiEndpoint = config('app.api_base_url') . '/auth/validate';
                }
                $call = $client->post($apiEndpoint, [
                    'form_params' => [
                        'token' => $publicKey,
                    ],
                ]);
                // $response = json_decode($call->getBody()->getContents(), true);
                // dd($response);
                $response = true;
                return $response;

            }
        } catch (\Exception $e) {
            // throw ($e);
            return $response;
        }
    }
}
