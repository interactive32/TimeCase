<?php
	$this->assign('title','TimeCase | Login');
	$this->assign('nav','securee');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/timecase.js").wait()
</script>

<div class="container main">

	<?php if ($this->feedback) { ?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php $this->eprint($this->feedback); ?>
		</div>
	<?php } ?>
	
	
		<form class="well form-horizontal" method="post" action="loginattempt">
		
			<fieldset>
			<legend>Login <i class="icon-lock"></i></legend>
				<div class="control-group">
					<label class="control-label" for="username">Username</label>
					<div class="controls">
						<input id="username" name="username" type="text" placeholder="Username..." />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input id="password" name="password" type="password" placeholder="Password..." />
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Login</button>
					</div>
				</div>
			</fieldset>
		</form>


	<!-- footer -->

	<footer>
		
	</footer>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>