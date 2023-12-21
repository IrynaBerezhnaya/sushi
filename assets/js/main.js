jQuery(document).ready(function ($) {
    $("body").on("updated_checkout", function () {
        $.ajax({
            url: mb_localize.admin_url,
            type: "POST",
            data: {
                'action': 'mb_update_office_address_checkout_field',
            },
            error: function () {
                console.log('error');
            },
            success: function (response) {
                if (response) {
                    $('#office_address_field_container').html(response);
                }
            }
        });
    });
});

/* Menu Slider
**************************************************************/
let init = false,
    swiper;

function swiperMenu() {
    if (window.innerWidth <= 1200) {
        if (!init) {
            init = true;
            swiper = new Swiper(".categories-slide_js", {
                direction: "horizontal",
                slidesPerView: 3,
                grabCursor: true,
                keyboard: true,
                slidesPerGroup: 1,
                breakpoints: {
                    375: {
                        slidesPerView: 4,
                    },
                    550: {
                        slidesPerView: 6,
                    },
                    768: {
                        slidesPerView: 8,
                    },
                    992: {
                        slidesPerView: 12,
                    },
                },
            });
        }
    } else if (init) {
        swiper.destroy();
        init = false;
    }
}
swiperMenu();
window.addEventListener("resize", swiperMenu);