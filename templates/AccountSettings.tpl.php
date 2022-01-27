<?php
	$this->assign('title','TimeCase | Account Settings');
	$this->assign('nav','securee');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("bootstrap/js/bootstrap-datepicker.js")
	.script("bootstrap/js/bootstrap-combobox.js")
	.script("scripts/timecase.js").wait()
</script>

<div class="container main">

	<?php if ($this->feedback) { ?>
		<div class="alert alert-<?php $this->eprint($this->feedback['type']); ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php $this->eprint($this->feedback['text']); ?>
		</div>
	<?php } ?>
	
	
		<form class="form-horizontal" method="post" action="accountsettings">
			<fieldset>
			<legend class="pull-left">Account Settings</legend>
			
				<div class="control-group">
					<label for="fullname" class="control-label">Full Name</label>
					<div class="controls">
						<input type="text" value="<?php $this->eprint($this->user->FullName); ?>" placeholder="Full Name" id="fullname" name="fullname" class="input-xlarge">
					</div>
				</div>	
				
				<div class="control-group">
					<label for="email" class="control-label">Email</label>
					<div class="controls">
						<input type="text" value="<?php $this->eprint($this->user->Email); ?>" placeholder="Email" id="email" name="email" class="input-xlarge">
					</div>
				</div>
				
				<div class="control-group">
					<label for="password" class="control-label">Password</label>
					<div class="controls">
						<input type="password" placeholder="Reset Password" id="password" name="password" class="input-xlarge">
					</div>
				</div>
				
				<div class="control-group">
					<label for="details" class="control-label">Details</label>
					<div class="controls">
						<textarea id="details" name="details" class="input-xlarge" rows="5"><?php $this->eprint($this->user->Details); ?></textarea>
					</div>
				</div>
					
				<div class="control-group">
					<div class="controls">
					<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div>
				
			</fieldset>
		</form>
	

	<!-- footer -->
	<hr>

	<footer>
		
	</footer>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>