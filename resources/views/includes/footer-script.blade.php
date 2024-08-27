<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script type="text/javascript">
@include('includes.toast-notification')
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<script type="text/javascript">
	$("img").lazyload({
	    effect : "fadeIn"
	});
</script>
<script src="{{asset('js/custom.js')}}"></script>
<script type="text/javascript">
	 window.history.forward();
    function noBack()
    {
        window.history.forward();
    }
</script>
