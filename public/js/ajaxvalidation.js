function doValidation(id,url)
{
    var data ={};
        $("input").each(function(){
             data[$(this).attr('name')]=$(this).val();
        });
     $.post(url,data,function(resp){
        $('#'+id).parent().find('.errors').remove();
        $('#'+id).parent().parent().append(getErrorHtml(resp[id],id));
    },'json');
}

function getErrorHtml(formErrors,id)
{
   
    var o='<ul id="errors-'+id+'" class="errors">';
    for(errorKey in formErrors)
    {
    	o+='<li>'+formErrors[errorKey]+'</li>';
    }
    o+='</ul>';
    return o;
}

function doValidationPlant(id,url)
{
    var data ={};
        $("input").each(function(){
             data[$(this).attr('name')]=$(this).val();
        });
     $.post(url,data,function(resp){
        $('#'+id).parent().find('.errors').remove();
        $('#'+id).parent().append(getErrorHtmlPlant(resp[id],id));
    },'json');
}

function getErrorHtmlPlant(formErrors,id){
   
    var o='<ul id="errors-'+id+'" class="errors">';
    for(errorKey in formErrors)
    {
    	o+='<li>'+formErrors[errorKey]+'</li>';
    }
    o+='</ul>';
    return o;
}

function removeHTMLTags(str){
 	if(str){
 		var strInputCode = str;
 			
 	 	strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1){
 		 	return (p1 == "lt")? "<" : ">";
 		});
 		var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
 		return strTagStrippedText;	
   	
 	}	
}