<?php
	$this->assign('title','TimeCase | Users');
	$this->assign('nav','users');

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
	.script("scripts/app/users.js").wait(function(){
		$(document).ready(function(){
			page.init();
		});
		
		// hack for IE9 which may respond inconsistently with document.ready
		setTimeout(function(){
			if (!page.isInitialized) page.init();
		},1000);
	});
</script>

<!-- modal edit dialog -->
<div class="modal hide fade" id="userDetailDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> User
			<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="modelAlert"></div>
		<div id="userModelContainer"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="saveUserButton" class="btn btn-primary">Save Changes</button>
	</div>
</div>
	
<div class="container main">

<div class="row">
<div class="span2">

	<hr>
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newUserButton" class="btn btn-primary btn-sidebar"><i class="icon-plus"></i>&nbsp; Add User</button>
	</p>
	<hr>
	
<?php $this->display('_SidebarCommon.tpl.php');?>


</div>

<div class="span10">

	<h1>
		<i class="icon-user"></i> Users
		<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
		<span class='input-append pull-right searchContainer'>
			<input id='filter' type="text" placeholder="Search..." />
			<button class='btn add-on'><i class="icon-search"></i></button>
		</span>
	</h1>
	<div class="clearfix"></div>
	
	<!-- underscore template for the collection -->
	<script type="text/template" id="userCollectionTemplate">
		<table class="collection table table-bordered">
		<thead>
			<tr>
				<th id="header_Username"><i class="icon-user"></i>&nbsp; Username<# if (page.orderBy == 'Username') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_LevelId"><i class="icon-cog"></i>&nbsp; Level<# if (page.orderBy == 'LevelId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_FullName"><i class="icon-reorder"></i>&nbsp; Full Name<# if (page.orderBy == 'FullName') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Email"><i class="icon-envelope"></i>&nbsp; Email<# if (page.orderBy == 'Email') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
			</tr>
		</thead>
		<tbody>
		<# items.each(function(item) { #>
			<tr id="<#= _.escape(item.get('id')) #>">
				<td><#= _.escape(item.get('username') || '') #></td>
				<td><#= _.escape(item.get('levelName') || '') #></td>
				<td><#= _.escape(item.get('fullName') || '') #></td>
				<td><#= _.escape(item.get('email') || '') #></td>
			</tr>
		<# }); #>
		</tbody>
		</table>

		<#=  view.getPaginationHtml(page) #>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="userModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group" style="display:none">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><#= _.escape(item.get('id') || '') #></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="usernameInputContainer" class="control-group">
					<label class="control-label" for="username">Username</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="username" placeholder="Username" value="<#= _.escape(item.get('username') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="levelIdInputContainer" class="control-group">
					<label class="control-label" for="levelId">Level</label>
					<div class="controls inline-inputs">
						<select id="levelId" name="levelId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="fullNameInputContainer" class="control-group">
					<label class="control-label" for="fullName">Full Name</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="fullName" placeholder="Full Name" value="<#= _.escape(item.get('fullName') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="emailInputContainer" class="control-group">
					<label class="control-label" for="email">Email</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="email" placeholder="Email" value="<#= _.escape(item.get('email') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="passwordInputContainer" class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls inline-inputs">
						<input type="password" class="input-xlarge" id="password" placeholder="Password">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="detailsInputContainer" class="control-group">
					<label class="control-label" for="details">Details</label>
					<div class="controls inline-inputs">
						<textarea class="input-xlarge" id="details" rows="3"><#= _.escape(item.get('details') || '') #></textarea>
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteUserButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteUserButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete User</button>
						<span id="confirmDeleteUserContainer" class="hide">
							<button id="cancelDeleteUserButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteUserButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<div id="userCollectionContainer" class="collectionContainer">
	</div>

</div>
</div> <!-- /row -->

	<!-- footer -->
	<hr>

	<footer>
		
	</footer>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
