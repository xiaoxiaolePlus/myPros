$(function(){
	var mySwiper = new Swiper ('.swiper-container', {
	    //direction: 'vertical',
	    loop: true,
	    //自动播放
	    autoplay:1000,
	    //播放速度
	    speed:1000,
	    // 如果需要分页器
	    pagination: '.swiper-pagination',
	    //页面点击
	    paginationClickable: true,
	    //如果需要前进后退按钮
	    nextButton: '.swiper-button-next',
	    prevButton: '.swiper-button-prev',
	    //操作之后是否停止轮播，，默认true；
	    autoplayDisableOnInteraction:false,
	    // 如果需要滚动条
	    // scrollbar: '.swiper-scrollbar',
		  // paginationBulletRender: function (index, className) {
		  //     return '<span class="' + className + '">' + (index + 1) + '</span>';
		  // },
		onSlideChangeStart: function(swiper){
			var swiper_mian_box = $('.swiper_mian_box');
			swiper_mian_box.css({opacity:0,left:"0px",transition: "0s"});
		},
		onSlideChangeEnd: function(swiper){
			 $('.paginaOn').html('0'+(swiper.activeIndex-1));
			 var swiper_mian_box = $('.swiper_mian_box');
			swiper_mian_box.css({opacity:1,left:"50%",transition: "0.8s"});
		}

	});
	var leng = $('.swiper-pagination-bullet').length;
	$(".paginaNum").html('/0'+leng);
})
//祖星云swiper、、借鉴点swiper里面的很多自生成的标签可以自定义类名
// var bannerSwiper = new Swiper('.banner-carousel', {
//     pagination: '.pagination',
//     bulletClass: 'nav',
//     bulletActiveClass: 'active-nav',
//     prevButton:'.button-prev',
//     nextButton:'.button-next',
//     paginationType: 'bullets',
//     paginationClickable: true,
//     autoplay: 4000,
//     loop: true,
//     autoplayDisableOnInteraction: false,
//     onSlideChangeStart: function(swiper){
//         var index = Number($('.banner-carousel .swiper-slide-active').attr("data-swiper-slide-index"));
//         index = index < 10?'0'+(index+1):index+1;
//         $(".content-nav .currNum").html(index);
//         var img = $('.banner-carousel .swiper-slide-active .fade-left');
//         img.css({opacity:0,right:"530px",transition: "0s"});
//     },
//     onSlideChangeEnd: function(){
//         var img = $('.banner-carousel .swiper-slide-active .fade-left');
//         var div = $('.banner-carousel .swiper-slide-active .btn-bg1');
//         img.css({opacity:1,right:0,transition: "0.8s"});
//         div.css({transition: "1.2s"});
//     }
// });