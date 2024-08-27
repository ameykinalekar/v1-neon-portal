<!--- Sidemenu -->
<ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item py-2">Navigation</li> -->
    <li class="side-nav-item">
        <a href="{{route('pa_dashboard')}}" class="side-nav-link py-2">
            <!--<i class="dripicons-meter"></i>-->
            <img width="22" height="22" src="{{asset('img/system/icons/home.png')}}">
            <span > Home </span>
        </a>
    </li>



    <li class="side-nav-item  "> <a data-bs-toggle="collapse" href="#users"   @if(in_array(Route::current()->getName(),
            array('pa_trusteelist','pa_schoollist','pa_addschool','pa_editschool','pa_schoolsubscriptions','pa_schoolsubscribeplan'))) aria-expanded=true @else
            aria-expanded=false @endif aria-controls="users"
            class="side-nav-link py-2">
            <!-- <i class="User"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Users</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse @if(in_array(Route::current()->getName(), array('pa_trusteelist','pa_schoollist','pa_addschool','pa_editschool','pa_schoolsubscriptions','pa_schoolsubscribeplan'))) show @endif" id="users">
            <ul class="side-nav-second-level">
                <li @if(in_array(Route::current()->getName(), array('pa_schoollist','pa_addschool','pa_editschool','pa_schoolsubscriptions','pa_schoolsubscribeplan'))) class="active" @endif>
                    <a href="{{route('pa_schoollist')}}">School</a>
                </li>
                <li @if(in_array(Route::current()->getName(), array('pa_trusteelist'))) class="active" @endif>
                    <a href="{{route('pa_trusteelist')}}">Trustee</a>
                </li>

            </ul>
        </div>

    </li>
    <li class="side-nav-item  "> <a href="{{route('pa_boardlist')}}" class="side-nav-link">
            <!-- <i class="Library"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Boards</span>
        </a>
    </li>
    <li class="side-nav-item  "> <a href="{{route('pa_countrylist')}}" class="side-nav-link">
            <!-- <i class="Library"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Countries</span>
        </a>
    </li>
    <li class="side-nav-item  "> <a href="{{route('pa_subscriptionplanlist')}}" class="side-nav-link">
            <!-- <i class="Library"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Subscription Plans</span>
        </a>
    </li>

    <li class="side-nav-item  "> <a href="#" class="side-nav-link">
            <!-- <i class="User"></i> -->
            <img width="22" height="22" src="{{asset('img/system/icons/User.png')}}">
            <span >Financial Report</span>
        </a>
    </li>
</ul>
<!-- End Sidemenu -->
