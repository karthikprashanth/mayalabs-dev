$("#content1").change(function(){
	checkExt("content1");
});
$("#content2").change(function(){
	checkExt("content2");
});
$("#content3").change(function(){
	checkExt("content3");
});
$("#content4").change(function(){
	checkExt("content4");
});
$("#content5").change(function(){
	checkExt("content5");
});
$("#GTData").submit(function(){
	var filename;
	var prestitle;
	var i=1;
	for(i=1;i<=5;i++)
	{
		filename = document.getElementById("content"+i).value;
		prestitle = document.getElementById("prestitle"+i).value;
		if(prestitle != "" && filename == "")
		{
			alert("You have not uploaded a file for '" + prestitle + "'");
			return false;
		}
		if(filename != "" && prestitle == "")
		{
			alert("You have not given a title for '" + filename + "'");
			return false;
		}
	}
});
function checkExt(content,title)
{
	var filename = document.getElementById(content).value;
	var file_ext = filename.substring(filename.indexOf(".")+1);
	file_ext = file_ext.toLowerCase();
	if(file_ext == 'pdf' || file_ext == 'doc' || file_ext == 'ppt' ||
	file_ext == 'docx' || file_ext == 'pptx' || file_ext == 'xls' ||
	file_ext == 'xlsx' || file_ext == 'jpg' || file_ext == 'jpeg' ||
	file_ext == 'gif' || file_ext == 'png')
	{
		return;
	}
	else
	{
		alert("'" + file_ext + "' Files are not allowed");
		document.getElementById(content).value = "";
	}
}
