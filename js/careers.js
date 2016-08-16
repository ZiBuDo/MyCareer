$( document ).ready(function() {
    $.ajax({
	  url: "careers.php",
	  type: "POST",
	  success: function(data){
		$("#careers").append(data);
		$("#choiceList").show();
		$(".loader").fadeOut("slow");
	  }
	});
	var foo = getParameterByName('career');
	if(foo != null && foo != undefined && foo != ""){
		$("#career").val(foo);
		getCareer();
	}
	 $("#careerChoice").submit(function(e){
        e.preventDefault();
		getCareer();
    });
});

function getCareer(){
	$(".loader2").show();
	var car = $("#career").val();
	 $.ajax({
	  url: "career.php?career=" + car,
	  type: "GET",
	  success: function(data){
		  var arr = data.split("|");
			$("#Overview").html(arr[0]);
			$("#Knowledge").html(arr[1]);
			$("#Skills").html(arr[2]);
			$("#Abilities").html(arr[3]);
			$("#Green").html(arr[4]);
			$("#Technologies").html(arr[5]);
			$("#Similar").html(arr[6]);
			
			
			//reset tables
			var tfrow = document.getElementById('tfhover1').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover1').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			
			var tfrow = document.getElementById('tfhover2').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover2').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover3').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover3').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover4').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover4').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover5').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover5').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover6').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover6').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover7').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover7').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			var tfrow = document.getElementById('tfhover8').rows.length;
			var tbRow=[];
			for (var i=1;i<tfrow;i++) {
				tbRow[i]=document.getElementById('tfhover8').rows[i];
				tbRow[i].onmouseover = function(){
				  this.style.backgroundColor = '#f3f8aa';
				};
				tbRow[i].onmouseout = function() {
				  this.style.backgroundColor = '#ffffff';
				};
			}
			
			$("#info").show();
			$(".loader2").fadeOut("slow");
	  }
	 });
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}