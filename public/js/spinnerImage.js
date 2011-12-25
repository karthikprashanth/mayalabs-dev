$("#submitbutton").click(function() {
	var file = document.getElementById("content1").value 
	+document.getElementById("content2").value
	+document.getElementById("content3").value
	+document.getElementById("content4").value
	+document.getElementById("content5").value; 
	
	var title = document.getElementById("prestitle1").value 
	+document.getElementById("prestitle2").value
	+document.getElementById("prestitle3").value
	+document.getElementById("prestitle4").value
	+document.getElementById("prestitle5").value;
	if (file != "")
	{
		if(title != "")
		{
			document.getElementById("spinnerimage").style.display = "block";
		}
	}
});