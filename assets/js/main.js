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