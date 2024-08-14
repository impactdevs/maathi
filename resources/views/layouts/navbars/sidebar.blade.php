        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex" href="/dashboard">
                <div class="sidebar-brand-text mx-3">MAATHI</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Heading -->
            <div class="sidebar-heading">
                MAIN SECTIONS
            </div>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <li class="nav-item {{ Request::is('top-up-funds') ? 'active' : '' }}">
                <a class="nav-link" href="/top-up-funds">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Top Up Funds</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ Request::is('funds-disbursement') ? 'active' : '' }}">
                <a class="nav-link" href="/funds-disbursement">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Funds Disbursement</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item {{ Request::is('payout-management') ? 'active' : '' }}">
                <a class="nav-link" href="/payout-management">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Payout Management</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item {{ Request::is('cashout-management') ? 'active' : '' }}">
                <a class="nav-link" href="/cashout-management">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Cashout Management</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ Request::is('user-management') ? 'active' : '' }}">
                <a class="nav-link" href="/user-management">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Beneficiaries Management</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ Request::is('accounts-management') ? 'active' : '' }}">
                <a class="nav-link" href="/accounts-management">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Account Management</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->
