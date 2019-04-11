$(function() {
   
/**
 * Chart
 */

if ($('.chart').length) {
    var max_value  = $('.chart').data("max-value");
    var cur_cnt    = 0;
    var cur_height = 0;

    $(".chart .bar").each(function(index) {
        cur_cnt = $(this).data("value");
        cur_height = Math.round((cur_cnt / max_value) * 150);
        $(this).animate({height: cur_height,}, 500 );
    });
}

/**
 * Collapsible Panel
 */
    $('.toggle-collapse').click(function(e) {
        var target = $(this).data('target');
        $(target).toggleClass("collapse");        
        $(this).toggleClass("rotate");
    });

}); // END Document Ready

/**
 * Custom Functions
 */