/**
 * View logic for TimeEntries
 */

/**
 * application logic specific to the TimeEntry listing page
 */


function getDateFromInput(dateSelector, timeSelector){
	
	var date_value = $(dateSelector).val();
	var time_value = $(timeSelector).val();
	
	if (date_value === undefined || time_value === undefined) return new Date();
	
	var arr = date_value.split("-");
	var year = arr[0];
	var month = arr[1];
	var day = arr[2];
	
	var arr = time_value.split(":");
	var hours = arr[0];
	var minutes = arr[1];
	
	var d = new Date();
	
	if(!(!isNaN(parseFloat(year)) && isFinite(year))) year = d.getFullYear(); else year = year - 0 + 2000;
	if(!(!isNaN(parseFloat(month)) && isFinite(month))) month = d.getMonth() + 1;
	if(!(!isNaN(parseFloat(day)) && isFinite(day))) day = d.getDate();
	if(!(!isNaN(parseFloat(hours)) && isFinite(hours))) hours = 0; //d.getHours();
	if(!(!isNaN(parseFloat(minutes)) && isFinite(minutes))) minutes = 0; //d.getMinutes();
	
	var date_return = new Date(year, month - 1, day, hours, minutes, 0);
	
	return date_return;	
}


function startTimer(){

	var btn = $("#timeTrackingButton");
	
	btn.attr('data-state', 'started');
	btn.find('i').removeClass("icon-play");
	btn.find('i').addClass("icon-stop");
	btn.removeClass("btn-success");
	btn.addClass("btn-warning");
	
	btn.find('span').html('Stop Timer');
	
	g_time_start = Math.round(new Date().getTime() / 1000);
	
	$("#currentTimetrack").html('');
	
	setTimeout("app.appendAlert('Timer started','alert-info',3000,'collectionAlert')",500);
	
}

function stopTimer(){

	// stop timer
	$.get('api/stoptimetracking', function(data) {
		if (data >= 0){
						
			var btn = $("#timeTrackingButton");
			
			btn.find('i').removeClass("icon-stop");
			btn.find('i').addClass("icon-play");
			btn.removeClass("btn-warning");
			btn.addClass("btn-success");
			btn.attr('data-state', 'stopped');
			btn.find('span').html('Start Timer');

			$("#currentTimetrack").html('');
			
			g_time_start = -1;
			
			// clear this
			page.isStopButtonClicked = false;
			
			setTimeout("app.appendAlert('Timer stopped','alert-info',3000,'collectionAlert')",500);
			
		}else{
			location.reload(true); // refresh page on error
		}
	});
	
}


function refreshInput(dateSelector, timeSelector, reset){
	
	var date = getDateFromInput(dateSelector, timeSelector);
	
	if (reset) date = new Date();
	
	var val = {
			h: date.getHours(),
			i: date.getMinutes(),
			d: date.getDate(),
			m: date.getMonth() + 1,
			yy: date.getFullYear().toString().substring(2)
		};
		val.hh = (val.h < 10 ? '0' : '') + val.h;
		val.ii = (val.i < 10 ? '0' : '') + val.i;
		val.dd = (val.d < 10 ? '0' : '') + val.d;
		val.mm = (val.m < 10 ? '0' : '') + val.m;
		
	$(dateSelector).val(val.yy + '-' + val.mm + '-' + val.dd);
	$(timeSelector).val(val.hh + ':' + val.ii);
	
	return;
}

function updateDurationField(){

	var negative = '';
	
	refreshInput('input#start', 'input#start-time');
	refreshInput('input#end', 'input#end-time');
	
	var date_start = getDateFromInput('input#start', 'input#start-time');
	var date_end = getDateFromInput('input#end', 'input#end-time');
	
	var elapsedMinutes = (date_end - date_start) / 1000 / 60;

    if (elapsedMinutes < 0){
    	negative = '-';
    	elapsedMinutes = elapsedMinutes * -1;

    	$('.quick-times .duration span').removeClass('label-info');
    	$('.quick-times .duration span').addClass('label-important');
    }else{
    	$('.quick-times .duration span').removeClass('label-important');
    	$('.quick-times .duration span').addClass('label-info');
    }
        
	var hours = Math.floor(elapsedMinutes / 60);          
    var minutes = elapsedMinutes % 60;
    
    hours = (hours < 10 ? '0' : '') + hours;
    minutes = (minutes < 10 ? '0' : '') + minutes;
    
	$('.quick-times .duration span').html(negative + hours + ':' + minutes);
	
}


