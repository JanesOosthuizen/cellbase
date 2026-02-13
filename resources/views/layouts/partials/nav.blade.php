<nav class="app-nav">
    <div class="app-nav__inner">
        <a href="{{ route('dashboard') }}" class="app-nav__brand">{{ config('app.name', 'CellBase') }}</a>
        <ul class="app-nav__menu">
            <li><a href="{{ route('dashboard') }}" class="app-nav__link {{ request()->routeIs('dashboard') ? 'app-nav__link--active' : '' }}">Dashboard</a></li>
            <li class="app-nav__dropdown">
                <button type="button" class="app-nav__link app-nav__dropdown-trigger {{ request()->routeIs('repairs.*', 'loan-devices.*') ? 'app-nav__link--active' : '' }}" aria-expanded="false" aria-haspopup="true">Repairs</button>
                <div class="app-nav__dropdown-menu" role="menu">
                    <a href="{{ route('repairs.index') }}" class="app-nav__dropdown-item">Repairs</a>
                    <a href="{{ route('loan-devices.index') }}" class="app-nav__dropdown-item">Loan Devices</a>
                </div>
            </li>
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
        <div class="app-nav__search" id="global-search-wrap">
            <div class="app-nav__search-wrap">
                <input type="text" class="app-nav__search-input" id="global-search-input" placeholder="Search IMEIs, customers, repairs…" autocomplete="off" aria-label="Global search">
            </div>
            <div class="app-nav__search-dropdown" id="global-search-dropdown" role="listbox" aria-hidden="true"></div>
        </div>
        <div class="app-nav__user">
            <span class="app-nav__user-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="app-nav__logout-form">
                @csrf
                <button type="submit" class="app-nav__logout">Log out</button>
            </form>
        </div>
    </div>
</nav>

@push('scripts')
<script>
(function () {
    const wrap = document.getElementById('global-search-wrap');
    const input = document.getElementById('global-search-input');
    const dropdown = document.getElementById('global-search-dropdown');
    const url = '{{ route('api.global-search') }}';
    let debounceTimer = null;
    let loading = false;

    function render(data) {
        const hasAny = (data.imeis && data.imeis.length) || (data.customers && data.customers.length) || (data.repairs && data.repairs.length);
        if (!hasAny) {
            dropdown.innerHTML = '<div class="app-nav__search-empty">No results found</div>';
            dropdown.classList.add('is-open');
            dropdown.setAttribute('aria-hidden', 'false');
            return;
        }
        let html = '';
        if (data.imeis && data.imeis.length) {
            html += '<div class="app-nav__search-section-title">IMEIs</div>';
            data.imeis.forEach(function (item) {
                html += '<a href="' + escapeHtml(item.url) + '" class="app-nav__search-item" role="option">';
                html += '<span class="app-nav__search-item-label">' + escapeHtml(item.label) + '</span>';
                if (item.sub) html += '<span class="app-nav__search-item-sub">' + escapeHtml(item.sub) + '</span>';
                html += '</a>';
            });
        }
        if (data.customers && data.customers.length) {
            html += '<div class="app-nav__search-section-title">Customers</div>';
            data.customers.forEach(function (item) {
                html += '<a href="' + escapeHtml(item.url) + '" class="app-nav__search-item" role="option">';
                html += '<span class="app-nav__search-item-label">' + escapeHtml(item.label) + '</span>';
                if (item.sub) html += '<span class="app-nav__search-item-sub">' + escapeHtml(item.sub) + '</span>';
                html += '</a>';
            });
        }
        if (data.repairs && data.repairs.length) {
            html += '<div class="app-nav__search-section-title">Repairs</div>';
            data.repairs.forEach(function (item) {
                html += '<a href="' + escapeHtml(item.url) + '" class="app-nav__search-item" role="option">';
                html += '<span class="app-nav__search-item-label">' + escapeHtml(item.label) + (item.status ? ' · ' + escapeHtml(item.status) : '') + '</span>';
                if (item.sub) html += '<span class="app-nav__search-item-sub">' + escapeHtml(item.sub) + '</span>';
                html += '</a>';
            });
        }
        dropdown.innerHTML = html;
        dropdown.classList.add('is-open');
        dropdown.setAttribute('aria-hidden', 'false');
    }

    function escapeHtml(s) {
        if (!s) return '';
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function closeDropdown() {
        dropdown.classList.remove('is-open');
        dropdown.setAttribute('aria-hidden', 'true');
    }

    function doSearch() {
        const q = (input.value || '').trim();
        if (q.length < 2) {
            closeDropdown();
            return;
        }
        loading = true;
        dropdown.innerHTML = '<div class="app-nav__search-empty">Searching…</div>';
        dropdown.classList.add('is-open');
        dropdown.setAttribute('aria-hidden', 'false');

        fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                loading = false;
                if ((input.value || '').trim() === q) render(data);
            })
            .catch(function () {
                loading = false;
                if ((input.value || '').trim() === q) {
                    dropdown.innerHTML = '<div class="app-nav__search-empty">Search failed</div>';
                }
            });
    }

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 250);
    });

    input.addEventListener('focus', function () {
        if ((input.value || '').trim().length >= 2 && dropdown.classList.contains('is-open')) return;
        if ((input.value || '').trim().length >= 2) doSearch();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeDropdown(); input.blur(); }
    });

    wrap.addEventListener('focusout', function (e) {
        if (wrap.contains(e.relatedTarget)) return;
        setTimeout(closeDropdown, 150);
    });
})();
</script>
@endpush
