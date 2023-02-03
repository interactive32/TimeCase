/**
 * backbone model definitions
 */

/**
 * Use emulated HTTP if the server doesn't support PUT/DELETE or application/json requests
 */
Backbone.emulateHTTP = false;
Backbone.emulateJSON = false

var model = {};

/**
 * long polling duration in miliseconds.  (5000 = recommended, 0 = disabled)
 * warning: setting this to a low number will increase server load
 */
model.longPollDuration = g_long_polling_duration;

/**
 * whether to refresh the collection immediately after a model is updated
 */
model.reloadCollectionOnModelUpdate = true;

/**
 * Category Backbone Model
 */
model.CategoryModel = Backbone.Model.extend({
	urlRoot: 'api/category',
	idAttribute: 'id',
	id: '',
	name: '',
	defaults: {
		'id': null,
		'name': ''
	}
});

/**
 * Category Backbone Collection
 */
model.CategoryCollection = Backbone.Collection.extend({
	url: 'api/categories',
	model: model.CategoryModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});

/**
 * Customer Backbone Model
 */
model.CustomerModel = Backbone.Model.extend({
	urlRoot: 'api/customer',
	idAttribute: 'id',
	id: '',
	name: '',
	contactPerson: '',
	email: '',
	password: '',
	allowLogin: '',
	address: '',
	location: '',
	web: '',
	tel: '',
	tel2: '',
	statusId: '',
	description: '',
	defaults: {
		'id': null,
		'name': '',
		'contactPerson': '',
		'email': '',
		'password': '',
		'allowLogin': '',
		'address': '',
		'location': '',
		'web': '',
		'tel': '',
		'tel2': '',
		'statusId': '2',
		'description': ''
	}
});

/**
 * Customer Backbone Collection
 */
model.CustomerCollection = Backbone.Collection.extend({
	url: 'api/customers/all',
	model: model.CustomerModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});

/**
 * Only active customers
 */
model.CustomerActiveOnlyCollection = model.CustomerCollection.extend({
	url: 'api/customers/active'
});

/**
 * Level Backbone Model
 */
model.LevelModel = Backbone.Model.extend({
	urlRoot: 'api/level',
	idAttribute: 'id',
	id: '',
	name: '',
	defaults: {
		'id': null,
		'name': ''
	}
});

/**
 * Level Backbone Collection
 */
model.LevelCollection = Backbone.Collection.extend({
	url: 'api/levels',
	model: model.LevelModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});


/**
 * Project Backbone Model
 */
model.ProjectModel = Backbone.Model.extend({
	urlRoot: 'api/project',
	idAttribute: 'id',
	id: '',
	title: '',
	customerId: '',
	created: '',
	closed: '',
	deadline: '',
	progress: '',
	statusId: '',
	description: '',
	defaults: {
		'id': null,
		'title': '',
		'customerId': '',
		'created': '',
		'closed': '',
		'deadline': '',
		'progress': '0',
		'statusId': '2',
		'description': ''
	}
});

/**
 * Project Backbone Collection
 */
model.ProjectCollection = Backbone.Collection.extend({
	url: 'api/projects/all',
	model: model.ProjectModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		
		for (var key in rows) {
			  
			if (typeof rows[key].created == "undefined" || typeof rows[key].deadline == "undefined") break;
			   var currentStamp = new Date().getTime();
			   var createdStamp = new Date(rows[key].created.replace(/-/g,'/')).getTime();
			   var deadlineStamp = new Date(rows[key].deadline.replace(/-/g,'/')).getTime();
			   
			   var cspan = currentStamp - createdStamp;
			   var tspan = deadlineStamp - createdStamp;
			   var deadlinePercent = cspan * 100 / tspan;
			   
			   if(!(deadlinePercent > 0)) deadlinePercent = 0;

			   rows[key].deadlineApproach = deadlinePercent;
			   
			}
		


		return rows;
	}
});

/**
 * Only active Projects
 */
model.ProjectActiveOnlyCollection = model.ProjectCollection.extend({
	url: 'api/projects/active'
});

/**
 * Status Backbone Model
 */
model.StatusModel = Backbone.Model.extend({
	urlRoot: 'api/status',
	idAttribute: 'id',
	id: '',
	description: '',
	defaults: {
		'id': null,
		'description': ''
	}
});

/**
 * Status Backbone Collection
 */
model.StatusCollection = Backbone.Collection.extend({
	url: 'api/statuses',
	model: model.StatusModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});

/**
 * TimeEntry Backbone Model
 */
model.TimeEntryModel = Backbone.Model.extend({
	urlRoot: 'api/timeentry',
	idAttribute: 'id',
	id: '',
	projectId: '',
	userId: '',
	categoryId: '',
	categoryName: '',
	start: '',
	end: '',
	description: '',
	defaults: {
		'id': null,
		'projectId': '',
		'userId': '',
		'categoryId': '',
		'start': '',
		'end': '',
		'description': ''
	}
	
});



/**
 * TimeEntry Backbone Collection
 */
model.TimeEntryCollection = Backbone.Collection.extend({
	url: 'api/timeentries',
	model: model.TimeEntryModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});


/**
 * Reports Model
 */
model.ReportsModel = Backbone.Model.extend({
	urlRoot: 'api/reports',
	idAttribute: 'id',
	id: '',
	projectId: '',
	userId: '',
	categoryId: '',
	categoryName: '',
	start: '',
	end: '',
	description: '',
	defaults: {
		'id': null,
		'projectId': '',
		'userId': '',
		'categoryId': '',
		'start': '',
		'end': '',
		'description': ''
	}
	
});



/**
 * Reports Backbone Collection
 */
model.ReportsCollection = Backbone.Collection.extend({
	url: 'api/reports',
	model: model.ReportsModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	totalDuration: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
			this.totalDuration = response.totalDuration;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
			this.totalDuration = response.totalDuration;
		}

		return rows;
	}
});

/**
 * User Backbone Model
 */
model.UserModel = Backbone.Model.extend({
	urlRoot: 'api/user',
	idAttribute: 'id',
	id: '',
	username: '',
	levelId: '',
	fullName: '',
	email: '',
	password: '',
	details: '',
	currentProject: '',
	currentCategory: '',
	defaults: {
		'id': null,
		'username': '',
		'levelId': '',
		'fullName': '',
		'email': '',
		'password': '',
		'details': '',
		'currentProject': null,
		'currentCategory': null
}
});

/**
 * User Backbone Collection
 */
model.UserCollection = Backbone.Collection.extend({
	url: 'api/users',
	model: model.UserModel,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, xhr) {

		// check the raw response to determine if collection actually changed
		// note xhr param was removed from backbone 0.99
		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;

		if (response.currentPage)
		{
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}
		else
		{
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});