var page = {

	timeEntries: new model.TimeEntryCollection(),
	collectionView: null,
	timeEntry: null,
	modelView: null,
	isInitialized: false,
	isInitializing: false,

	fetchParams: { filter: '', orderBy: '', orderDesc: '', page: 1 },
	fetchInProgress: false,
	dialogIsOpen: false,
	isStopButtonClicked: false,

	/**
	 *
	 */
	init: function()
	{
		// ensure initialization only occurs once
		if (page.isInitialized || page.isInitializing) return;
		page.isInitializing = true;
		
		if (!$.isReady && console) console.warn('page was initialized before dom is ready.  views may not render properly.');
		
		// start/stop timer button
		$("#timeTrackingButton").click(function(e) {
			e.preventDefault();
			
			var btn = $(this);
			var state = $(this).attr('data-state');
			
			if (state == 'stopped'){
				
				// start timer
				$.get('api/starttimetracking', function(data) {
					if (data == 'started'){
						
						startTimer();

					}else{
						setTimeout("app.appendAlert('Timer error','alert-error',3000,'collectionAlert')",500);
					}
				});
				
			}else{
				page.showDetailDialog(false, true);
				// we need a force stop button now
				$('#forceTimeEntryStopButton').show();
				page.isStopButtonClicked = true;
			}
		});
		
		// make the new button clickable
		$("#newTimeEntryButton").click(function(e) {
			e.preventDefault();
			page.showDetailDialog();
		});
		
		// force timer to stop button
		$("#forceTimeEntryStopButton").click(function(e) {
			stopTimer();
		});
		
		// make the defaults button clickable
		$("#setDefaultsButton").click(function(e) {
			
			// reset any previous errors
			$('#secondModelAlert').html('');
			$('.control-group').removeClass('error');
			$('.help-inline').html('');
			
			$('#setDefaultsDialog').modal({ show: true });
			
			page.timeEntry = new model.TimeEntryModel();
			page.modelView.model = page.timeEntry;
			page.modelView.render();

			// get current defaults
			$.get('api/getcurrentuser', function(data) {
				
				var currentProject = data.CurrentProject;
				var currentCategory = data.CurrentCategory;

				app.hideProgress('secondModelLoader');
				
				
				// populate the dropdown options for customer filer
				var filterCustomerIdValues = new model.CustomerCollection();
				filterCustomerIdValues.fetch({
					success: function(c){
						var dd = $('#filterCustomerId');
						dd.append('<option value=""></option>');
						c.forEach(function(item,index)
						{
							dd.append(app.getOptionHtml(
								item.get('id'),
								item.get('name'), // TODO: change fieldname if the dropdown doesn't show the desired column
								false
							));
						});
						
						if (!app.browserSucks())
						{
							dd.combobox();
							$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
						}
	
					},
					error: function(collection,response,scope){
						app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
					}
				});
				
				// customer changed action
				$("#filterCustomerIdInputContainer input, #filterCustomerIdInputContainer select").change(function(e) {
					e.preventDefault();
					app.showProgress('modelLoader');

					
					// on customer change update projects combo so it displays only related projects
					var customerId = $(this).val();
					
					// reset combo select for projectId
					$('#parentProjectId select option').remove();
					$('#parentProjectId ul li').remove();
						
					// populate new dropdown options for projectId based on customerId
					var projectIdValues = new model.ProjectCollection();
					projectIdValues.fetch({
						success: function(c){
							
							$('#currentProjectId *').remove();
							var dd = $('#currentProjectId');							
							dd.append('<option value=""></option>');
							c.forEach(function(item,index)
							{
								// add only projects related to this customer or all in blank
								if (customerId == '' || item.get('customerId') == customerId){
									dd.append(app.getOptionHtml(
											item.get('id'),
											item.get('title'), // TODO: change fieldname if the dropdown doesn't show the desired column
											false // no defaults
										));
								}	
									
							});
							
							if (!app.browserSucks())
							{
								// refresh bootstrap combo
								dd.data('combobox').refresh()
								$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
							}
							
							app.hideProgress('modelLoader');
							return true;
							

						},
						error: function(collection,response,scope){
							app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
							return false;
						}
					});
						
					app.hideProgress('modelLoader');
				});
				
				
				
				// populate the dropdown options for default projectId
				// TODO: load only the selected value, then fetch all options when the drop-down is clicked
				var projectIdValues = new model.ProjectActiveOnlyCollection();
				projectIdValues.fetch({
					success: function(c){
						
						var dd = $('#currentProjectId');
						dd.append('<option value=""></option>');
						c.forEach(function(item,index)
						{
							dd.append(app.getOptionHtml(
								item.get('id'),
								item.get('title'), // TODO: change fieldname if the dropdown doesn't show the desired column
								currentProject == item.get('id')
							));
						});
						
						if (!app.browserSucks())
						{
							dd.combobox();
							$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
						}
	
					},
					error: function(collection,response,scope){
						app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
					}
				});
				
				// populate the dropdown options for default categoryId
				// TODO: load only the selected value, then fetch all options when the drop-down is clicked
				var categoryIdValues = new model.CategoryCollection();
				categoryIdValues.fetch({
					success: function(c){
						var dd = $('#currentCategoryId');
						dd.append('<option value=""></option>');
						c.forEach(function(item,index)
						{
							dd.append(app.getOptionHtml(
								item.get('id'),
								item.get('name'), // TODO: change fieldname if the dropdown doesn't show the desired column
								currentCategory == item.get('id')
							));
						});
						
						if (!app.browserSucks())
						{
							dd.combobox();
							$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
						}
	
					},
					error: function(collection,response,scope){
						app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
					}
				});
			
			});
		});
		
		// save the model when the save button is clicked
		$("#saveDefaultsButton").click(function(e) {
			e.preventDefault();
			page.updateDefaults();
		});

		// let the page know when the dialog is open
		$('#timeEntryDetailDialog').on('show',function(){
			page.dialogIsOpen = true;
		});

		// when the model dialog is closed, let page know and reset the model view
		$('#timeEntryDetailDialog').on('hidden',function(){
			$('#modelAlert').html('');
			page.dialogIsOpen = false;
		});

		// save the model when the save button is clicked
		$("#saveTimeEntryButton").click(function(e) {
			e.preventDefault();
			page.updateModel();
		});
		
		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#timeEntryCollectionContainer"),
			templateEl: $("#timeEntryCollectionTemplate"),
			collection: page.timeEntries
		});

		// initialize the search filter
		$('#filter').change(function(obj){
			page.fetchParams.filter = $('#filter').val();
			page.fetchParams.page = 1;
			page.fetchTimeEntries(page.fetchParams);
		});
		
		// make the rows clickable ('rendered' is a custom event, not a standard backbone event)
		this.collectionView.on('rendered',function(){

			// attach click handler to the table rows for editing
			$('table.collection tbody tr').click(function(e) {
				
				e.preventDefault();
				var m = page.timeEntries.get(this.id);
				page.showDetailDialog(m);
			});


			// make the headers clickable for sorting
 			$('table.collection thead tr th').click(function(e) {
 				
 				if (this.id == 'header_Duration') return;
 				
 				e.preventDefault();
				var prop = this.id.replace('header_','');

				// toggle the ascending/descending before we change the sort prop
				page.fetchParams.orderDesc = (prop == page.fetchParams.orderBy && !page.fetchParams.orderDesc) ? '1' : '';
				page.fetchParams.orderBy = prop;
				page.fetchParams.page = 1;
 				page.fetchTimeEntries(page.fetchParams);
 			});

			// attach click handlers to the pagination controls
			$('.pageButton').click(function(e) {
				e.preventDefault();
				page.fetchParams.page = this.id.substr(5);
				page.fetchTimeEntries(page.fetchParams);
			});
			
			page.isInitialized = true;
			page.isInitializing = false;
			
		});

		// backbone docs recommend bootstrapping data on initial page load, but we live by our own rules!
		this.fetchTimeEntries({ page: 1 });

		// initialize the model view
		this.modelView = new view.ModelView({
			el: $("#timeEntryModelContainer")
		});

		// tell the model view where it's template is located
		this.modelView.templateEl = $("#timeEntryModelTemplate");

		if (model.longPollDuration > 0)
		{
			setInterval(function () {

				if (!page.dialogIsOpen)
				{
					page.fetchTimeEntries(page.fetchParams,true);
				}

			}, model.longPollDuration);
		}
	},


	/**
	 * Fetch the collection data from the server
	 * @param object params passed through to collection.fetch
	 * @param bool true to hide the loading animation
	 */
	fetchTimeEntries: function(params, hideLoader)
	{
		// persist the params so that paging/sorting/filtering will play together nicely
		page.fetchParams = params;

		if (page.fetchInProgress)
		{
			if (window.console) console.log('supressing fetch because it is already in progress');
		}

		page.fetchInProgress = true;

		if (!hideLoader) app.showProgress('loader');;

		page.timeEntries.fetch({

			data: params,

			success: function() {

				if (page.timeEntries.collectionHasChanged)
				{
					// data returned from the server.  render the collection view
					page.collectionView.render();
				}

				app.hideProgress('loader');
				page.fetchInProgress = false;
			},

			error: function(m, r) {
				app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'collectionAlert');
				app.hideProgress('loader');
				page.fetchInProgress = false;
			}

		});
	},

	/**
	 * show the dialog for editing a model
	 * @param model
	 */
	showDetailDialog: function(m, stop_timer) {

		// always hide force stop button
		$('#forceTimeEntryStopButton').hide();
		
		// show the modal dialog
		$('#timeEntryDetailDialog').modal({ show: true });
		
		// if a model was specified then that means a user is editing an existing record
		// if not, then the user is creating a new record
		page.timeEntry = m ? m : new model.TimeEntryModel();

		page.modelView.model = page.timeEntry;

		if (page.timeEntry.id == null || page.timeEntry.id == '')
		{
			// this is new entry, get current userid as default and render model view
			$.get('api/getcurrentuser', function(data) {
				
				page.timeEntry.set('userId', data.UserId);
				page.timeEntry.set('projectId', data.CurrentProject);
				page.timeEntry.set('categoryId', data.CurrentCategory);
								
				// set tracking time on stop_timer
				if (stop_timer === true){
					var d = new Date();
					var start_point = new Date(d.getTime() - g_time_elapsed * 1000);
					page.timeEntry.set('start', start_point);
				}

		    	// this is a new record, there is no need to contact the server
				page.renderModelView(false);
				
				updateDurationField();
			});
			

		}
		else
		{
			app.showProgress('modelLoader');

			// fetch the model from the server so we are not updating stale data
			page.timeEntry.fetch({

				success: function() {
					// data returned from the server.  render the model view
					page.renderModelView(true);
					
					updateDurationField();
				},

				error: function(m, r) {
					app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'modelAlert');
					app.hideProgress('modelLoader');
				}

			});
		}

	},

	/**
	 * Render the model template in the popup
	 * @param bool show the delete button
	 */
	renderModelView: function(isExistingRecord)
	{
		
		// callback just before showing html at render
		function callbackBeforeDisplay(html){
			
			// hide some fields for basic users
			if (g_level_id == 16){		
				
				isExistingRecord = false; // this will kill delete button
				html = $(html).find('#startInputContainer').remove().end();
				html = $(html).find('#endInputContainer').remove().end();
				html = $(html).find('.break-hr.timer').remove().end();
			}
			
			// remove user field if not admin or manager
			if (g_level_id != 1 && g_level_id != 2){
				html = $(html).find('#userIdInputContainer').remove().end();
			}
			
			// hide some fields for if not admin or manager or regular user
			if (g_level_id != 1 && g_level_id != 2 && g_level_id != 4){
				html = $(html).find('#filterCustomerIdTEInputContainer').remove().end();
			}
			
			
			return html;
		}

		page.modelView.render(callbackBeforeDisplay);
		
		app.hideProgress('modelLoader');

		// initialize any special controls (late modal binding)
		try {
			
			$('.date-picker')
				.datepicker({ format: 'yy-mm-dd' })
				.on('changeDate', function(ev){
					$('.date-picker').datepicker('hide');
					
					updateDurationField();
				});
			
			
			// update duration on time change
			$(".date-picker, .time-picker").change(function(e) {
				updateDurationField();
			});
			
			
			// up/down arrows
			$(".quick-times i.ctrl").click(function(e) {
				$(this).toggleClass("icon-sort-up icon-sort-down");
				
				var btns = $(this).siblings(".btn-group").find("button[data-min]");
				
				btns.each(function() {
					var value = $(this).attr("data-min");
					$(this).attr("data-min", value * -1);
				});
			});
			
			
			// restart
			$(".quick-times i.restart").click(function(e) {
				refreshInput('input#start', 'input#start-time', true);
				refreshInput('input#end', 'input#end-time', true);
				updateDurationField();
			});
			

			// quick times
			$(".quick-times button").click(function(e) {

				var subtract_minutes = $(this).attr('data-min');
				var input_object = $(this).attr('data-obj');
				
				var date = getDateFromInput('input#' + input_object, 'input#' + input_object + '-time');
				date.setMinutes(date.getMinutes() + (subtract_minutes - 0));
				
				var val = {
						h: date.getHours(),
						i: date.getMinutes(),
						d: date.getDate(),
						m: date.getMonth() + 1,
						yy: date.getFullYear().toString().substring(2)
					};
					val.hh = (val.h < 10 ? '0' : '') + val.h;
					val.ii = (val.i < 10 ? '0' : '') + val.i;
					val.dd = (val.d < 10 ? '0' : '') + val.d;
					val.mm = (val.m < 10 ? '0' : '') + val.m;
					
				$('input#' + input_object).val(val.yy + '-' + val.mm + '-' + val.dd);
				$('input#' + input_object + '-time').val(val.hh + ':' + val.ii);
				
				updateDurationField();
			});
			
		} catch (error) {
			// this happens if the datepicker input.value isn't a valid date
			if (window.console) console.log('datepicker error: '+error.message);
		}

		// populate the dropdown options for customer filer
		if (g_level_id == 1 || g_level_id == 2 || g_level_id == 4){
			var filterCustomerIdValues = new model.CustomerCollection();
			filterCustomerIdValues.fetch({
				success: function(c){
					var dd = $('#filterCustomerIdTE');
					dd.append('<option value=""></option>');
					c.forEach(function(item,index)
					{
						dd.append(app.getOptionHtml(
							item.get('id'),
							item.get('name'), // TODO: change fieldname if the dropdown doesn't show the desired column
							false
						));
					});
					
					if (!app.browserSucks())
					{
						dd.combobox();
						$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
					}
	
				},
				error: function(collection,response,scope){
					app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
				}
			});
		}
		
		// customer changed action
		$("#filterCustomerIdTEInputContainer input, #filterCustomerIdTEInputContainer select").change(function(e) {
				e.preventDefault();
				app.showProgress('modelLoader');
				
				// on customer change update projects combo so it displays only related projects
				var customerId = $(this).val();
				
				// reset combo select for projectId
				$('#parentProjectIdTE select option').remove();
				$('#parentProjectIdTE ul li').remove();
					
				// populate new dropdown options for projectId based on customerId
				var projectIdValues = new model.ProjectCollection();
				projectIdValues.fetch({
					success: function(c){
						
						$('#projectId *').remove();
						var dd = $('#projectId');							
						dd.append('<option value=""></option>');
						c.forEach(function(item,index)
						{
							// add only projects related to this customer or all in blank
							if (customerId == '' || item.get('customerId') == customerId){
								dd.append(app.getOptionHtml(
										item.get('id'),
										item.get('title'), // TODO: change fieldname if the dropdown doesn't show the desired column
										false // no defaults
									));
							}	
								
						});
						
						if (!app.browserSucks())
						{
							// refresh bootstrap combo
							dd.data('combobox').refresh()
							$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
						}
						
						app.hideProgress('modelLoader');
						return true;
						

					},
					error: function(collection,response,scope){
						app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
						return false;
					}
				});
					
				app.hideProgress('modelLoader');
		});
		
		// populate the dropdown options for projectId
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		if (isExistingRecord)
			var projectIdValues = new model.ProjectCollection();
		else
			var projectIdValues = new model.ProjectActiveOnlyCollection();
		projectIdValues.fetch({
			success: function(c){
				var dd = $('#projectId');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index)
				{
					dd.append(app.getOptionHtml(
						item.get('id'),
						item.get('title'), // TODO: change fieldname if the dropdown doesn't show the desired column
						page.timeEntry.get('projectId') == item.get('id')
					));
				});
				
				if (!app.browserSucks())
				{
					dd.combobox();
					$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
				}

			},
			error: function(collection,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
			}
		});

		// populate the dropdown options for userId
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var userIdValues = new model.UserCollection();
		userIdValues.fetch({
			success: function(c){
				var dd = $('#userId');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index)
				{
					dd.append(app.getOptionHtml(
						item.get('id'),
						item.get('username'), // TODO: change fieldname if the dropdown doesn't show the desired column
						page.timeEntry.get('userId') == item.get('id')
					));
				});
				
				if (!app.browserSucks())
				{
					dd.combobox();
					$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
				}

			},
			error: function(collection,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
			}
		});

		// populate the dropdown options for categoryId
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var categoryIdValues = new model.CategoryCollection();
		categoryIdValues.fetch({
			success: function(c){
				var dd = $('#categoryId');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index)
				{
					dd.append(app.getOptionHtml(
						item.get('id'),
						item.get('name'), // TODO: change fieldname if the dropdown doesn't show the desired column
						page.timeEntry.get('categoryId') == item.get('id')
					));
				});
				
				if (!app.browserSucks())
				{
					dd.combobox();
					$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
				}

			},
			error: function(collection,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
			}
		});


		if (isExistingRecord)
		{
			// attach click handlers to the delete buttons

			$('#deleteTimeEntryButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteTimeEntryContainer').show('fast');
			});

			$('#cancelDeleteTimeEntryButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteTimeEntryContainer').hide('fast');
			});

			$('#confirmDeleteTimeEntryButton').click(function(e) {
				e.preventDefault();
				page.deleteModel();
			});

		}
		else
		{
			// no point in initializing the click handlers if we don't show the button
			$('#deleteTimeEntryButtonContainer').hide();
		}
		
	},

	/**
	 * update the model that is currently displayed in the dialog
	 */
	updateModel: function()
	{
		// reset any previous errors
		$('#modelAlert').html('');
		$('.control-group').removeClass('error');
		$('.help-inline').html('');

		// if this is new then on success we need to add it to the collection
		var isNew = page.timeEntry.isNew();

		app.showProgress('modelLoader');

		page.timeEntry.save({

			'projectId': $('select#projectId').val(),
			'userId': $('select#userId').val(),
			'categoryId': $('select#categoryId').val(),
			'start': $('input#start').val()+' '+$('input#start-time').val(),
			'end': $('input#end').val()+' '+$('input#end-time').val(),
			'description': $('textarea#description').val()
		}, {
			wait: true,
			success: function(){
				$('#timeEntryDetailDialog').modal('hide');
				setTimeout("app.appendAlert('TimeEntry was sucessfully " + (isNew ? "inserted" : "updated") + "','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				// if the collection was initally new then we need to add it to the collection now
				if (isNew) { page.timeEntries.add(page.timeEntry) }

				if (model.reloadCollectionOnModelUpdate)
				{
					// re-fetch and render the collection after the model has been updated
					page.fetchTimeEntries(page.fetchParams,true);
				}

				if (page.isStopButtonClicked === true){
					stopTimer();
				}
				
				
		},
			error: function(model,response,scope){

				app.hideProgress('modelLoader');

				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');

				try {
					var json = $.parseJSON(response.responseText);

					if (json.errors)
					{
						$.each(json.errors, function(key, value) {
							$('#'+key+'InputContainer').addClass('error');
							$('#'+key+'InputContainer span.help-inline').html(value);
							$('#'+key+'InputContainer span.help-inline').show();
						});
					}
				} catch (e2) {
					if (window.console) console.log('error parsing server response: '+e2.message);
				}
			}
		});
	},

	
	/**
	 * update defaults
	 */
	updateDefaults: function()
	{
		// reset any previous errors
		$('#secondModelAlert').html('');
		$('.control-group').removeClass('error');
		$('.help-inline').html('');

		app.showProgress('secondModelLoader');

		page.user = new model.UserModel();
		
		page.user.urlRoot = 'api/updatedefaults';
		
		page.user.save({
			'currentProject': $('select#currentProjectId').val(),
			'currentCategory': $('select#currentCategoryId').val()
		}, {
			wait: true,
			success: function(){
				$('#setDefaultsDialog').modal('hide');
				setTimeout("app.appendAlert('Defaults Updated','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('secondModelLoader');
		},
			error: function(model,response,scope){

				app.hideProgress('secondModelLoader');

				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'secondModelAlert');

				try {
					var json = $.parseJSON(response.responseText);

					if (json.errors)
					{
						$.each(json.errors, function(key, value) {
							$('#'+key+'InputContainer').addClass('error');
							$('#'+key+'InputContainer span.help-inline').html(value);
							$('#'+key+'InputContainer span.help-inline').show();
						});
					}
				} catch (e2) {
					if (window.console) console.log('error parsing server response: '+e2.message);
				}
			}
		});
	},
	
	
	/**
	 * delete the model that is currently displayed in the dialog
	 */
	deleteModel: function()
	{
		// reset any previous errors
		$('#modelAlert').html('');

		app.showProgress('modelLoader');

		page.timeEntry.destroy({
			wait: true,
			success: function(){
				$('#timeEntryDetailDialog').modal('hide');
				setTimeout("app.appendAlert('The TimeEntry record was deleted','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				if (model.reloadCollectionOnModelUpdate)
				{
					// re-fetch and render the collection after the model has been updated
					page.fetchTimeEntries(page.fetchParams,true);
				}
			},
			error: function(model,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
				app.hideProgress('modelLoader');
			}
		});
	}
};

