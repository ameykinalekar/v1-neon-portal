<head>
    <meta charset="utf-8" />
    <title>{{$settingInfo['system_title']??'Neon Edu'}} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="neon-edu" name="author" />
    <!-- App favicon -->
    <!-- <link rel="shortcut icon" href="uploads/school_fevicon/298.jpg" type="image/gif" sizes="16x16"> -->

    <meta name="_token" content="{!! csrf_token() !!}" />
    <!-- App favicon -->
    @if(isset($settingInfo) && $settingInfo!=null && $settingInfo['favicon']!='')
    <link rel="shortcut icon" href="{{config('app.api_asset_url').$settingInfo['favicon']}}">
    @else
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}">
    @endif
    <!-- all the css files -->
    <!-- App css -->
    <link href="{{asset('css/init/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/init/app-modern.min.css')}}" rel="stylesheet" type="text/css" id="light-style" />
    <!-- <link href="{{asset('css/init/app-modern-dark.min.css')}}" rel="stylesheet"
        type="text/css" id="dark-style" /> -->

    <!-- App css End-->
    <link href="{{asset('css/init/vendor/dataTables.bootstrap5.css')}}" rel="stylesheet"
        type="text/css" />
    <!-- <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script> -->

    <!-- third party css -->
    <!--<link href="{{asset('css/init/vendor/fullcalendar.min.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/vendor/dataTables.bootstrap5.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/vendor/responsive.bootstrap5.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/vendor/buttons.bootstrap5.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/vendor/select.bootstrap5.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/vendor/summernote-bs4.css')}}" rel="stylesheet"
        type="text/css" />-->
    <!-- third party css end -->

    <!--Font Awesome 6-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> -->


    <!-- <link href="{{asset('css/init/powerful_calendar_style.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('css/init/powerful_calendar_theme.css')}}" rel="stylesheet"
        type="text/css" /> -->


    <link href="{{asset('css/init/custom.css')}}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{asset('css/init/content-placeholder.css')}}" rel="stylesheet"
        type="text/css" />

    <link href="{{asset('css/init/donut_style.css')}}" rel="stylesheet"
        type="text/css" /> -->

    <!--Notify for ajax-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


    <!--Font Awesome 6-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
    body {
        background-color: #EBEBEB !important;
    }

    .card-header,
    .card-title {
        text-align: right;
    }

    .side-nav .side-nav-link {
        font-weight: 600;
    }

    .side-nav-second-level li a,
    .side-nav-third-level li a {
        transition: all .4s;
        font-size: .90rem;
        /* font-weight: 500; */
    }

    table.dataTable tbody td.focus,
    table.dataTable tbody th.focus {
        outline: 0px solid #536de6 !important;
        outline-offset: -1px;
        background-color: rgb(83 109 230 / 0%);
    }
    body[data-layout=detached] .leftbar-user {
    padding: 23px 20px;
}
    </style>
    <style>
    .hide {
        display: none;
    }

    .leftside-menushow {
        background: #fff !important;
    }
    .footer{
        position: relative;
    }
    </style>
    @yield('pagecss')
    <style>
        @if(isset($tenantInfo) && $tenantInfo!=null && $tenantInfo['theme_color']!='')
    .topnav-navbar-dark {
        background-color: {{$tenantInfo['theme_color']}} !important;
    }
    @endif
    </style>

</head>
