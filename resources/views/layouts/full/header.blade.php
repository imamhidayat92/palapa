@include('layouts.elements.userbar')

<nav class="navbar navbar-header" role="navigation">
    <div class="container-fluid">
        <div class="col-lg-6 col-md-10 col-sm-10 col-xs-10 trails">
            <a href="{{ route('home') }}" class="home">
                <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa ion-monitor fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <span class="trail"><i class="fa fa-angle-right"></i></span>
            @yield('breadcrumb')
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-6 text-right">
            <img class="logo" src="{{ asset('images/logo/logo-kejari.jpg') }}" alt=""/>
            <h1 class="hidden-xs hidden-sm hidden-md">PROFIL KEJAKSAAN NEGERI JEMBER</h1>
        </div>
    </div>
</nav>

<div class="clearfix"></div>
