<?php
	$this->assign('title','Categories');
	$this->assign('nav','categories');

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
	.script("scripts/app/categories.js").wait(function(){
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
<div class="modal hide fade" id="categoryDetailDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>
			<i class="icon-edit"></i> Category
			<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
		</h3>
	</div>
	<div class="modal-body">
		<div id="modelAlert"></div>
		<div id="categoryModelContainer"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" >Cancel</button>
		<button id="saveCategoryButton" class="btn btn-primary">Save Changes</button>
	</div>
</div>

<div class="container main">

<div class="row">
<div class="span2">

	<hr>
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newCategoryButton" class="btn btn-primary btn-sidebar"><i class="icon-plus"></i>&nbsp; Add Work Type</button>
	</p>
	<hr>
	
<?php $this->display('_SidebarCommon.tpl.php');?>


</div>

<div class="span10">

	<h1>
		<i class="icon-reorder"></i> Work Types
		<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
		<span class='input-append pull-right searchContainer'>
			<input id='filter' type="text" placeholder="Search..." />
			<button class='btn add-on'><i class="icon-search"></i></button>
		</span>
	</h1>
	<div class="clearfix"></div>

	<!-- underscore template for the collection -->
	<script type="text/template" id="categoryCollectionTemplate">
		<table class="collection table table-bordered">
		<thead>
			<tr>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Id"><i class="icon-reorder"></i>&nbsp; Id<# if (page.orderBy == 'Id') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
-->
				<th id="header_Name"><i class="icon-reorder"></i>&nbsp; Name<# if (page.orderBy == 'Name') { #> <i class='icon-arrow-<#= page.orderDesc ? 'up' : 'down' #>' /><# } #></th>
			</tr>
		</thead>
		<tbody>
		<# items.each(function(item) { #>
			<tr id="<#= _.escape(item.get('id')) #>">
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><#= _.escape(item.get('id') || '') #></td>
-->
				<td><#= _.escape(item.get('name') || '') #></td>
			</tr>
		<# }); #>
		</tbody>
		</table>

		<#=  view.getPaginationHtml(page) #>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="categoryModelTemplate">
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
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteCategoryButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteCategoryButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Work Type</button>
						<span id="confirmDeleteCategoryContainer" class="hide">
							<button id="cancelDeleteCategoryButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteCategoryButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<div id="categoryCollectionContainer" class="collectionContainer">
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
