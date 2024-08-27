@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    @foreach ($errors->all() as $error)
    <span>{{ $error }}</span><br />
    @endforeach

</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable __web-inspector-hide-shortcut__">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('success') }}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('error') }}
</div>
@endif

@if(Request::get('msg') == 'sync')
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    City data sync successfully.
</div>
@endif
