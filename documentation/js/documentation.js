$(document).ready(function () {
    // Page animation
    $('.js-page-scrolling').click(function (e) {
        let $elementTarget = e.target.tagName.toLowerCase() === 'a' ? $(e.target) : $(e.target.parentNode),
            $scrollingTarget = $($elementTarget.attr('href')),
            scrollingPosition = 0;

        if ($scrollingTarget !== '#top') scrollingPosition = $scrollingTarget.offset().top - 35;
        else return false;

        $('html, body').animate({scrollTop: scrollingPosition}, 500);
    });

    $(window).bind('scroll', function () {
        let currentTop = $(window).scrollTop();

        $('.scrollspy-section').each(function (index) {
            let elemTop = $(this).offset().top - 55;
            let elemBottom = elemTop + $(this).height();
            if (currentTop >= elemTop && currentTop <= elemBottom) {
                let id = $(this).attr('id');
                let navElem = $('a[href="#' + id + '"]');
                navElem.parent().addClass('active').siblings().removeClass('active').children().children().removeClass('active');
            }
        })
    });

    // Toggle sidebar
    $('.toggle-sidebar').click(function (e) {
        e.preventDefault();
        $('.toggle-sidebar').toggleClass('change');
        $(".sidebar-wrapper").toggleClass('active');
    });

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });
});
