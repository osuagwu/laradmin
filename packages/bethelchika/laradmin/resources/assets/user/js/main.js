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
 * Initialize the toggle button for sidebar 
 */
$(document).ready(function () {
    
    $('.sidebar-collapse-toggle').on('click', function () {
        $('.sidebar').toggleClass('collapsed');
    });

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