<div class="navbar-custom topnav-navbar topnav-navbar-dark">
        <div class="container-fluid">
<?php //print_r($settingInfo);?>
            <a href="{{route('t_dashboard')}}" class="topnav-logo" style="min-width: unset;">
                <span class="topnav-logo-lg">
                @if(isset($settingInfo) && $settingInfo!=null && $settingInfo['main_logo']!='' &&  $settingInfo['tenant_id']==0)
                    <img src="{{config('app.api_asset_url').$settingInfo['main_logo']}}" alt="logo" height="62" style="border-radius:50%">

                @else

                    <img src="{{config('app.api_asset_url').'/img/system/logo/logo-dark.png'}}" alt="logo" height="62" style="border-radius:50%">

                @endif
                </span>
                <span class="topnav-logo-sm">
                    <img src="{{config('app.api_asset_url').'/img/system/logo/logo-dark.png'}}" alt="logo small" height="40" style="border-radius:50%">
                </span>
            </a>

            <ul class="list-unstyled topbar-menu float-end mb-0">

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
                            <span class="account-position">{{GlobalVars::USER_TYPES[$userInfo['user_type']]}}</span>

                        </span>
                    </a>

                    <div
                        class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                        <!-- item-->
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        <!-- item-->
                        <a href="{{route('t_myaccount')}}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle me-1"></i>
                            <span>My Account</span>
                        </a>

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
