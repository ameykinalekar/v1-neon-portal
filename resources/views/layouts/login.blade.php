@php
$userInfo=Session::get('user');
$profileInfo=Session::get('profile_info');
$tenantInfo=Session::get('tenant_info');
$settingInfo=Session::get('setting_info');
$tenantShortName=Session::get('tenant_short_name');
//print_r($tenantShortName);
//print_r($settingInfo);
$bg=asset('img/system/login_auth.png');
if(isset($tenantInfo) && $tenantInfo!=null && $tenantInfo['background_image']!=''){
    $bg=config('app.api_asset_url').$tenantInfo['background_image'];
}
//echo $bg;
@endphp
<!DOCTYPE html>
<html>
	@include('includes.head-login')

	<body class="auth-fluid-pages pb-0" style="background: url('{{$bg}}') no-repeat center center fixed;background-size:cover" onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">
        @yield('content')
        @include('includes.footer-script')
        @yield('pagescript')
    </body>
</html>
