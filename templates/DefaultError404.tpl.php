<?php
	$this->assign('title','TimeCase | File Not Found');
	$this->assign('nav','home');

	$this->display('_Header.tpl.php');
?>

<div class="container main">

	<h1>Oh Snap!</h1>

	<!-- this is used by app.js for scraping -->
	<!-- ERROR The page you requested was not found /ERROR -->

	<p>The page you requested was not found.  Please check that you typed the URL correctly.</p>

<!-- footer -->
	<hr>

	<footer>
		
	</footer>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>