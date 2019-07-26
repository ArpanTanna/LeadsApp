<header>
    <nav class="navbar navbar-expand-md fixed-top shadow" id="lbh">
        <a class="navbar-brand" href="#">LeadsApp</a>
        <button class="navbar-toggler mr-2" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <!--<li class="nav-item {{ (request()->is('client/dashboard')) ? 'active' : '' }}">
                    <a class="nav-link" href="{{url("/client/dashboard/")}}">Dashboard <span class="sr-only">(current)</span></a>
                </li>-->
                <li class="nav-item {{ (request()->is('client/lead')) ? 'active' : '' }}">
                    <a class="nav-link" href="{{url("/client/lead/")}}">Leads</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('logout')}}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</header>
