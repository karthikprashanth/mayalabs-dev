$(document).ready(function(){
	$("#content").change(function(){
		var filename = document.getElementById("content").value;
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
			document.getElementById("content").value = "";
		}
			
	});	
});




