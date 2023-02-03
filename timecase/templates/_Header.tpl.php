<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<base href="<?php $this->eprint($this->ROOT_URL); ?>" />
	<title><?php $this->eprint($this->title); ?></title>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="TimeCase helps you keep track of your time. It is a powerful yet easy to use web application for everyone who wants to see how much time is spent on certain tasks and projects" />
	<meta name="author" content="alcalbg | interactive32.com" />
	<meta name="generator" content="interactive32.com">
	<meta name="keywords" content="timecase, timecase.net, time tracking, timetracker, time, tracker, apps, best, online, webapp, ipod, iphone, project, task">

	<!-- Le styles -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="styles/style.css" rel="stylesheet" />
	<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="bootstrap/css/font-awesome.css" rel="stylesheet" />
	<link href="bootstrap/css/bootstrap-datepicker.css" rel="stylesheet" />
	<link href="bootstrap/css/bootstrap-combobox.css" rel="stylesheet" />
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script type="text/javascript" src="scripts/libs/html5.js"></script>
	<![endif]-->
	
	<!--[if lt IE 8]>
		<script type="text/javascript" src="scripts/libs/json2.js"></script>
	<![endif]-->

	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="images/favicon.ico" />
	
	<script type="text/javascript">

		// init global js vars
		<?php if ($this->useSessionTimer):?>
		var g_time_start = <?php echo (isset($_SESSION['timetracking']) ? 'Math.round(new Date().getTime() / 1000) - ' . (time() - $_SESSION['timetracking']) : -1)?>;
		<?php else:?>
		var g_time_start = <?php echo ($this->timer ? 'Math.round(new Date().getTime() / 1000) - ' . ($this->timer) : -1)?>;
		<?php endif;?>

		var g_time_elapsed = 0;
		var g_long_polling_duration = <?php echo GlobalConfig::$LONG_POLLING_DURATION; ?>;

		// we need user level inside js, not used for security just for display tweeks
		var g_level_id = <?php echo (isset($this->currentUser->LevelId) ? $this->currentUser->LevelId : 0);?>;
		
	</script>
	
	<script type="text/javascript" src="scripts/libs/LAB.min.js"></script>
	<script type="text/javascript">
		$LAB
			.script("scripts/libs/jquery-1.8.2.min.js").wait()
			.script("bootstrap/js/bootstrap.min.js");
	</script>
	
</head>

<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="">TimeCase</a>
			
			<span id="currentTimetrack"></span>
			
			<div class="nav-collapse collapse">
			
			<?php if ($this->currentUser):?>
			
				<ul class="nav">
				
					<?php if(!($this->currentUser->LevelId & $this->ROLE_CUSTOMER)):?>
					<li <?php if ($this->nav=='timeentries') { echo 'class="active"'; } ?>><a href="timeentries">Time Tracking</a></li>
					<?php endif;?>
				
					<?php if($this->currentUser->LevelId & ($this->ROLE_ADMIN | $this->ROLE_MANAGER)):?>
					<li <?php if ($this->nav=='customers') { echo 'class="active"'; } ?>><a href="customers">Customers</a></li>
					<li <?php if ($this->nav=='projects') { echo 'class="active"'; } ?>><a href="projects">Projects</a></li>
					<?php endif;?>
					
					<?php if(!($this->currentUser->LevelId & $this->ROLE_BASIC_USER)):?>
					<li <?php if ($this->nav=='reports') { echo 'class="active"'; } ?>><a href="reports">Reports</a></li>
					<?php endif;?>
					
					<?php if($this->currentUser->LevelId & $this->ROLE_ADMIN):?>
					<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Settings <b class="caret"></b></a>
					<ul class="dropdown-menu">
					<li <?php if ($this->nav=='users') { echo 'class="active"'; } ?>><a href="users">Users</a></li>
					<li <?php if ($this->nav=='categories') { echo 'class="active"'; } ?>><a href="categories">Work Types</a></li>
					<li <?php if ($this->nav=='statuses') { echo 'class="active"'; } ?>><a href="statuses">Statuses</a></li>
					</ul>
					</li>
					<?php endif;?>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo ucfirst($this->currentUser->Username)?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
						<?php if(!($this->currentUser->LevelId & ($this->ROLE_CUSTOMER))):?>
						<li><a href="accountsettings">Account Settings</a></li>
						<?php endif;?>
						<li><a data-toggle="modal" href="#aboutUs">About TimeCase</a></li>
						<li><a href="logout">Logout</a></li>
						</ul>
					</li>

				</ul>
			<?php else:?>
				<ul class="nav pull-right">
					<li><a href="loginform">Login <i class="icon-lock"></i></a></li>
				</ul>
			<?php endif;?>
			</div><!--/.nav-collapse -->
			
		</div>
	</div>
</div>


<div class="modal hide fade" id="aboutUs">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>TimeCase</h3>
	</div>
	<div class="modal-body" style="max-height: 300px">
	
		<p>TimeCase helps you keep track of your time. It is a powerful yet easy to use web application for everyone who wants to see how much time is spent on certain tasks and projects.</p>

		<p>Version: TimeCase v2.0</p>

		<p>For more info please visit <a target="_blank" href="http://timecase.net">http://timecase.net</a></p>
		
	
		<br />

		<p>Copyright &copy; 2008 - <?php echo date('Y'); ?> <a href="http://interactive32.com">Interactive32.com</a>. All rights reserved.</p>
	
			
		<p>&nbsp;</p>
	</div>
	<div class="modal-footer">
		<button id="okButton" data-dismiss="modal" class="btn btn-primary">Close</button>
	</div>
</div>
