<nav class="navbar navbar-expand-lg navbar-light bg-secondary sticky-top">
	<a class="navbar-brand ml-lg-4" href="/moov/portal/">
		<img src="/moov/portal/assets/logo/moov_portal_logo_100x50.png" class="d-inline-block align-top w-100" alt="Moov Portal" loading="lazy">
	</a>

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigationBar" aria-controls="navigationBar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse ml-lg-4" id="navigationBar">
		<ul class="navbar-nav">
			<li class="nav-item <?php echo isset($page_name) && $page_name == 'dashboard' ? 'active' : ''; ?>">
				<a class="nav-link" href="/moov/portal/">Dashboard</a>
			</li>

			<li class="nav-item <?php echo isset($page_name) && $page_name == 'bookings' ? 'active' : ''; ?>">
				<a class="nav-link disabled" href="bookings">Bookings</a>
			</li>
			
            <li class="nav-item dropdown <?php echo isset($parent_page_name) && $parent_page_name == 'car' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="/moov/portal/car/" id="databaseDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Car</a>

                <div class="dropdown-menu" aria-labelledby="databaseDropDownMenu">
                    <a class="dropdown-item <?php echo isset($page_name) && $page_name == 'new-car' ? 'active' : ''; ?>" href="/moov/portal/car/new-car">Register New Car</a>
                    <a class="dropdown-item <?php echo isset($page_name) && $page_name == 'car' ? 'active' : ''; ?>" href="/moov/portal/car">Modify Car</a>
                </div>
            </li>

			<li class="nav-item <?php echo isset($page_name) && $page_name == 'customer' ? 'active' : ''; ?>">
				<a class="nav-link" href="/moov/portal/customer/">Customer</a>
			</li>

			<?php
			if (isset($_SESSION['moov_portal_logged_in']) && $_SESSION['moov_portal_logged_in'] == TRUE && $_SESSION['moov_portal_staff_role'] == 'Admin') {
				echo '
                <li class="nav-item dropdown ' . (isset($parent_page_name) && $parent_page_name == 'staff' ? 'active' : '') . '">
				    <a class="nav-link dropdown-toggle" href="/moov/portal/staff/" id="staffDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Staff</a>
                    
                    <div class="dropdown-menu" aria-labelledby="staffDropDownMenu">
                        <a class="dropdown-item ' . (isset($page_name) && $page_name == 'new-staff' ? 'active' : '') . '" href="/moov/portal/staff/new-staff">Register New Staff</a>
                        <a class="dropdown-item ' . (isset($page_name) && $page_name == 'staff' ? 'active' : '') . '" href="/moov/portal/staff/">Modify Staff</a>
                    </div>
                </li>
                ';
			}
			?>

			<li class="nav-item dropdown <?php echo isset($parent_page_name) && $parent_page_name == 'database' ? 'active' : ''; ?>">
				<a class="nav-link dropdown-toggle" href="/moov/portal/database" id="databaseDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Report</a>

				<div class="dropdown-menu" aria-labelledby="databaseDropDownMenu">
					<a class="dropdown-item disabled" href="">Car Report</a>
				</div>
			</li>

			<li class="nav-item dropdown d-block d-lg-none">
				<a class="nav-link dropdown-toggle <?php echo isset($parent_page_name) && $parent_page_name == 'profile' ? 'active' : ''; ?>" href="/moov/portal/" id="portalStaffDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">G'day, <?php echo $_SESSION['moov_portal_staff_first_name']; ?></a>

				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="portalStaffDropDownMenu">
					<a class="dropdown-item <?php echo isset($page_name) && $page_name == 'my-account' ? 'active' : ''; ?>" href="/moov/portal/my-account">My Account</a>
					<a class="dropdown-item" href="/moov/portal/logout">Logout</a>
				</div>
			</li>
		</ul>
	</div>

	<div class="d-none d-lg-block">
		<ul class="navbar-nav float-right">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle <?php echo isset($parent_page_name) && $parent_page_name == 'profile' ? 'active' : ''; ?>" href="/moov/portal/logout" id="portalStaffDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">G'day, <?php echo $_SESSION['moov_portal_staff_first_name']; ?></a>

				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="portalStaffDropDownMenu">
					<a class="dropdown-item <?php echo isset($page_name) && $page_name == 'my-account' ? 'active' : ''; ?>" href="/moov/portal/my-account">My Account</a>
					<a class="dropdown-item" href="/moov/portal/logout">Logout</a>
				</div>
			</li>
		</ul>
	</div>
</nav>