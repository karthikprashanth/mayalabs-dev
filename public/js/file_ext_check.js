$(document).ready(function(){
	$("input:file").change(function(){
		var filename = $("input:file").val();
		var file_ext = filename.split(".").pop();
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
			$("input:file").val("");
		}
			
	});	
});




