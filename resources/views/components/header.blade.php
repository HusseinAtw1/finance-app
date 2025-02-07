<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
	<div class="container">
		<a class="navbar-brand fw-bold" href="/">MyApp</a>
		<button
			class="navbar-toggler"
			type="button"
			data-bs-toggle="collapse"
			data-bs-target="#navbarNav"
			aria-controls="navbarNav"
			aria-expanded="false"
			aria-label="Toggle navigation"
		>
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
				<!-- Dropdown for Financial Categories -->
				<li class="nav-item dropdown">
					<a
						class="nav-link dropdown-toggle"
						href="#"
						id="financialDropdown"
						role="button"
						data-bs-toggle="dropdown"
						aria-expanded="false"
					>
						Financial Categories
					</a>
					<ul class="dropdown-menu" aria-labelledby="financialDropdown">
						<li>
							<a class="dropdown-item" href="/assets">Assets</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('liabilities.index') }}">Liability</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('equities.index') }}">Equity</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('expenses.index') }}">Expense</a>
						</li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a
						class="nav-link dropdown-toggle"
						href="#"
						id="addonsDropdown"
						role="button"
						data-bs-toggle="dropdown"
						aria-expanded="false"
					>
						Add-Ons
					</a>
					<ul class="dropdown-menu" aria-labelledby="addonsDropdown">
						<li>
							<a class="dropdown-item" href="{{ route('asset_types.show') }}">Assets Types</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('currencies.currencies') }}">Currencies</a>
						</li>
					</ul>
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
						<a
							class="nav-link dropdown-toggle"
							href="#"
							id="userDropdown"
							role="button"
							data-bs-toggle="dropdown"
							aria-expanded="false"
						>
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
