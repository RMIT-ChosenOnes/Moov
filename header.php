<nav class="navbar navbar-expand-lg navbar-dark bg-logo">
	<a class="navbar-brand ml-lg-4" href="/chosen-ones/">
		<img src="assets/logo/moov_logo_100x50.png" class="d-inline-block align-top w-100" alt="Moov" loading="lazy">
	</a>
	
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigationBar" aria-controls="navigationBar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	
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
			
			<li class="nav-item <?php echo isset($page_name) && $page_name == 'login' ? 'active' : ''; ?>">
				<a class="nav-link" href="login">Login</a>
			</li>
		</ul>
	</div>
</nav>