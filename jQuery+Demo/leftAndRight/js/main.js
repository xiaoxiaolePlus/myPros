/**
 * Created by 顾萌萌 on 2016/11/30.
 */
$(window).load(function(){
    $(".left").mCustomScrollbar();
});
var $li = $(".left>ul>li");
//console.log($li);
$li.bind("click",function (event) {
    $(this).addClass("active").siblings().removeClass("active");
    var url = "html/test_" + $(this).html() + ".html";
    //console.log (url);
    $("#right").load(url);
});
$li.eq(0).trigger("click");