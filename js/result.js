$( document ).ready(function() {
	var foo = getParameterByName('one');
	if(foo != null && foo != undefined && foo != ""){
		$("#one").html("<a style='color:black' href='http://projects.miscthings.xyz/CollegeCareer/careers.html?career="+ foo + "' target='_blank'>"+ foo + "</a>");
	}
	foo = getParameterByName('two');
	if(foo != null && foo != undefined && foo != ""){
		$("#two").html("<a style='color:black' href='http://projects.miscthings.xyz/CollegeCareer/careers.html?career="+ foo + "' target='_blank'>"+ foo + "</a>");
	}
	foo = getParameterByName('three');
	if(foo != null && foo != undefined && foo != ""){
		$("#three").html("<a style='color:black' href='http://projects.miscthings.xyz/CollegeCareer/careers.html?career="+ foo + "' target='_blank'>"+ foo + "</a>");
	}
	foo = getParameterByName('four');
	if(foo != null && foo != undefined && foo != ""){
		$("#four").html("<a style='color:black' href='http://projects.miscthings.xyz/CollegeCareer/careers.html?career="+ foo + "' target='_blank'>"+ foo + "</a>");
	}
	foo = getParameterByName('five');
	if(foo != null && foo != undefined && foo != ""){
		$("#five").html("<a style='color:black' href='http://projects.miscthings.xyz/CollegeCareer/careers.html?career="+ foo + "' target='_blank'>"+ foo + "</a>");
	}
});


function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}