jQuery(document).ready(function($) {	
	//* Include colorpicker
	
	$('.wow-plugin .tab-nav li:first').addClass('select'); 
	$('.wow-plugin .tab-panels>div').hide().filter(':first').show();    
	$('.wow-plugin .tab-nav a').click(function(){
		$('.wow-plugin .tab-panels>div').hide().filter(this.hash).show(); 
		$('.wow-plugin .tab-nav li').removeClass('select');
		$(this).parent().addClass('select');
		return (false); 
	})	
	$('.wow-plugin input:checkbox:checked').each(function(){
		var str = $(this).attr("id");
		var check = str.replace("wow_", "");
		$( "input[name='param["+check+"]']" ).val(1);	
		});
	
	$('.wow-plugin input[type="checkbox"]').change(function () {
		var str = $(this).attr("id");
		var check = str.replace("wow_", "");
		if($(this).prop('checked')){			
			$( "input[name='param["+check+"]']" ).val(1);			
		}
		else {
			$( "input[name='param["+check+"]']" ).val(0);
		}
	});	
	
	wow_attach_tooltips($(".wow-help"));	

});


function wow_attach_tooltips(selector) {
    selector.tooltip({
        content: function() {
            return jQuery(this).prop("title")
        },
        tooltipClass: "wow-ui-tooltip",
        position: {
            my: "center top",
            at: "center bottom+10",
            collision: "flipfit"
        },
        hide: {
            duration: 200
        },
        show: {
            duration: 200
        }
    })
}

