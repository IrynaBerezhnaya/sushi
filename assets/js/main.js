jQuery(document).ready(function ($) {
    const body = $('body');

    body.on("updated_checkout", function () {
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


    //QTY box
    if (!String.prototype.getDecimals) {
        String.prototype.getDecimals = function () {
            let num = this,
                match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            if (!match) {
                return 0;
            }
            return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
        }
    }

    //QTY box buttons (minus, plus)
    body.on('click', '.qty_plus__js, .qty_minus__js', function (e) {
        e.preventDefault();

        let $qty = $(this).closest('.quantity').find('input[type="number"]'),
            currentVal = parseFloat($qty.val()),
            max = parseFloat($qty.attr('max')),
            min = parseFloat($qty.attr('min')),
            step = $qty.attr('step');

        // Format values
        if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
        if (max === '' || max === 'NaN') max = '';
        if (min === '' || min === 'NaN') min = 0;
        if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = '1';

        // Change the value
        if ($(this).is('.qty_plus__js')) {
            if (max && (currentVal >= max)) {
                $qty.val(max);
            } else {
                $qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
            }
        } else {
            if (min && (currentVal <= min)) {
                $qty.val(min);
            } else if (currentVal > 0) {
                $qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
            }
        }

        // Trigger change event
        $qty.trigger('change');
    });

    //open popup
    body.on('click', '.open_popup__js', function (e) {
        e.preventDefault();
        let popup = $(this).data('open-popup-on-click');
        if (popup) {
            $(popup).addClass('active');
            $('#popup_overlay').addClass('active');
        }
    });

    //popup_overlay
    body.on('click', '#popup_overlay', closePopups);

    function closePopups() {
        $('.popup__js.active').removeClass('active');
        $('#popup_overlay').removeClass('active');
    }

    let $citySelectionPopup = $('#city_selection_popup');
    if ($citySelectionPopup.length) {

        //Cities buttons (Буча, Бровари, ... )
        $citySelectionPopup.on('click', '.shipping_zone__js', function () {
            let $shippingZoneButton = $(this);

            $citySelectionPopup.find('.active').removeClass('active');
            $shippingZoneButton.addClass('active');
        });

        //City selection Submit
        $citySelectionPopup.on('click', '#shipping_zone_submit', function () {
            let $shippingZoneButton = $citySelectionPopup.find('.shipping_zone__js.active'),
                city = $shippingZoneButton.data('city'),
                postcode = $shippingZoneButton.data('post-code');

            $.ajax({
                url: mb_localize.admin_url,
                type: "POST",
                data: {
                    'action': 'mb_update_user_address_details',
                    'city': city,
                    'postcode': postcode,
                },
                error: function () {
                    console.log('error');
                },
                success: function (response) {
                    closePopups();

                    //Insert new value to checkout field:
                    if ($('#customer_details').length) {
                        $('#billing_city').val(city);
                        $('#billing_postcode').val(postcode);
                        $(document.body).trigger('update_checkout');
                    }

                    //Insert new value to header top bar:
                    let $cityTopBar = $('#user_city_top_bar_value');
                    if ($cityTopBar.length) {
                        $cityTopBar.text(city);
                    }
                }
            });
        });

        //Open City Selection popup from checkout
        body.on('click', '#billing_city_field, #billing_city_field .woocommerce-input-wrapper', function (e) {
            e.preventDefault();

            //make necessary button active before opening:
            let currentValue = $(this).find('#billing_city').val();
            if (currentValue !== '') {
                let $buttonToMakeActive = $citySelectionPopup.find('.shipping_zone__js[data-city="' + currentValue + '"]');
                if ($buttonToMakeActive.length) {
                    $citySelectionPopup.find('.shipping_zone__js.active').removeClass('active');
                    $buttonToMakeActive.addClass('active');
                }
            }

            $citySelectionPopup.addClass('active');
            $('#popup_overlay').addClass('active');
        });

        //Open City Selection popup from header
        body.on('click', '#user_city_top_bar', function () {

            //make necessary button active before opening:
            let currentValue = $(this).find('#user_city_top_bar_value').text();
            if (currentValue !== '') {
                let $buttonToMakeActive = $citySelectionPopup.find('.shipping_zone__js[data-city="' + currentValue + '"]');
                if ($buttonToMakeActive.length) {
                    $citySelectionPopup.find('.shipping_zone__js.active').removeClass('active');
                    $buttonToMakeActive.addClass('active');
                }
            }

            $citySelectionPopup.addClass('active');
            $('#popup_overlay').addClass('active');
        });

    }


    //Open Filter
    body.on('click', '.filter-title__js', function (e) {
        $('.wpfMainWrapper').toggleClass('active');
        $('.filter-close').toggleClass('active');
    });

    //Close Filter
    body.on('click', '.filter-close', function () {
        $(this).removeClass('active');
        $('.wpfMainWrapper').removeClass('active');
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

/* Scroll Event
**************************************************************/
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.navigation-branding');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > lastScrollTop) {
            header.classList.add('move-up');
        } else {
            if (currentScroll <= 0) {
                header.classList.remove('move-up');
            }
        }

        lastScrollTop = currentScroll;
    });
});

