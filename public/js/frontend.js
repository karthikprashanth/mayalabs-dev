function autocenter() {
	//#99B4DE
	var winH = document.body.offsetHeight;
	var winW = document.body.offsetWidth;
	var obj = document.getElementById('login-container');
	obj.style.position = 'absolute';
	obj.style.top = (winH - 450)/2;
	obj.style.left = (winW - 990)/2;
	document.getElementById("username").focus();
}

$(document).ready( function(){
        $('#plants').accordion({
            change: function(event,ui) {
				var index = jQuery(this).find("h3").index(ui.newHeader[0]);
				var plantid = document.getElementById('plant'+index).value;			
                $('.ui-accordion-content-active').load('../administration/list/',{"plantid": plantid})
                                                                  .height(250);
            }
        });
 });