$( document ).ready(function() {
	
	$.ajax({
	  url: "data.php",
	  type: "GET",
	  success: function(data){
		var arr = data.split("|");
			$("#majors").html(arr[0]);
			$("#Interests").html(arr[1]);
			$("#Knowledge").html(arr[2]);
			$("#Skills").html(arr[3]);
			$("#Abilities").html(arr[4]);
			$("#Green").html(arr[5]);
			$('.interests').each(function(i, obj) {
				var temp = "#" + $(this).attr("inter");
				var temp2 = temp + "T";
				$(temp).slider({
					value: 1,
					min: 1,
					max: 7,
					step: .01,
					slide: function( event, ui ) {
					  $(temp2).val( ui.value );
				   }
				});
			});
			$('.knowledge').each(function(i, obj) {
				var temp = "#" + $(this).attr("know");
				var temp2 = temp + "T";
				$(temp).slider({
					value: 0,
					min: 0,
					max: 7,
					step: .01,
					slide: function( event, ui ) {
					  $(temp2).val( ui.value );
				   }
				});
			});
			$('.skills').each(function(i, obj) {
				var temp = "#" + $(this).attr("skill");
				var temp2 = temp + "T";
				$(temp).slider({
					value: 0,
					min: 0,
					max: 7,
					step: .01,
					slide: function( event, ui ) {
					  $(temp2).val( ui.value );
				   }
				});
			});
			$('.abilities').each(function(i, obj) {
				var temp = "#" + $(this).attr("ability");
				var temp2 = temp + "T";
				$(temp).slider({
					value: 0,
					min: 0,
					max: 7,
					step: .01,
					slide: function( event, ui ) {
					  $(temp2).val( ui.value );
				   }
				});
			});
			$(".loader").fadeOut("slow");
			$("#info").show();
	  }
	});
	
	
	$( "#datepicker" ).datepicker({
	  changeYear: true,
	  yearRange: "-100:+0"
	});
	
	
	 $("#classifyForm").submit(function(e){
        e.preventDefault();
		getClassification();
    });
	
});



function getClassification(){
	var datastring =  $("#classifyForm").serialize();
	$("#loading").show();
	$("#notload").hide();
	$.ajax({
	  url: "process.php",
	  type: "GET",
	  data: datastring,
	  success: function(data){
			window.location.href = data;
	  }
	});
	
}