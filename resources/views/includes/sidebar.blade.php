<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{asset('uploads/adminuser_logos')}}/{{ Auth::guard('admin')->user()->user_logo }}"
                    class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::guard('admin')->user()->first_name }} {{ Auth::guard('admin')->user()->last_name }}</p>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <!-- <li class="header">MAIN NAVIGATION</li> -->
            <li class="@if(in_array(Route::current()->getName(), array('dashboard'))) active menu-open @endif">
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <!-- <li class="treeview @if(in_array(Route::current()->getName(), array('users.list','users.add','users.edit'))) menu-open @endif">
				<a href="#">
					<i class="fa fa-user"></i> <span>Manage Users</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu" @if(in_array(Route::current()->getName(), array('users.list','users.add','users.edit'))) style="display: block;" @endif>
					<li @if(in_array(Route::current()->getName(), array('users.list','users.edit'))) class="active" @endif><a href="{{ route('users.list') }}"><i class="fa fa-bars"></i>List</a></li>
					<li @if(in_array(Route::current()->getName(), array('users.add'))) class="active" @endif><a href="{{ route('users.add') }}"><i class="fa fa-plus"></i>Add</a></li>

				</ul>
			</li> -->

            <li
                class="@if(in_array(Route::current()->getName(), array('banners.list','banners.edit','banners.delete'))) active menu-open @endif">
                <a href="{{ route('banners.list') }}">
                    <i class="fa fa-tasks"></i> <span>Home Page Banner</span>
                </a>
            </li>

            <li
                class="@if(in_array(Route::current()->getName(), array('cms.list','cms.edit','cmscontents.list','cmscontents.edit'))) active menu-open @endif">
                <a href="{{ route('cms.list') }}">
                    <i class="fa fa-tasks"></i> <span>CMS</span>
                </a>
            </li>


            <li
                class="@if(in_array(Route::current()->getName(), array('inquerytype.list','inquerytype.edit'))) active menu-open @endif">
                <a href="{{ route('inquerytype.list') }}">
                    <i class="fa fa-tasks"></i> <span>Inquery Type </span>
                </a>
            </li>
            <li class="@if(in_array(Route::current()->getName(), array('contacts.list'))) active menu-open @endif">
                <a href="{{ route('contacts.list') }}">
                    <i class="fa fa-envelope-open-o"></i> <span>Contact List</span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('faqcategory.list','faqcategory.edit'))) active menu-open @endif">
                <a href="{{ route('faqcategory.list') }}">
                    <i class="fa fa-tasks"></i> <span>FAQ Category</span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('faq.list','faq.edit'))) active menu-open @endif">
                <a href="{{ route('faq.list') }}">
                    <i class="fa fa-tasks"></i> <span>FAQ </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('review.list','review.edit'))) active menu-open @endif">
                <a href="{{ route('review.list') }}">
                    <i class="fa fa-tasks"></i> <span>Reviews </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('testimonial.list','testimonial.edit'))) active menu-open @endif">
                <a href="{{ route('testimonial.list') }}">
                    <i class="fa fa-tasks"></i> <span>Testimonial </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('clients.list','clients.edit'))) active menu-open @endif">
                <a href="{{ route('clients.list') }}">
                    <i class="fa fa-tasks"></i> <span>Clientele </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('teams.list','teams.edit'))) active menu-open @endif">
                <a href="{{ route('teams.list') }}">
                    <i class="fa fa-tasks"></i> <span>Team </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('galleries.list','galleries.edit'))) active menu-open @endif">
                <a href="{{ route('galleries.list') }}">
                    <i class="fa fa-tasks"></i> <span>Gallery </span>
                </a>
            </li>
            <li class="@if(in_array(Route::current()->getName(), array('newsletter.list'))) active menu-open @endif">
                <a href="{{ route('newsletter.list') }}">
                    <i class="fa fa-tasks"></i> <span>Newsletter List</span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('blogcategory.list','blogcategory.edit'))) active menu-open @endif">
                <a href="{{ route('blogcategory.list') }}">
                    <i class="fa fa-tasks"></i> <span>Blog Category</span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('blog.list','blog.edit'))) active menu-open @endif">
                <a href="{{ route('blog.list') }}">
                    <i class="fa fa-tasks"></i> <span>Blog </span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('pricecategory.list','pricecategory.edit'))) active menu-open @endif">
                <a href="{{ route('pricecategory.list') }}">
                    <i class="fa fa-tasks"></i> <span>Price Category</span>
                </a>
            </li>
            <li
                class="@if(in_array(Route::current()->getName(), array('price.list','price.edit'))) active menu-open @endif">
                <a href="{{ route('price.list') }}">
                    <i class="fa fa-tasks"></i> <span>Price</span>
                </a>
            </li>
            <li class="@if(in_array(Route::current()->getName(), array('settings.edit'))) active menu-open @endif">
                <a href="{{ route('settings.edit') }}">
                    <i class="fa fa-cog"></i> <span>Settings</span>
                </a>
            </li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>