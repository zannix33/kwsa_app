<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile not-navigation-link">
      <div class="nav-link">
        <div class="user-wrapper">
          <div class="profile-image">
            <img src="{{ url('assets/images/faces/face0.jpg') }}" alt="profile image">
          </div>
          <div class="text-wrapper">
            <p class="profile-name">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
            <div class="dropdown" data-display="static">
              <a href="#" class="nav-link d-flex user-switch-dropdown-toggler" id="UsersettingsDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <small class="designation text-muted">{{ auth()->user()->position }}</small>
                <span class="status-indicator online"></span>
              </a>
              <div class="dropdown-menu" aria-labelledby="UsersettingsDropdown">
                <a class="dropdown-item p-0">
                  <div class="d-flex border-bottom">
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center">
                      <i class="mdi mdi-bookmark-plus-outline mr-0 text-gray"></i>
                    </div>
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center border-left border-right">
                      <i class="mdi mdi-account-outline mr-0 text-gray"></i>
                    </div>
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center">
                      <i class="mdi mdi-alarm-check mr-0 text-gray"></i>
                    </div>
                  </div>
                </a>
                <a class="dropdown-item mt-2"> Manage Accounts </a>
                <a class="dropdown-item"> Change Password </a>
                <a class="dropdown-item"> Check Inbox </a>
                <a class="dropdown-item"> Sign Out </a>
              </div>
            </div>
          </div>
        </div>
        <!--<button class="btn btn-success btn-block">New Project <i class="mdi mdi-plus"></i>
        </button>-->
      </div>
    </li>
    <li class="nav-item {{ active_class(['/']) }}">
      <a class="nav-link" href="{{ url('/') }}">
        <i class="menu-icon mdi mdi-television"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

      <li class="nav-item {{ active_class(['basic-ui/*']) }}">
          <a class="nav-link" data-toggle="collapse" href="#hr" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-human"></i>
              <span class="menu-title">HR</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/*']) }}" id="hr">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ route('hr.employee.index') }}">Employee</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item {{ active_class(['basic-ui/clients']) }}">
          <a class="nav-link" data-toggle="collapse" href="#clients" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-office-building"></i>
              <span class="menu-title">Clients</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/clients']) }}" id="clients">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ route('clients.companies.index') }}">Companies</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ url('/') }}">Areas and Branches</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item {{ active_class(['basic-ui/accounting']) }}">
          <a class="nav-link" data-toggle="collapse" href="#accounting" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-calculator"></i>
              <span class="menu-title">Accounting</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/accounting']) }}" id="accounting">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/payroll-periods') }}">Payroll</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/payrolls') }}">Payroll Views</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ route('dtr.bulk.create') }}">DTR</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ url('/') }}">13th Month Pay</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item {{ active_class(['basic-ui/payroll']) }}">
          <a class="nav-link" data-toggle="collapse" href="#payroll" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-calculator"></i>
              <span class="menu-title">Payroll</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/accounting']) }}" id="payroll">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/payroll-periods') }}">Payroll</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/payrolls') }}">Payroll Views</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item {{ active_class(['basic-ui/accounting']) }}">
          <a class="nav-link" data-toggle="collapse" href="#operations" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-head-cog"></i>
              <span class="menu-title">Operations</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/accounting']) }}" id="operations">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/') }}">Arms Inventory</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ url('/') }}">DDO</a>
                  </li>
              </ul>
          </div>
      </li>



    <li class="nav-item {{ active_class(['basic-ui/*']) }}">
      <a class="nav-link" data-toggle="collapse" href="#basic-ui" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
        <i class="menu-icon mdi mdi-dna"></i>
        <span class="menu-title">Basic UI Elements</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ show_class(['basic-ui/*']) }}" id="basic-ui">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
            <a class="nav-link"
               href="{{ url('/basic-ui/buttons') }}">Buttons</a>
          </li>

          <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
            <a class="nav-link" href="{{ url('/basic-ui/dropdowns') }}">Dropdowns</a>
          </li>
          <li class="nav-item {{ active_class(['basic-ui/typography']) }}">
            <a class="nav-link" href="{{ url('/basic-ui/typography') }}">Typography</a>
          </li>
        </ul>
      </div>
    </li>


    <li class="nav-item {{ active_class(['charts/chartjs']) }}">
      <a class="nav-link" href="{{ url('/charts/chartjs') }}">
        <i class="menu-icon mdi mdi-chart-line"></i>
        <span class="menu-title">Charts</span>
      </a>
    </li>

    <!--
    <li class="nav-item {{ active_class(['tables/basic-table']) }}">
      <a class="nav-link" href="{{ url('/tables/basic-table') }}">
        <i class="menu-icon mdi mdi-table-large"></i>
        <span class="menu-title">Tables</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['icons/material']) }}">
      <a class="nav-link" href="{{ url('/icons/material') }}">
        <i class="menu-icon mdi mdi-emoticon"></i>
        <span class="menu-title">Icons</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['user-pages/*']) }}">
      <a class="nav-link" data-toggle="collapse" href="#user-pages" aria-expanded="{{ is_active_route(['user-pages/*']) }}" aria-controls="user-pages">
        <i class="menu-icon mdi mdi-lock-outline"></i>
        <span class="menu-title">User Pages</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ show_class(['user-pages/*']) }}" id="user-pages">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item {{ active_class(['user-pages/login']) }}">
            <a class="nav-link" href="{{ url('/user-pages/login') }}">Login</a>
          </li>
          <li class="nav-item {{ active_class(['user-pages/register']) }}">
            <a class="nav-link" href="{{ url('/user-pages/register') }}">Register</a>
          </li>
          <li class="nav-item {{ active_class(['user-pages/lock-screen']) }}">
            <a class="nav-link" href="{{ url('/user-pages/lock-screen') }}">Lock Screen</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="https://www.bootstrapdash.com/demo/star-laravel-free/documentation/documentation.html" target="_blank">
        <i class="menu-icon mdi mdi-file-outline"></i>
        <span class="menu-title">Documentation</span>
      </a>
    </li>
    -->

      <li class="nav-item {{ active_class(['basic-ui/system']) }}">
          <a class="nav-link" data-toggle="collapse" href="#system" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-cogs"></i>
              <span class="menu-title">System Variables</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/system']) }}" id="system">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/') }}">Overrides</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ url('/') }}">Data Configurations</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item {{ active_class(['basic-ui/clients']) }}">
          <a class="nav-link" data-toggle="collapse" href="#clients" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
              <i class="menu-icon mdi mdi-office-building"></i>
              <span class="menu-title">Payroll Settings</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ show_class(['basic-ui/clients']) }}" id="clients">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
                      <a class="nav-link" href="{{ url('/sss-contributions') }}">SSS table</a>
                  </li>
                  <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
                      <a class="nav-link" href="{{ url('/payroll-rates') }}">Payroll Rates</a>
                  </li>
              </ul>
          </div>
      </li>
  </ul>
</nav>
