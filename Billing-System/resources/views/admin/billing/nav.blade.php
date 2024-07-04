<div class="col-xs-12">
  <div class="nav-tabs-custom nav-tabs-floating">
    <ul class="nav nav-tabs">
      <li class="{{ Route::currentRouteName() !== 'admin.billing' ?: 'active' }}">
        <a href="{{ route('admin.billing') }}">General</a>
      </li>
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.invoices') ?: 'active' }}">
        <a href="{{ route('admin.billing.invoices') }}">Invoices</a>
      </li>
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.users') ?: 'active' }}">
        <a href="{{ route('admin.billing.users') }}">Users</a>
      </li>
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.categories') ?: 'active' }}">
        <a href="{{ route('admin.billing.categories') }}">Categories</a>
      </li>
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.products') ?: 'active' }}">
        <a href="{{ route('admin.billing.products') }}">Products</a>
      </li>
      <!--
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.deploy') ?: 'active' }}">
        <a href="{{ route('admin.billing.deploy') }}">Deploy</a>
      </li>
      -->
      <!--
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.promotional-codes') ?: 'active' }}">
        <a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a>
      </li>
      -->
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.tos') ?: 'active' }}">
        <a href="{{ route('admin.billing.tos') }}">TOS</a>
      </li>
      <!--
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.gateways') ?: 'active' }}">
        <a href="{{ route('admin.billing.gateways') }}">Gateways</a>
      </li>
      -->
      <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.billing.settings') ?: 'active' }}">
        <a href="{{ route('admin.billing.settings') }}">Settings</a>
      </li>
    </ul>
  </div>
</div>