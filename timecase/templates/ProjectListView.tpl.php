<?php
	$this->assign('title','TimeCase | Projects');
	$this->assign('nav','projects');

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
	.script("scripts/app/projects.js").wait(function(){
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
<div class="modal hide fade" id="projectDetailDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> Project
			<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="modelAlert"></div>
		<div id="projectModelContainer"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="saveProjectButton" class="btn btn-primary">Save Changes</button>
	</div>
</div>
	
<div class="container main">

<div class="row">
<div class="span2">

	<hr>
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newProjectButton" class="btn btn-primary btn-sidebar"><i class="icon-plus"></i>&nbsp; Add Project</button>
	</p>
	<hr>
	
<?php $this->display('_SidebarCommon.tpl.php');?>


</div>

<div class="span10">

	<h1>
		<i class="icon-briefcase"></i> Projects
		<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
		<span class='input-append pull-right searchContainer'>
			<input id='filter' type="text" placeholder="Search..." />
			<button class='btn add-on'><i class="icon-search"></i></button>
		</span>
	</h1>
	<div class="clearfix"></div>

	<!-- underscore template for the collection -->
	<script type="text/template" id="projectCollectionTemplate">
		<table class="collection table table-bordered">
		<thead>
			<tr>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Id">Id<# if (page.orderBy == 'Id') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
-->
				<th id="header_Title"><i class="icon-reorder"></i>&nbsp; Title<# if (page.orderBy == 'Title') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_CustomerId"><i class="icon-group"></i>&nbsp; Customer<# if (page.orderBy == 'CustomerId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Deadline"><i class="icon-warning-sign"></i>&nbsp; Deadline Date<# if (page.orderBy == 'Deadline') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th><i class="icon-warning-sign"></i>&nbsp; Deadline<# if (page.orderBy == 'DeadlineApproach') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Progress"><i class="icon-signal"></i>&nbsp; Progress<# if (page.orderBy == 'Progress') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_StatusId"><i class="icon-info-sign"></i>&nbsp; Status<# if (page.orderBy == 'StatusId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Created"><i class="icon-reorder"></i>&nbsp; Created<# if (page.orderBy == 'Created') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Closed"><i class="icon-reorder"></i>&nbsp; Closed<# if (page.orderBy == 'Closed') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
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
				<td><#= _.escape(item.get('title') || '') #></td>
				<td><#= _.escape(item.get('customerName') || '') #></td>
				<td><#if (item.get('deadline')) { #><#= _date(app.parseDate(item.get('deadline'))).format('MMM D, YYYY H:mm') #><# } else { #>NULL<# } #></td>
				<td><div class="progress progress-striped"><div class="bar" style="width: <#= _.escape(item.get('deadlineApproach') || '') #>%;"></div></div></td>
				<td><div class="progress progress-striped"><div class="bar" style="width: <#= _.escape(item.get('progress') || '') #>%;"></div></div></td>
				<td><#= _.escape(item.get('statusDescription') || '') #></td>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><#if (item.get('created')) { #><#= _date(app.parseDate(item.get('created'))).format('MMM D, YYYY H:mm') #><# } else { #>NULL<# } #></td>
				<td><#if (item.get('closed')) { #><#= _date(app.parseDate(item.get('closed'))).format('MMM D, YYYY H:mm') #><# } else { #>NULL<# } #></td>
				<td><#= _.escape(item.get('description') || '') #></td>
-->
			</tr>
		<# }); #>
		</tbody>
		</table>

		<#=  view.getPaginationHtml(page) #>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="projectModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group" style="display:none">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><#= _.escape(item.get('id') || '') #></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="titleInputContainer" class="control-group">
					<label class="control-label" for="title">Title</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="title" placeholder="Title" value="<#= _.escape(item.get('title') || '') #>">
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
				<div id="customerIdInputContainer" class="control-group">
					<label class="control-label" for="customerId">Customer</label>
					<div class="controls inline-inputs">
						<select id="customerId" name="customerId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="createdInputContainer" class="control-group">
					<label class="control-label" for="created">Created</label>
					<div class="controls inline-inputs">
						<input type="text" class="date-picker input-xlarge" id="created" value="<#= _date(app.parseDate(item.get('created'))).format('YYYY-MM-DD') #>">
						<input type="text" class="time-picker input-xlarge" id="created-time" value="<#= _date(app.parseDate(item.get('created'))).format('H:mm') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="deadlineInputContainer" class="control-group">
					<label class="control-label" for="deadline">Deadline</label>
					<div class="controls inline-inputs">
						<input type="text" class="date-picker input-xlarge" id="deadline" value="<#= _date(app.parseDate(item.get('deadline'))).format('YYYY-MM-DD') #>">
						<input type="text" class="time-picker input-xlarge" id="deadline-time" value="<#= _date(app.parseDate(item.get('deadline'))).format('H:mm') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="closedInputContainer" class="control-group" style="display:none"> <!-- additional date disabled -->
					<label class="control-label" for="closed">Closed / Payment</label>
					<div class="controls inline-inputs">
						<input type="text" class="date-picker input-xlarge" id="closed" value="<#= _date(app.parseDate(item.get('closed'))).format('YYYY-MM-DD') #>">
						<input type="text" class="time-picker input-xlarge" id="closed-time" value="<#= _date(app.parseDate(item.get('closed'))).format('H:mm') #>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="progressInputContainer" class="control-group">
					<label class="control-label" for="progress">Progress %</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-mini" id="progress" placeholder="Progress" value="<#= _.escape(item.get('progress') || '') #>">
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
		<form id="deleteProjectButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteProjectButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Project</button>
						<span id="confirmDeleteProjectContainer" class="hide">
							<button id="cancelDeleteProjectButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteProjectButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>
	
	<div id="projectCollectionContainer" class="collectionContainer">
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
