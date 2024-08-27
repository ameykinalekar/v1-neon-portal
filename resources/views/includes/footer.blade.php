<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                @if($settingInfo['footer_link']??''=='')
                © <a href="#">{{ $settingInfo['footer_text']??'Neon AI' }}</a>
                @else
                © <a href="{{ $settingInfo['footer_link']??'#' }}" target = "_blank">{{ $settingInfo['footer_text']??'Neon AI' }}</a>
                @endif
            </div>
        </div>
    </div>
</footer>
