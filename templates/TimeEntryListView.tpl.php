<?php
	$this->assign('title','TimeCase | TimeEntries');
	$this->assign('nav','timeentries');

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
	.script("scripts/app/timeentries.js").wait(function(){
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
<div class="modal hide fade" id="timeEntryDetailDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> Time Entry
			<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="modelAlert"></div>
		<div id="timeEntryModelContainer"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="forceTimeEntryStopButton" class="btn btn-warning" data-dismiss="modal" >Stop Timer</button>
		<button id="saveTimeEntryButton" class="btn btn-primary">Save Changes</button>
	</div>
</div>

<!-- modal set defaults dialog -->
<div class="modal hide fade" id="setDefaultsDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> Set Default Project
			<span id="secondModelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="secondModelAlert"></div>
		<div id="defaultsModelContainer">
		
		<form onsubmit="return false;" class="form-horizontal">
		<fieldset>
			<?php if(!($this->currentUser->LevelId & ($this->ROLE_CUSTOMER | $this->ROLE_BASIC_USER))):?>			
			<div id="filterCustomerIdInputContainer" class="control-group">
				<label class="control-label" for="filterCustomerId">Customer Filter</label>
				<div class="controls inline-inputs">
					<select id="filterCustomerId" name="filterCustomerId"></select>
					<span class="help-inline"></span>
				</div>
			</div>
			<?php endif;?>
			<div id="currentProjectIdInputContainer" class="control-group">
				<label class="control-label" for="currentProjectId">Project</label>
				<div id="parentProjectId" class="controls inline-inputs">
					<select id="currentProjectId" name="currentProjectId"></select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div id="currentCategoryIdInputContainer" class="control-group">
				<label class="control-label" for="currentCategoryId">Work Type</label>
				<div class="controls inline-inputs">
					<select id="currentCategoryId" name="currentCategoryId"></select>
					<span class="help-inline"></span>
				</div>
			</div>
		</fieldset>
		</form>
			
			
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="saveDefaultsButton" class="btn btn-primary">Save Defaults</button>
	</div>
</div>

<div class="container main">

<div class="row">
<div class="span2">

	<hr>
	
	<?php if(!($this->currentUser->LevelId & $this->ROLE_BASIC_USER)):?>
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newTimeEntryButton" class="btn btn-primary btn-sidebar"><i class="icon-plus"></i>&nbsp; Add Time Entry</button>
	</p>
	<?php endif;?>
	
	<p class="buttonContainer">
		<button id="setDefaultsButton" class="btn btn-primary btn-sidebar"><i class="icon-star"></i>&nbsp; Set Default Project</button>
	</p>
	
	<hr>

	<p class="buttonContainer">
	 <?php if ($this->useSessionTimer):?>
	 
		<?php if (isset($_SESSION['timetracking'])):?>
			<button id="timeTrackingButton" class="btn btn-warning btn-sidebar" data-state="started"><i class="icon-stop"></i>&nbsp; <span>Stop Timer</span></button>
		<?php else:?>
			<button id="timeTrackingButton" class="btn btn-success btn-sidebar" data-state="stopped"><i class="icon-play"></i>&nbsp; <span>Start Timer</span></button>
		<?php endif;?>
		
	 <?php else:?>
	 
	 	<?php if ($this->timer):?>
			<button id="timeTrackingButton" class="btn btn-warning btn-sidebar" data-state="started"><i class="icon-stop"></i>&nbsp; <span>Stop Timer</span></button>
		<?php else:?>
			<button id="timeTrackingButton" class="btn btn-success btn-sidebar" data-state="stopped"><i class="icon-play"></i>&nbsp; <span>Start Timer</span></button>
		<?php endif;?>
		
		
	 <?php endif;?>
	</p>
	
	<hr>
	
<?php $this->display('_SidebarCommon.tpl.php');?>


</div>

<div class="span10">

	<h1>
		<i class="icon-time"></i> Time Tracking
		<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
		<span class='input-append pull-right searchContainer'>
			<input id='filter' type="text" placeholder="Search..." />
			<button class='btn add-on'><i class="icon-search"></i></button>
		</span>
	</h1>
	<div class="clearfix"></div>

	<!-- underscore template for the collection -->
	<script type="text/template" id="timeEntryCollectionTemplate">
		<table class="collection table table-bordered">
		<thead>
			<tr>
				<!--
				<th id="header_Id">Id<# if (page.orderBy == 'Id') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				-->
				<th id="header_ProjectId"><i class="icon-briefcase"></i>&nbsp; Project<# if (page.orderBy == 'ProjectId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_UserId"><i class="icon-user"></i>&nbsp; User Name<# if (page.orderBy == 'UserId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_CategoryId"><i class="icon-reorder"></i>&nbsp; Work Type<# if (page.orderBy == 'CategoryId') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Description"><i class="icon-reorder"></i>&nbsp; Description<# if (page.orderBy == 'Description') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_Duration"><i class="icon-time"></i>&nbsp; Duration<# if (page.orderBy == 'Duration') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th><i class="icon-user"></i>&nbsp; Customer Name</th>
				<th id="header_Start"><i class="icon-reorder"></i>&nbsp; Start<# if (page.orderBy == 'Start') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
				<th id="header_End"><i class="icon-reorder"></i>&nbsp; End<# if (page.orderBy == 'End') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
-->
			</tr>
		</thead>
		<tbody>
		<# items.each(function(item) { #>
			<tr id="<#= _.escape(item.get('id')) #>">
				<!--
				<td><#= _.escape(item.get('id') || '') #></td>
				-->
				<td><#= _.escape(item.get('projectTitle') || '') #></td>
				<td><#= _.escape(item.get('userName') || '') #></td>
				<td><#= _.escape(item.get('categoryName') || '') #></td>
				<td><#= _.escape(item.get('description') || '') #></td>
				<td class="rtext"><#= _.escape(item.get('durationFormatted') || '') #></td>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><#= _.escape(item.get('customerName') || '') #></td>
				<td><#if (item.get('start')) { #><#= _date(app.parseDate(item.get('start'))).format('MMM D, YYYY H:mm') #><# } else { #>NULL<# } #></td>
				<td><#if (item.get('end')) { #><#= _date(app.parseDate(item.get('end'))).format('MMM D, YYYY H:mm') #><# } else { #>NULL<# } #></td>
-->
			</tr>
		<# }); #>
		</tbody>
		</table>

		<#=  view.getPaginationHtml(page) #>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="timeEntryModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group" style="display:none">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><#= _.escape(item.get('id') || '') #></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="startInputContainer" class="control-group">
					<label class="control-label" for="start">Start</label>
					<div class="controls inline-inputs">
					 <div class="timespan">
						<input type="text" class="date-picker input-xlarge" id="start" value="<#= _date(app.parseDate(item.get('start'))).format('YY-MM-DD') #>"> 
						<input type="text" class="time-picker input-xlarge" id="start-time" value="<#= _date(app.parseDate(item.get('start'))).format('H:mm') #>">
						<span class="quick-times">
							<span class="start-btns">
							&nbsp;<i class="icon-sort-up ctrl"></i>&nbsp;
							 <div class="btn-group">
              			 	  <button class="btn ctrl" data-min="-60" data-obj="start" tabindex="-1">1h</button>
              			 	  <button class="btn ctrl" data-min="-10" data-obj="start" tabindex="-1">10</button>
              			 	  <button class="btn ctrl" data-min="-15" data-obj="start" tabindex="-1">15</button>
            				 </div>
							 <span class="duration">
							 	<span class="label label-info" unselectable="on">00:00</span>
							 	&nbsp;<i class="icon-repeat restart"></i>
							 </span>
							</span>
						</span>
						<span class="help-inline"></span>
					 </div>
					</div>
				</div>
				<div id="endInputContainer" class="control-group">
					<label class="control-label" for="end">End</label>
					<div class="controls inline-inputs">
					 <div class="timespan">
						<input type="text" class="date-picker input-xlarge" id="end" value="<#= _date(app.parseDate(item.get('end'))).format('YY-MM-DD') #>">
						<input type="text" class="time-picker input-xlarge" id="end-time" value="<#= _date(app.parseDate(item.get('end'))).format('H:mm') #>">
						<span class="quick-times">
							&nbsp;<i class="icon-sort-down ctrl"></i>&nbsp;
							 <div class="btn-group">
              			 	  <button class="btn ctrl" data-min="60" data-obj="end" tabindex="-1">1h</button>
              			 	  <button class="btn ctrl" data-min="10" data-obj="end" tabindex="-1">10</button>
              			 	  <button class="btn ctrl" data-min="15" data-obj="end" tabindex="-1">15</button>
            				 </div>
						</span>
						<span class="help-inline"></span>
					 </div>
					</div>
				</div>
				<hr class="break-hr timer"/>
				<div id="userIdInputContainer" class="control-group">
					<label class="control-label" for="userId">User</label>
					<div class="controls inline-inputs">
						<select id="userId" name="userId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="filterCustomerIdTEInputContainer" class="control-group">
					<label class="control-label" for="filterCustomerIdTE">Customer Filter</label>
					<div class="controls inline-inputs">
						<select id="filterCustomerIdTE" name="filterCustomerIdTE"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="projectIdInputContainer" class="control-group">
					<label class="control-label" for="projectId">Project</label>
					<div id="parentProjectIdTE" class="controls inline-inputs">
						<select id="projectId" name="projectId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="categoryIdInputContainer" class="control-group">
					<label class="control-label" for="categoryId">Work Type</label>
					<div class="controls inline-inputs">
						<select id="categoryId" name="categoryId"></select>
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
		<form id="deleteTimeEntryButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteTimeEntryButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete TimeEntry</button>
						<span id="confirmDeleteTimeEntryContainer" class="hide">
							<button id="cancelDeleteTimeEntryButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteTimeEntryButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<div id="timeEntryCollectionContainer" class="collectionContainer">
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
