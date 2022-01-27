<?php
	$this->assign('title','TimeCase | Home');
	$this->assign('nav','home');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("bootstrap/js/bootstrap-datepicker.js")
	.script("bootstrap/js/bootstrap-combobox.js")
	.script("scripts/libs/underscore-min.js").wait()
	.script("scripts/libs/underscore.date.min.js")
	.script("scripts/libs/backbone.js")
	.script("scripts/app.js")
	.script("scripts/model.js").wait()
	.script("scripts/view.js").wait()
	.script("scripts/timecase.js").wait()

</script>

	<div class="container main">

		<!-- Main hero unit for a primary marketing message or call to action -->
		<div class="hero-unit">
		<div class="row">
		    <div class="span8">
				<!-- Main hero unit for a primary marketing message or call to action -->
				<div>
					<h1>TimeCase</h1>
					
					<br>
					<p>Manage your projects and activities with accuracy and efficiency.</p>
					
					<br /> 
					<a href="loginform" class="btn btn-primary btn-large">Login &raquo;</a>
					
				</div>
		    </div>
		     <div class="span2">
				<div class="hero-img">
					<br><img alt="TimeCase" src="images/iconb_small.png">
				</div>
		     </div>
		  </div>

		</div>
		
		<div class="row">
			<div class="span3">
				<h2><i class="icon-time"></i> Time Tracking</h2>
				<p>Keep track of your time with accuracy and precision.
				Use the same web app on your desktop, tablet or phone.</p>
			</div>
			<div class="span3">
				<h2><i class="icon-briefcase"></i> Customers</h2>
				 <p>Allow your customers to login and analyze time spent on their projects and view reports.</p>
			</div>
			<div class="span3">
				<h2><i class="icon-user"></i> Users</h2>
				 <p>Users will adapt quickly to this modern and easy to use web application. 
				 You can assign different roles to different users and pick team managers.</p>
		 	</div>

			<div class="span3">
				<h2><i class="icon-file"></i> Reports</h2>
				<p>View and analyze reports in real-time with flexible ajax filters. Export data as csv file or display them as printable html.</p>
			</div>
		</div>

		<hr>
		<footer>
			<p>Copyright &copy; 2008 - <?php echo date('Y'); ?> <a href="http://interactive32.com">Interactive32.com</a>. All rights reserved.</p>
		</footer>

	</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
