$(function() {
   
// Chart
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

}); // END Document Ready

/**
 * Custom Functions
 */