/**
 * Global scripts 
 */


function myTimer(options) {	
	// and continue to run this every 1 sec
	setTimeout(myTimer, 1000);
	
	if (g_time_start === -1) return;
		
	g_time_elapsed = Math.round(new Date().getTime() / 1000) - g_time_start;
			
	var days=Math.floor(g_time_elapsed / 86400);
	var hours = Math.floor((g_time_elapsed - (days * 86400 ))/3600)
	var minutes = Math.floor((g_time_elapsed - (days * 86400 ) - (hours * 3600 ))/60)
	var secs = Math.floor((g_time_elapsed - (days * 86400 ) - (hours * 3600 ) - (minutes * 60)))
	
	if (days === 0){
		days = '';
	}else if (days === 1){
		days = days + " day, ";
	}else{
		days = days + " days, ";
	}

	if (minutes === 0){
		minutes = '00';
	}else if (minutes < 10){
		minutes = '0' + minutes;
	}

	if (secs < 10){
		secs = '0' + secs;
	}	
	secs = ':' + secs;
	
	if (hours === 0){
		hours = '';
	}else if (hours < 10){
		hours = '0' + hours + ':';
		secs = '';
	}else{
		hours = hours + ":";
		secs = '';
	}
	
	var total = days + hours + minutes + secs;
	
	$("#currentTimetrack").html('<i class="icon-time"></i> ' + total);
	
}

/** run timer */
myTimer();