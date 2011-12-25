ddsmoothmenu.init({
    mainmenuid: "smoothmenu1", //menu DIV id
    orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
    classname: 'ddsmoothmenu', //class added to menu's outer DIV
    customtheme: ["#2e4e68", "#00192c"],
    contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})
var color="#fff";
function glow(obj,ncolor) {
    color=obj.style.backgroundColor;
    obj.style.backgroundColor="#"+ncolor;
}
function unglow(obj) {
    obj.style.backgroundColor=color;
}
function expandNotification(){
    $("#note-hidden").removeClass('hidden');
    $("#note-view").addClass('hidden');
    $("#note-hide").removeClass('hidden')
}
function reduceNotification(){
    $("#note-hidden").addClass('hidden');
    $("#note-view").removeClass('hidden');
    $("#note-hide").addClass('hidden')
}

function advancedSearch(){
    $("#advancedSearch").slideDown(300);
    $("#advancedSearch").removeClass('hidden');    
    $("#displayAdvancedSearch").addClass('hidden');
	$("#removeAdvancedSearch").removeClass('hidden');
}

function normalSearch(){
	$("#advancedSearch").slideUp(300);
    $("#advancedSearch").addClass('hidden');    
    $("#displayAdvancedSearch").removeClass('hidden');
	$("#removeAdvancedSearch").addClass('hidden');
}