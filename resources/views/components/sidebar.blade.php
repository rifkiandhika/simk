<!-- Sidebar wrapper starts -->
<nav id="sidebar" class="sidebar-wrapper">
  <!-- Sidebar profile starts -->
  <div class="sidebar-profile">
    <img src="{{ asset('assets/images/user6.png') }}" class="img-shadow img-3x me-3 rounded-5" alt="Hospital Admin Templates">
    <div class="m-0">
      <h5 class="mb-1 profile-name text-nowrap text-truncate">Nick Gonzalez</h5>
      <p class="m-0 small profile-name text-nowrap text-truncate">Dept Admin</p>
    </div>
  </div>
  <!-- Sidebar profile ends -->

  <!-- Sidebar menu starts -->
  <div class="sidebarMenuScroll">
    <ul class="sidebar-menu">
      <li class="{{ Request::is('/') || Request::is('dashboard') ? 'active current-page' : '' }}">
        <a href="{{ url('/dashboard') }}">
          <i class="ri-home-6-line"></i>
          <span class="menu-text">Hospital Dashboard</span>
        </a>
      </li>
      
      <li class="{{ Request::is('doctors*') ? 'active' : '' }}">
        <a href="{{ url('/doctors') }}">
          <i class="ri-stethoscope-line"></i>
          <span class="menu-text">Doctors</span>
        </a>
      </li>
      
      <li class="treeview {{ Request::is('patients*') ? 'active' : '' }}">
        <a href="#!">
          <i class="ri-heart-pulse-line"></i>
          <span class="menu-text">Patients</span>
        </a>
        <ul class="treeview-menu">
          <li class="{{ Request::is('patients') ? 'active' : '' }}">
            <a href="{{ url('/patients') }}">Patients List</a>
          </li>
          <li class="{{ Request::is('patients/create') ? 'active' : '' }}">
            <a href="{{ url('/patients/create') }}">Add Patient</a>
          </li>
        </ul>
      </li>

      <li class="treeview {{ Request::is('appointments*') ? 'active' : '' }}">
        <a href="#!">
          <i class="ri-dossier-line"></i>
          <span class="menu-text">Appointments</span>
        </a>
        <ul class="treeview-menu">
          <li class="{{ Request::is('appointments') ? 'active' : '' }}">
            <a href="{{ url('/appointments') }}">Appointments List</a>
          </li>
          <li class="{{ Request::is('appointments/create') ? 'active' : '' }}">
            <a href="{{ url('/appointments/create') }}">Book Appointment</a>
          </li>
        </ul>
      </li>

      <li class="treeview {{ Request::is('staff*') ? 'active' : '' }}">
        <a href="#!">
          <i class="ri-nurse-line"></i>
          <span class="menu-text">Staff</span>
        </a>
        <ul class="treeview-menu">
          <li class="{{ Request::is('staff') ? 'active' : '' }}">
            <a href="{{ url('/staff') }}">Staff List</a>
          </li>
          <li class="{{ Request::is('staff/create') ? 'active' : '' }}">
            <a href="{{ url('/staff/create') }}">Add Staff</a>
          </li>
        </ul>
      </li>

      <li class="{{ Request::is('reports*') ? 'active' : '' }}">
        <a href="{{ url('/reports') }}">
          <i class="ri-bar-chart-line"></i>
          <span class="menu-text">Reports</span>
        </a>
      </li>

      <li class="{{ Request::is('settings*') ? 'active' : '' }}">
        <a href="{{ url('/settings') }}">
          <i class="ri-settings-5-line"></i>
          <span class="menu-text">Settings</span>
        </a>
      </li>
    </ul>
  </div>
  <!-- Sidebar menu ends -->

  <!-- Sidebar contact starts -->
  <div class="sidebar-contact">
    <p class="fw-light mb-1 text-nowrap text-truncate">Emergency Contact</p>
    <h5 class="m-0 lh-1 text-nowrap text-truncate">0987654321</h5>
    <i class="ri-phone-line"></i>
  </div>
  <!-- Sidebar contact ends -->
</nav>
<!-- Sidebar wrapper ends -->