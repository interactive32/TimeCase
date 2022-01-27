<?php
	$this->assign('title','TimeCase | Customers');
	$this->assign('nav','customers');

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
	.script("scripts/app/customers.js").wait(function(){
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
<div class="modal hide fade" id="customerDetailDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> Customer
			<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="modelAlert"></div>
		<div id="customerModelContainer"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="saveCustomerButton" class="btn btn-primary">Save Changes</button>
	</div>
</div>
	
<div class="container main">

<div class="row">
<div class="span2">

	<hr>
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newCustomerButton" class="btn btn-primary btn-sidebar"><i class="icon-plus"></i>&nbsp; Add Customer</button>
	</p>
	<hr>
	
<?php $this->display('_SidebarCommon.tpl.php');?>


</div>

<div class="span10">

	<h1>
		<i class="icon-group"></i> Customers
		<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
		<span class='input-append pull-right searchContainer'>
			<input id='filter' type="text" placeholder="Search..." />
			<button class='btn add-on'><i class="icon-search"></i></button>
		</span>
	</h1>
	<div class="clearfix"></div>

	<!-- underscore template for the collection -->
	<script type="text/template" id="customerCollectionTemplate">
		<table class="collection table table-bordered">
		<thead>
			<tr>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Id">Id<# if (page.orderBy == 'Id') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
-->
				<th id="header_Name"><i class="icon-reorder"></i>&nbsp; Name<# if (page.orderBy == 'Name') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_ContactPerson"><i class="icon-user"></i>&nbsp; Contact Person<# if (page.orderBy == 'ContactPerson') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Tel"><i class="icon-phone"></i>&nbsp; Phone<# if (page.orderBy == 'Tel') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_StatusId"><i class="icon-info-sign"></i>&nbsp; Status<# if (page.orderBy == 'StatusId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
							
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Location"><i class="icon-reorder"></i>&nbsp; Location<# if (page.orderBy == 'Location') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Email"><i class="icon-envelope"></i>&nbsp; Email<# if (page.orderBy == 'Email') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Address"><i class="icon-envelope"></i>&nbsp; Address<# if (page.orderBy == 'Address') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Web"><i class="icon-cloud"></i>&nbsp; Web<# if (page.orderBy == 'Web') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Tel2"><i class="icon-phone"></i>&nbsp; Phone<# if (page.orderBy == 'Tel2') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Description"><i class="icon-reorder"></i>&nbsp; Description<# if (page.orderBy == 'Description') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
-->
			</tr>
		</thead>
		<tbody>
		<# items.each(function(item) { #>
			<tr id="<#= _.escape(item.get('id')) #>">

<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><#= _.escape(item.get('id') || '') #></td>
-->
				<td><#= _.escape(item.get('name') || '') #></td>
				<td><#= _.escape(item.get('contactPerson') || '') #></td>
				<td><#= _.escape(item.get('tel') || '') #></td>
				<td><#= _.escape(item.get('statusDescription') || '') #></td>
				
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><#= _.escape(item.get('location') || '') #></td>
				<td><#= _.escape(item.get('email') || '') #></td>
				<td><#= _.escape(item.get('address') || '') #></td>
				<td><#= _.escape(item.get('web') || '') #></td>
				<td><#= _.escape(item.get('tel2') || '') #></td>
				<td><#= _.escape(item.get('description') || '') #></td>
-->
			</tr>
		<# }); #>
		</tbody>
		</table>

		<#=  view.getPaginationHtml(page) #>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="customerModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group" style="display:none">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><#= _.escape(item.get('id') || '') #></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="nameInputContainer" class="control-group">
					<label class="control-label" for="name">Name</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="name" placeholder="Name" value="<#= _.escape(item.get('name') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="statusIdInputContainer" class="control-group">
					<label class="control-label" for="statusId">Status</label>
					<div class="controls inline-inputs">
						<select id="statusId" name="statusId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<hr>
				<div id="allowLoginInputContainer" class="control-group">
					<label class="control-label" for="allowLogin">Allow Login</label>
					<div class="controls inline-inputs">
						<div class="btn-group">
   							<button class="btn checkbox <#= _.escape((item.get('allowLogin') == 1 ? 'active' : '')) #>">Yes</button>
								<input style="display:none" type="checkbox" id="allowLogin" value="<#= _.escape((item.get('allowLogin') == 1 ? '1' : '0')) #>" <#= _.escape((item.get('allowLogin') == 1 ? 'checked="checked"' : '')) #>>
  							<button class="btn checkbox <#= _.escape((item.get('allowLogin') == 1 ? '' : 'active')) #>">No</button>
						</div>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="emailInputContainer" class="control-group">
					<label class="control-label" for="email">Email / Username</label>
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
				<hr>
				<div id="contactPersonInputContainer" class="control-group">
					<label class="control-label" for="contactPerson">Contact Person</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="contactPerson" placeholder="Contact Person" value="<#= _.escape(item.get('contactPerson') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="addressInputContainer" class="control-group">
					<label class="control-label" for="address">Address</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="address" placeholder="Address" value="<#= _.escape(item.get('address') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="locationInputContainer" class="control-group">
					<label class="control-label" for="location">Location</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="location" placeholder="Location" value="<#= _.escape(item.get('location') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="webInputContainer" class="control-group">
					<label class="control-label" for="web">Web</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="web" placeholder="Web" value="<#= _.escape(item.get('web') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="telInputContainer" class="control-group">
					<label class="control-label" for="tel">Phone</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="tel" placeholder="Tel" value="<#= _.escape(item.get('tel') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="tel2InputContainer" class="control-group">
					<label class="control-label" for="tel2">Phone2</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="tel2" placeholder="Tel2" value="<#= _.escape(item.get('tel2') || '') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="descriptionInputContainer" class="control-group">
					<label class="control-label" for="description">Description</label>
					<div class="controls inline-inputs">
						<textarea class="input-xlarge" id="description" rows="3"><#= _.escape(item.get('description') || '') #></textarea>
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteCustomerButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteCustomerButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Customer</button>
						<span id="confirmDeleteCustomerContainer" class="hide">
							<button id="cancelDeleteCustomerButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteCustomerButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>
	
	<div id="customerCollectionContainer" class="collectionContainer">
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
