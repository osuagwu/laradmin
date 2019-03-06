/**
 * Find the class that needs to be full height and make them so
 * This is alternative and more accurate than the CSS rule which has already been set. If this does not run the CSS will be a fallback
 */
function resizeSegmentFullHeight(){
    var fullPageClass='.section-full-page';// class that needs to cover whole page considering the height of page top and footer
    var fullHeightClass='.section-full-height'; //class that needs to full-page considering height of page top

    $(fullPageClass).css("min-height", 
        $(window).height() - $('#site-top').height()  - $('#footer').height()  //
    );

    $(fullHeightClass).css("min-height", 
        $(window).height() - $('#site-top').height() // - $('#footer').height()  //
    );

    
    
}
$(window).resize(function(){
    resizeSegmentFullHeight(); 
});
//initial call 
resizeSegmentFullHeight();


/**
 * Make sure footer is at the bottom. We do this by extending the part of the site before the footer to conver the viewport.
 * This is an alternative/reinforcement of the  CSS rule which has already been set
 */
function resizeKeepFooterBottom(){
    var fullHeightEle='#site-top-and-content';
    $(fullHeightEle).css("min-height", 
        $(window).height() - $('#footer').height()  //
    );
    
}
$(window).resize(function(){
    resizeKeepFooterBottom(); 
});
//initial call
resizeKeepFooterBottom();



/**
 * Initialize the toggle button for sidebar and other sidebar stuff
 */
$(document).ready(function () {
    //attach toggle
    $('.sidebar-collapse-toggle').on('click', function () {
        $('.has-sidebar').toggleClass('sidebar-collapsed');
    });

    
    $(window).resize(function(){
        // auto toggle when window resize from between <= small and >=medium: This is b/c the sidebar works opposite on small and bigger screens; toggling the class below allow sidebar state to be maintained when window size changes //NOTE: This is not supper important though
        if(viewPortSwitched()){
            $('.has-sidebar').toggleClass('sidebar-collapsed');
        }
    }); 

    
    //use the mainbar as button to close the sidebar on small screens(NOTE: This is also not supper important though)
    $('.sidebar-mainbar .mainbar').on('click',function(eve){
        if(findBootstrapEnvironment()=='xs' && $('.has-sidebar').hasClass('sidebar-collapsed')){ // sidebar-collapsed actually means that sidebar is not collased for smaller screens i.e opposite of large screens.
            $('.has-sidebar').removeClass('sidebar-collapsed');
            
            //Use all possible methods to make sure clicks dont work here
            eve.preventDefault();
            eve.stopPropagation();
            return false;
        }
    });

    //initiate the sidebar second close button
    $('.sidebar-close-btn').on('click',function(){
        $('.has-sidebar').toggleClass('sidebar-collapsed');
    });

});


/**
 * Popovers
 */
$(document).ready(function(){
    $('[data-toggle="popover"]').popover(); 
});

/**
 * Smooth Scrolling To Internal Links With jQuery
 * src: https://paulund.co.uk/smooth-scroll-to-internal-links-with-jquery
 */
$(document).ready(function(){
	$('a[href^="#"]').on('click',function (e) {
	    e.preventDefault();

	    var target = this.hash;
	    var $target = $(target); 

	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 900, 'swing', function () {
	        window.location.hash = target;
	    });
    });
    
});


/**
 * Makes the head dropdown menu item navigatable. This is correction for Bootstrap 3
 * TODO: This is a temporary solution which does not even work properly. 
 */
// $(function(){
//     $('.dropdown-toggle').click(
//       function(evt){
//           //evt.preventDefault();
//           //evt.stopPropagation();        
//     });  
// });
  


/**
 * Vue
 */////////////////////////////////////////
// Vue ///
var app=new Vue({
    el:'#app',
})