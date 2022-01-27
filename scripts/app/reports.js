/**
 * View logic for Reports
 */


var page = {

	timeEntries: new model.ReportsCollection(),
	collectionView: null,
	reports: null,
	modelView: null,
	isInitialized: false,
	isInitializing: false,

	fetchParams: { 
		orderBy: '', 
		orderDesc: '', 
		page: 1, 
		filterByTimeStart: '', 
		filterByTimeEnd: '', 
		filterByCustomer: '', 
		filterByProject: '',  
		filterByUser: '', 
		filterByCategory: ''},
		
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
		
		app.hideProgress('modelLoader');
		
		// show reports in new window
		$(".showReportButton").click(function(e) {
			e.preventDefault();
			
			var params = page.refreshData(true);
			params.page = '';
			var urlParams = $.param(params);
			var reportType = $(this).attr('data-type');
			
			window.open('report/' + reportType + '?' + urlParams);
			return true;
		});
		
		// selection or time changed
		$("#filterContainer input, #filterContainer select").change(function(e) {
			e.preventDefault();
			app.showProgress('modelLoader');
			
			var currentId = $(this).attr('id');

			// on customer change update projects combo so it displays only related projects
			if (currentId == 'customerId'){
				
				var customerId = $(this).val();
				
				// reset combo select for projectId
				$('#parentProjectId select option').remove();
				$('#parentProjectId ul li').remove();
				
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
						
						page.refreshData();
						app.hideProgress('modelLoader');
						return true;
						

					},
					error: function(collection,response,scope){
						app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
						return false;
					}
				});
			}
			
			page.refreshData();
			app.hideProgress('modelLoader');
		});
		

		// init date-pickers
		$('.date-picker')
		.datepicker({ format: 'yyyy-mm-dd' })
		.on('changeDate', function(ev){
			$('.date-picker').datepicker('hide');
		});
		
		
		// call comboboxes
		if (!app.browserSucks())
		{
			$('#customerId').combobox();
			$('#projectId').combobox();
			$('#userId').combobox();
			$('#categoryId').combobox();
			$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
		}

			
		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#timeEntryCollectionContainer"),
			templateEl: $("#timeEntryCollectionTemplate"),
			collection: page.timeEntries
		});

		
		// make the rows clickable ('rendered' is a custom event, not a standard backbone event)
		this.collectionView.on('rendered',function(){

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
		var initStart = $('input#start').val()+' '+$('input#start-time').val();
		var initEnd = $('input#end').val()+' '+$('input#end-time').val();
		this.fetchTimeEntries({ page: 1,  filterByTimeStart: initStart, filterByTimeEnd: initEnd});

	},
	
	refreshData: function(getFiltersOnly)
	{
		var timeStart = $('input#start').val()+' '+$('input#start-time').val();
		if (!$('input#end').val()){
			$('input#end').val($('input#start').val());
		}
		var timeEnd = $('input#end').val()+' '+$('input#end-time').val();
		var customerId = $('#customerId').val();
		var projectId = $('#projectId').val();
		var userId = $('#userId').val();
		var categoryId = $('#categoryId').val();
		
		page.fetchParams.filterByTimeStart = timeStart;
		page.fetchParams.filterByTimeEnd = timeEnd;
		page.fetchParams.filterByCustomer = customerId;
		page.fetchParams.filterByProject = projectId;
		page.fetchParams.filterByUser = userId;
		page.fetchParams.filterByCategory = categoryId;
		
		if (getFiltersOnly){
			return page.fetchParams;
		}
		
		page.fetchParams.page = 1;
		page.fetchTimeEntries(page.fetchParams);
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
					page.timeEntries.render();
				}

				// update total duration
				$('#totalDurationHolder').html(page.timeEntries.totalDuration);
				
				app.hideProgress('loader');
				page.fetchInProgress = false;
			},

			error: function(m, r) {
				app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'collectionAlert');
				app.hideProgress('loader');
				page.fetchInProgress = false;
			}

		});
	}

};

