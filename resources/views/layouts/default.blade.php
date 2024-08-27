@php
$userInfo=Session::get('user');
$profileInfo=Session::get('profile_info');
$tenantInfo=Session::get('tenant_info');
$settingInfo=Session::get('setting_info');
$tenantShortName=Session::get('tenant_short_name');
//print_r($tenantInfo);
//print_r($settingInfo);
@endphp
<!DOCTYPE html>
<html>
@include('includes.head')

<body class="loading1" data-layout="detached" onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">
    <!-- HEADER -->

    @switch($userInfo['user_type'])
    @case('TA')
    @case('TU')
    @case('P')
    @include('includes.ta-header')
    @break
    @case('T')
    @include('includes.t-header')
    @break
    @default
    @include('includes.header')
    @break
    @endswitch
    <div class="container-fluid">
        <div class="wrapper">
            <!-- BEGIN CONTENT -->
            <!-- SIDEBAR -->
            <div class="leftside-menu leftside-menu-detached">
                <div class="leftbar-user">

                </div>
                @switch($userInfo['user_type'])
                @case('TA')
                @case('TU')
                @case('P')
                @include('includes.ta-navigation')
                @break
                @case('T')
                @include('includes.t-navigation')
                @break
                @default
                @include('includes.navigation')
                @break
                @endswitch
                <div class="clearfix"></div>
                <!-- Sidebar -left -->

            </div>
            <!-- PAGE CONTAINER-->
            <div class="content-page" style="background:#EBEBEB">
                <div class="content" style="margin-top:10px;">
                    <div class="loadings hidden"></div>
                    <!-- BEGIN PlACE PAGE CONTENT HERE -->
                    @yield('content')

                    <!-- END PLACE PAGE CONTENT HERE -->
                </div>
                <!-- Footer -->

            </div>
            <!-- END CONTENT -->
        </div>
    </div>
    <div class="clearfix"></div>
    @include('includes.footer')
    <!-- all the js files -->
    @include('includes.script')
    @yield('pagescript')
    @include('includes.modals')
    <script type="text/javascript">
        //initDataTable('basic-datatable');
    </script>
</body>

</html>
