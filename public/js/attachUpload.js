var attach_ids;

$(document).ready(function(){
	var source = document.getElementById("source").innerHTML;
	var data;
	var json;
	var title;
	var modeId;
	var mode;
	var name;
	attach_ids = document.getElementById("attach_ids").value;
	$("body").append("<iframe id='upload_target' name='upload_target' src='#' style='display:none'></iframe>");
	$("#attach-form").append("<input type = 'hidden' name = 'gtdata' value = 'true'>");
	if(source == 'add'){
		$("tr:has(td:has(button[name='addattach']))").append("<td><span id = 'attachments-head'></span><br><ul id = 'attachments-list'></ul></td>");
	}
	else if(source == 'edit'){
		$("tr:has(td:has(button[name='addattach']))").append($("#added-attachments").html());
	}
	$("#attach-form").append("<input type = 'hidden' name = 'source' value = '" + source + "'>");
	$("#add-attach").click(function(){
		$("#attachTitle").val("");
		$("input:file").val("");		
		$("#attach-form").dialog({title:'Add a new attachment',height:'auto',width:'auto'});
	});
	function startUpload(){			
	    return true;
    }
    
    function stopUpload(data){
	    return true;
    }
	
	$("#save-attach").click(function(){
		$("#spinnerimage").css("display","block");
	});
        
});

function del_attach(id){
	var attach_array;
	$.ajax({
		type: 'POST',
		data: "attachmentId=" + id,
		url : "/attachment/delete",
		success: function(msg){
			$("#a-"+id).remove();	
			if(document.getElementById("attachments-list").innerHTML == ""){
				$("#attachments-head").html("");
			}
			edit_attachids(id);
			$("#attach_ids").val(attach_ids);
		}
	});
}

function unlink(id){
	var gtdataid = document.getElementById("gtdataId").innerHTML; 
	$.ajax({
		type: 'POST',
		data: "attachmentId=" + id + "&gtdataid=" + gtdataid,
		url: "/attachment/unlink",
		dataType: 'json',
		success: function(msg){
			$("#a-"+id).remove();
			edit_attachids(id);
			$("#attach_ids").val(attach_ids);
			$("#presentationId").append("<option value='" + id + "' label='" + msg.title + "'>" + msg.title + "</option>");
			if(document.getElementById("attachments-list").innerHTML == ""){
				$("#attachments-head").html("");
			}
		}
	})
}

function edit_attachids(id){
	var attach_array = attach_ids.split(",");
	attach_ids = "";
	for(var i=0;i<attach_array.length-1;i++){
		if(parseInt(attach_array[i]) == id)
		{
			continue;
		}
		attach_ids = attach_ids + attach_array[i] + ",";
	} 
}
