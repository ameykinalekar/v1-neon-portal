<div class="navbar-custom topnav-navbar topnav-navbar-dark">
        <div class="container-fluid">

            <a href="{{route('tenant_dashboard', Session()->get('tenant_info')['subdomain'])}}" class="topnav-logo" style="min-width: unset;">
                <span class="topnav-logo-lg">

                    @if(isset($tenantInfo) && $tenantInfo!=null && $tenantInfo['logo']!='')
                    <img src="{{config('app.api_asset_url').$tenantInfo['logo']}}" alt="logo" height="62" style="border-radius: 50%">
                    @else
                    <img src="{{config('app.api_asset_url').'/img/system/logo/logo-dark.png'}}" alt="logo" height="62" style="border-radius: 50%">
                    @endif

                </span>
                <span class="topnav-logo-sm">

                    @if(isset($tenantInfo) && $tenantInfo!=null && $tenantInfo['logo']!='')
                    <img src="{{config('app.api_asset_url').$tenantInfo['logo']}}" alt="logo small" height="40" style="border-radius: 50%">
                    @else
                    <img src="{{config('app.api_asset_url').'/img/system/logo/logo-dark.png'}}" alt="logo small" height="40" style="border-radius: 50%">
                    @endif
                </span>
            </a>

            <ul class="list-unstyled topbar-menu float-end mb-0">
            <li><img src="{{ asset('img/system/icons/reminder.png') }}" alt="reminder"  style="height:23px;margin:20px;" class="hover-image" >
                <img src="{{ asset('img/system/icons/cart.png') }}" alt="cart" style="height:23px;margin:2px;" class="hover-image" >
                @if($userInfo['role']=='S' && $userInfo['user_type']=='TU')
                <a href="{{route('tus_inbox',Session()->get('tenant_info')['subdomain'])}}" title="In Box"><img src="{{ asset('img/system/icons/notification.png') }}" alt="notification" style="height:20px;margin:15px;" class="hover-image" ></a>
                @elseif($userInfo['role']=='T' && $userInfo['user_type']=='TU')
                <a href="{{route('tut_inbox',Session()->get('tenant_info')['subdomain'])}}" title="In Box"><img src="{{ asset('img/system/icons/notification.png') }}" alt="notification" style="height:20px;margin:15px;" class="hover-image" ></a>
                @elseif($userInfo['user_type']=='TA' && $userInfo['role']=='A')
                <a href="{{route('ta_inbox',Session()->get('tenant_info')['subdomain'])}}" title="In Box"><img src="{{ asset('img/system/icons/notification.png') }}" alt="notification" style="height:20px;margin:15px;" class="hover-image" ></a>
                @else
                <img src="{{ asset('img/system/icons/notification.png') }}" alt="notification" style="height:20px;margin:15px;" class="hover-image" >
                @endif
            </li>
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false"
                        style="background: transparent; border:none">
                        <span class="account-user-avatar">
                            @if($userInfo['user_logo']=='')
                            <img src="{{config('app.api_asset_url').'/img/system/auth-placeholder.jpg'}}" alt="user-image" class="rounded-circle">
                            @else
                            <img src="{{config('app.api_asset_url').$userInfo['user_logo']}}" alt="user-image" class="rounded-circle">
                            @endif
                        </span>
                        <span>
                            <span class="account-user-name">{{ $profileInfo['first_name']??'' }} {{ $profileInfo['middle_name']??'' }} {{ $profileInfo['last_name']??'' }}</span>
                            @if($userInfo['user_type']=='TU')
                            <span class="account-position">{{GlobalVars::TENANT_ROLES[$userInfo['role']]}}</span>
                            @else
                            <span class="account-position">{{GlobalVars::USER_TYPES[$userInfo['user_type']]}}</span>
                            @endif

                        </span>
                    </a>

                    <div
                        class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                        <!-- item-->
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        <!-- item-->
                        <a href="{{route('myaccount', Session()->get('tenant_info')['subdomain'])}}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle me-1"></i>
                            <span>My Account</span>
                        </a>
                        @if($userInfo['user_type']=='TA')
                        <!-- item-->
                        <a href="{{route('settings', Session()->get('tenant_info')['subdomain'])}}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-cog me-1"></i>
                            <span>System Settings</span>
                        </a>
                        @endif
                        <!-- item-->
                        <a href="{{route('front_logout')}}" class="dropdown-item notify-item">
                            <i class="mdi mdi-logout me-1"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </li>

            </ul>
            <div class="app-search dropdown pt-1 mt-2">
            </div>
            <a class="button-menu-mobile disable-btn">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </a>
        </div>
    </div>

    <script type="text/javascript">
    function getLanguageList() {
        $.ajax({
            url: "",
            success: function(response) {
                $('#language-list').html(response);
            }
        });
    }
    </script>
