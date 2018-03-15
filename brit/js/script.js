
window.onresize = function() {
	if (window.innerWidth) {
		document.getElementById("dimensionsouter").innerHTML = "&nbsp;size: " + window.outerWidth + " x " + window.outerHeight + " px";
		document.getElementById("dimensionsinner").innerHTML = " (" + window.innerWidth + " x " + window.innerHeight + " px)";
		}
	else {
		document.getElementById("dimensionsinner").innerHTML = "?: " + document.body.clientWidth + " x " + document.body.clientHeight + " pixels";
	}
}

$(".show_hidden_panel").click(function(){
	var aktId = $(this).attr("id");
  	/*$(".ct_title").text("ct_datas "+aktId+" div.hidden");*/
	$("#anim_"+aktId+".hide_form").slideToggle(500);
});

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

