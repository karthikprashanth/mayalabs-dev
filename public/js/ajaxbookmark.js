function addBookmark(category,id)
{
    url='/bookmark/add/';
    var data ={};
    var name = prompt("Enter the Bookmark Name: ");
	if(name == null || name == "")
	{
		return;
	}
    data['category']=category;
    data['id']=id;
    data['bmName'] = name;
    
	
    $.post(url,data,function(resp){
        $('#bookmark-container').html(resp);
    });
    alert("Bookmark added successfully");
}
function removeBookmark(id,mode)
{
    url='/bookmark/delete/';
    var data ={};
    data['id']=id;

    $.post(url,data,function(resp){
        $('#bookmark-container').html(resp);
    });
    alert("Bookmark removed successfully");
    if(mode == 'longlist')
    {
    	window.location = "/bookmark/longlist";
    }
}