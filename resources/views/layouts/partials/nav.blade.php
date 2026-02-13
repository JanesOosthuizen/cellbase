<nav class="app-nav">
    <div class="app-nav__inner">
        <a href="{{ route('dashboard') }}" class="app-nav__brand">{{ config('app.name', 'CellBase') }}</a>
        <ul class="app-nav__menu">
            <li><a href="{{ route('dashboard') }}" class="app-nav__link {{ request()->routeIs('dashboard') ? 'app-nav__link--active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('repairs.index') }}" class="app-nav__link {{ request()->routeIs('repairs.*') ? 'app-nav__link--active' : '' }}">Repairs</a></li>
            <li class="app-nav__dropdown">
                <button type="button" class="app-nav__link app-nav__dropdown-trigger {{ request()->routeIs('imeis.*') ? 'app-nav__link--active' : '' }}" aria-expanded="false" aria-haspopup="true">Imeis</button>
                <div class="app-nav__dropdown-menu" role="menu">
                    <a href="{{ route('imeis.index') }}" class="app-nav__dropdown-item">View All IMEIs</a>
                    <a href="#" class="app-nav__dropdown-item">Search Imeis</a>
                    <a href="#" class="app-nav__dropdown-item">Process Claims</a>
                    <a href="#" class="app-nav__dropdown-item">View Unresolved Claims</a>
                </div>
            </li>
            <li class="app-nav__dropdown">
                <button type="button" class="app-nav__link app-nav__dropdown-trigger {{ request()->routeIs('invoices.*') ? 'app-nav__link--active' : '' }}" aria-expanded="false" aria-haspopup="true">Invoices</button>
                <div class="app-nav__dropdown-menu" role="menu">
                    <a href="{{ route('invoices.index') }}" class="app-nav__dropdown-item">View Invoices</a>
                    <a href="{{ route('invoices.create') }}" class="app-nav__dropdown-item">Add New Invoice</a>
                </div>
            </li>
            <li><a href="{{ route('customers.index') }}" class="app-nav__link {{ request()->routeIs('customers.*') ? 'app-nav__link--active' : '' }}">Customers</a></li>
            <li><a href="{{ route('orders.index') }}" class="app-nav__link {{ request()->routeIs('orders.*') ? 'app-nav__link--active' : '' }}">Orders</a></li>
            <li><a href="{{ route('settings') }}" class="app-nav__link {{ request()->routeIs('settings*') ? 'app-nav__link--active' : '' }}">Settings</a></li>
        </ul>
        <div class="app-nav__user">
            <span class="app-nav__user-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="app-nav__logout-form">
                @csrf
                <button type="submit" class="app-nav__logout">Log out</button>
            </form>
        </div>
    </div>
</nav>
