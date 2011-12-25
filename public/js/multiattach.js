$(document).ready(function(){
	document.getElementById("prestitle2").style.display = "none";
	document.getElementById("prestitle2-label").style.display = "none";
	document.getElementById("content2").style.display = "none";
	document.getElementById("content2-label").style.display = "none";
	document.getElementById("del2").style.display = "none";	
	document.getElementById("del5").style.display = "none";
	document.getElementById("prestitle3").style.display = "none";
	document.getElementById("prestitle3-label").style.display = "none";
	document.getElementById("content3").style.display = "none";
	document.getElementById("content3-label").style.display = "none";
	document.getElementById("del3").style.display = "none";
	
	document.getElementById("prestitle4").style.display = "none";
	document.getElementById("prestitle4-label").style.display = "none";
	document.getElementById("content4").style.display = "none";
	document.getElementById("content4-label").style.display = "none";
	document.getElementById("del4").style.display = "none";
	
	document.getElementById("prestitle5").style.display = "none";
	document.getElementById("prestitle5-label").style.display = "none";
	document.getElementById("content5").style.display = "none";
	document.getElementById("content5-label").style.display = "none";
	
	
	$("#addmore").click(function(){
		if(document.getElementById("prestitle2").style.display == "none")
		{
			document.getElementById("prestitle2").style.display = "block";
			document.getElementById("prestitle2-label").style.display = "block";
			document.getElementById("content2").style.display = "block";
			document.getElementById("content2-label").style.display = "block";
			document.getElementById("del2").style.display = "block";
			
		}
		else if(document.getElementById("prestitle3").style.display == "none")
		{
			document.getElementById("prestitle3").style.display = "block";
			document.getElementById("prestitle3-label").style.display = "block";
			document.getElementById("content3").style.display = "block";
			document.getElementById("content3-label").style.display = "block";
			document.getElementById("del3").style.display = "block";
		}
		else if(document.getElementById("prestitle4").style.display == "none")
		{
			document.getElementById("prestitle4").style.display = "block";
			document.getElementById("prestitle4-label").style.display = "block";
			document.getElementById("content4").style.display = "block";
			document.getElementById("content4-label").style.display = "block";
			document.getElementById("del4").style.display = "block";
		}
		else if(document.getElementById("prestitle5").style.display == "none")
		{
			document.getElementById("prestitle5").style.display = "block";
			document.getElementById("prestitle5-label").style.display = "block";
			document.getElementById("content5").style.display = "block";
			document.getElementById("content5-label").style.display = "block";
			document.getElementById("del5").style.display = "block";
		}
		noDisplayed();
	});
	
	$("#del2").click(function(){
		document.getElementById("prestitle2").style.display = "none";
		document.getElementById("prestitle2-label").style.display = "none";
		document.getElementById("content2").style.display = "none";
		document.getElementById("content2-label").style.display = "none";
		document.getElementById("del2").style.display = "none";
		document.getElementById("prestitle2").value = "";
		document.getElementById("content2").value = "";
		noDisplayed();
		
	});
	
	$("#del3").click(function(){
		document.getElementById("prestitle3").style.display = "none";
		document.getElementById("prestitle3-label").style.display = "none";
		document.getElementById("content3").style.display = "none";
		document.getElementById("content3-label").style.display = "none";
		document.getElementById("del3").style.display = "none";
		document.getElementById("prestitle3").value = "";
		document.getElementById("content3").value = "";
		noDisplayed();
		
	});
	
	$("#del4").click(function(){
		document.getElementById("prestitle4").style.display = "none";
		document.getElementById("prestitle4-label").style.display = "none";
		document.getElementById("content4").style.display = "none";
		document.getElementById("content4-label").style.display = "none";
		document.getElementById("del4").style.display = "none";
		document.getElementById("prestitle4").value = "";
		document.getElementById("content4").value = "";
		noDisplayed();
	});
	
	$("#del5").click(function(){
		document.getElementById("prestitle5").style.display = "none";
		document.getElementById("prestitle5-label").style.display = "none";
		document.getElementById("content5").style.display = "none";
		document.getElementById("content5-label").style.display = "none";
		document.getElementById("del5").style.display = "none";
		document.getElementById("prestitle5").value = "";
		document.getElementById("content5").value = "";
		noDisplayed();
	});
	
	function noDisplayed()
	{
		var i = 1;
		var cnt=1;
		for(i=1;i<=5;i++)
		{
			if(document.getElementById("prestitle" + i).style.display == "block")
			{
				cnt++;
			}
		}
		var n = cnt;
		if(n != 5)
		{
			document.getElementById("addmore").style.display = "block";
		}
		else
		{
			document.getElementById("addmore").style.display = "none";
		}
	}
	
});
