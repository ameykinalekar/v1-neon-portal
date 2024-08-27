<head>
    <meta charset="utf-8" />
    <title>{{$tenantShortName??'Neon Edu'}} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <!-- App favicon -->
    @if(isset($settingInfo['favicon']) && $settingInfo['favicon'] != null)
    <link rel="shortcut icon" href="{{config('app.api_asset_url').$settingInfo['favicon']}}">
    @else
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}">
    @endif

    <!-- App css -->
    <link href="{{asset('css/init/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/init/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!--Notify for ajax-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @yield('pagecss')
  </head>
