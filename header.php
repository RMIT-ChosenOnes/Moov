<nav class="navbar navbar-expand-lg navbar-dark bg-logo">
	<a class="navbar-brand ml-lg-4" href="/chosen-ones/">
		<img src="assets/logo/moov_logo_100x50.png" class="d-inline-block align-top w-100" alt="Moov" loading="lazy">
	</a>
	
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigationBar" aria-controls="navigationBar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
    
    <?php
    if ($_SESSION['moov_user_avatar_status'] == 0) {
        $avatar_file_path = 'moov_default_avatar_500x500.png';

    } elseif ($_SESSION['moov_user_avatar_status'] == 1) {
        $avatar_file_path = 'avatar_' . $_SESSION['moov_user_account_id'] . '.' . $_SESSION['moov_user_avatar_type'];

    }
    ?>
	
	<div class="collapse navbar-collapse ml-lg-4" id="navigationBar">
		<ul class="navbar-nav">
			<li class="nav-item <?php echo isset($page_name) && $page_name == 'about us' ? 'active' : ''; ?>">
				<a class="nav-link" href="about-us">About Us</a>
			</li>
			
			<li class="nav-item <?php echo isset($page_name) && $page_name == 'find car' ? 'active' : ''; ?>">
				<a class="nav-link" href="book">Find a Car</a>
			</li>
			
			<li class="nav-item <?php echo isset($page_name) && $page_name == 'support' ? 'active' : ''; ?>">
				<a class="nav-link" href="support">Support</a>
			</li>
            
            <?php
            if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
				echo '
				<li class="nav-item d-block d-lg-none dropdown ' . (isset($parent_page_name) && $parent_page_name == 'account' ? 'active' : '') . '">
					<a class="nav-link dropdown-toggle" href="/moov/" id="userDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Welcome back, ' . $_SESSION['moov_user_display_name'] . '</a>

					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropDownMenu">
						<a class="dropdown-item ' . (isset($page_name) && $page_name == 'my-account' ? 'active' : '') . '" href="/moov/my-account">My Account</a>
						<a class="dropdown-item" href="/moov/logout">Logout</a>
					</div>
				</li>
				';
				
			} else {
				echo '
				<li class="nav-item d-block d-lg-none ' . (isset($page_name) && $page_name == 'login' ? 'active' : '') . '">
					<a class="nav-link" href="/moov/login">Login</a>
				</li>
				
				<li class="nav-item d-block d-lg-none ' . (isset($page_name) && $page_name == 'register' ? 'active' : '') . '">
					<a class="nav-link" href="/moov/register">Register</a>
				</li>
				';
				
			}
            ?>
		</ul>
	</div>
	
	<div class="d-none d-lg-block">
		<ul class="navbar-nav float-right mr-lg-4">
			<?php
			if (isset($_SESSION['moov_user_logged_in']) && $_SESSION['moov_user_logged_in'] == TRUE) {
				echo '
				<li class="nav-item dropdown ' . (isset($parent_page_name) && $parent_page_name == 'account' ? 'active' : '') . '">
					<a class="nav-link nav-account dropdown-toggle" href="/moov/" id="userDropDownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Welcome back, ' . $_SESSION['moov_user_display_name'] . '&nbsp;&nbsp;&nbsp;<img class="rounded-circle nav-avatar" style="background: url(\'/moov/avatar/' . $avatar_file_path . '\')"></a>

					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropDownMenu">
						<a class="dropdown-item ' . (isset($page_name) && $page_name == 'my-account' ? 'active' : '') . '" href="/moov/my-account">My Account</a>
						<a class="dropdown-item" href="/moov/logout">Logout</a>
					</div>
				</li>
				';
				
			} else {
				echo '
				<li class="nav-item ' . (isset($page_name) && $page_name == 'login' ? 'active' : '') . '">
					<a class="nav-link" href="/moov/login">Login</a>
				</li>
				
				<li class="nav-item ' . (isset($page_name) && $page_name == 'register' ? 'active' : '') . '">
					<a class="nav-link" href="/moov/register">Register</a>
				</li>
				';
				
			}
			?>
		</ul>
	</div>
</nav>