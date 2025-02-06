<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/">MyApp</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('create.acc.show') }}">Manage Accounts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/transactions">Transactions</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/assets">Assets</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('liabilities.index')}}">Liabilty</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('equities.index')}}">Equity</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('expenses.index')}}">Expense</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('currencies.currencies')}}">Currencies</a>
          </li>

        </ul>

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          @guest
            <li class="nav-item">
              <a class="nav-link" href="/login">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/register">Register</a>
            </li>
          @else
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Logout</button>
                  </form>
                </li>
              </ul>
            </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>


