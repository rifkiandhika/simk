<style>
  .treeview-menu li.active > a {
  background-color: #e9f3ff;
  color: #0d6efd;
  border-radius: 6px;
}

</style>
<!-- Sidebar wrapper starts -->
<nav id="sidebar" class="sidebar-wrapper">
  <!-- Sidebar profile starts -->
  <div class="sidebar-profile">
    <img src="{{ asset('assets/images/user6.png') }}" 
         class="img-shadow img-3x me-3 rounded-5" 
         alt="User Profile">

    <div class="m-0">

        {{-- Nama user --}}
        <h5 class="mb-1 profile-name text-nowrap text-truncate">
            {{ Auth::check() ? Auth::user()->name : 'Guest' }}
        </h5>

        {{-- Role user --}}
        <p class="m-0 small profile-name text-nowrap text-truncate">
            {{ Auth::check() && Auth::user()->roles->first() 
                ? Auth::user()->roles->first()->name 
                : 'No Role' 
            }}
        </p>

    </div>
</div>

  <!-- Sidebar profile ends -->

  <!-- Sidebar menu starts -->
  <div class="sidebarMenuScroll">
  <ul class="sidebar-menu">
    <li class="{{ Request::is('/') || Request::is('dashboard') ? 'active current-page' : '' }}">
      <a href="{{ url('/dashboard') }}">
        <i class="ri-home-6-line"></i>
        <span class="menu-text">Dashboard</span>
      </a>
    </li>

    {{-- Apotik --}}
    <li class="treeview {{ Request::is('pasiens*') || Request::is('tagihans*')  ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-heart-pulse-line"></i>
        <span class="menu-text">Loket</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('pasiens') ? 'active' : '' }}">
          <a href="{{ route('pasiens.index') }}">Registrasi Pasien</a>
        </li>
        <li class="{{ Request::is('tagihans') ? 'active' : '' }}">
          <a href="{{ route('tagihans.index') }}">Tagihan Pasien</a>
        </li>
      </ul>
    </li>

    {{-- Apotik --}}
    <li class="treeview {{ Request::is('apotik*') || Request::is('stock_apotiks*') || Request::is('permintaans*') && request('from') == 'apotik' ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-heart-pulse-line"></i>
        <span class="menu-text">Manajement Apotik</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('apotik') ? 'active' : '' }}">
          <a href="{{ route('apotik.index') }}">Apotik</a>
        </li>
        <li class="{{ Request::is('stock_apotiks') ? 'active' : '' }}">
          <a href="{{ route('stock_apotiks.index') }}">Stock Apotik</a>
        </li>
        <li class="{{ Request::is('purchase-orders')}}">
          <a href="{{ route('po.index') }}">PO</a>
        </li>
      </ul>
    </li>

    {{-- Supplier --}}
    <li class="treeview {{ Request::is('suppliers*') ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-folder-user-line"></i>
        <span class="menu-text">Manajement Supplier</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('suppliers') ? 'active' : '' }}">
          <a href="{{ route('suppliers.index') }}">Supplier</a>
        </li>
        <li class="{{ Request::is('purchase-orders')}}">
          <a href="{{ route('po.index') }}">PO</a>
        </li>
      </ul>
    </li>

    {{-- Gudang --}}
    <li class="treeview {{ Request::is('gudangs*') || Request::is('po*') || Request::is('tagihan*')  ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-folder-user-line"></i>
        <span class="menu-text">Manajement Gudang</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('gudangs') ? 'active' : '' }}">
          <a href="{{ route('gudangs.index') }}">Gudang</a>
        </li>
        <li class="{{ Request::is('po') ? 'active' : ''}}">
          <a href="{{ route('po.index') }}">PO</a>
        </li>
        <li class="{{ Request::is('tagihan') ? 'active' : ''}}">
          <a href="{{ route('tagihan.index') }}">Tagihan</a>
        </li>
      </ul>
    </li>

    {{-- Settings --}}
    <li class="treeview {{ Request::is('departments*') || Request::is('obat-masters*') || Request::is('obatrs*') || Request::is('asuransis*') || Request::is('alkes*') || Request::is('reagens*') || Request::is('satuans*') || Request::is('jenis*') ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-database-line"></i>
        <span class="menu-text">Master Data</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('obat-masters') ? 'active' : '' }}">
          <a href="{{ route('obat-masters.index') }}">Master Obat</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('departments') ? 'active' : '' }}">
          <a href="{{ route('departments.index') }}">Data Department</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('obatrs') ? 'active' : '' }}">
          <a href="{{ route('obatrs.index') }}">Data Obat Rs</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('asuransis') ? 'active' : '' }}">
          <a href="{{ route('asuransis.index') }}">Data Asuransi</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('reagens') ? 'active' : '' }}">
          <a href="{{ route('reagens.index') }}">Data Reagen</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('alkes') ? 'active' : '' }}">
          <a href="{{ route('alkes.index') }}">Data Alkes</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('satuans') ? 'active' : '' }}">
          <a href="{{ route('satuans.index') }}">Data Satuan</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('jenis') ? 'active' : '' }}">
          <a href="{{ route('jenis.index') }}">Data Jenis</a>
        </li>
      </ul>
    </li>
    {{-- Users --}}
    <li class="treeview {{ Request::is('karyawans*') || Request::is('role-permissions*') || Request::is('users*') ? 'active' : '' }}">
      <a href="#!">
        <i class="ri-user-2-line"></i>
        <span class="menu-text">Manajement Users</span>
      </a>
      <ul class="treeview-menu">
        <li class="{{ Request::is('users') ? 'active' : '' }}">
          <a href="{{ route('users.index') }}">Users</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('karyawans') ? 'active' : '' }}">
          <a href="{{ route('karyawans.index') }}">Data Karyawan</a>
        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="{{ Request::is('role-permissions') ? 'active' : '' }}">
          <a href="{{ route('role-permissions.index') }}">Role Permission</a>
        </li>
      </ul>
    </li>
  </ul>
</div>

  <!-- Sidebar contact starts -->
  <div class="sidebar-contact">
    <p class="fw-light mb-1 text-nowrap text-truncate">Emergency Contact</p>
    <h5 class="m-0 lh-1 text-nowrap text-truncate">0987654321</h5>
    <i class="ri-phone-line"></i>
  </div>
  <!-- Sidebar contact ends -->
</nav>
<!-- Sidebar wrapper ends -->