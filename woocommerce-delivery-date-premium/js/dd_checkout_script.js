jQuery(document).ready(function(){
	jQuery("#delivery_date").width("150px");
	var holydays =  dd.holydays.split(',');					
	var days_dis = dd.days_dis.split(',').map(Number);
	var date_format = dd.date_format;
	
	jQuery("#delivery_date").datepicker({
			dateFormat		: date_format,
			beforeShowDay	: function(date){ 
			 
				/*var _date = jQuery.datepicker.formatDate(date_format, date);
				if( jQuery.inArray( _date, holydays ) != -1 ){
					return true;
				}*/
				var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
				//return [ holydays.indexOf(string) == -1 ]
				if( jQuery.inArray( string, holydays ) != -1 ){
					return true;
				}
				
				var day = date.getDay() + 1;
				
				return [jQuery.inArray( day, days_dis ) == -1]
			},						

			minDate			: dd.dates_to_deliver, 
			changeMonth		: true, 
			changeYear		: true, 
			yearRange		: dd.year_range
	});
	
	jQuery("#delivery_date").after("<div><small style=font-size:10px;>"+dd.msg_text+"</small></div>");
});