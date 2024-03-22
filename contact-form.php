<?php
/**
 *  Template Name: Contact Form
 *  Template Post Type: post, page
 */

get_header();

function ib_get_list_cities() {
	$cities = array(
		'New Jersey',
		'New Mexico',
		'New York',
		'Los Angeles',
		'Chicago',
		'Houston',
		'Phoenix',
		'Philadelphia',
		'San Antonio',
		'San Diego',
		'Dallas',
		'San Jose'
	);

	return $cities;
}

$cities = ib_get_list_cities();
?>
    <section class="contact-form" id="form">
        <form id="contact-form" method="post" action="">
            <div class="contact-form__header">
                <h2 class="form-title"><?php echo __( 'Reach Out and ' ); ?>
                    <span><?php echo __( 'Connect ' ); ?></span><?php echo __( 'With Us' ); ?></h2>
                <p class="form-text"><?php echo __( 'Bridging Your Ideas with Our Expertise' ); ?></p>
            </div>
            <div class="contact-form__part">
                <input type="text" name="name" class="contact-form__input" id="contact-name" placeholder="Name *"
                       required=""><span class="error-message" id="name-error"></span>
                <input type="email" name="email" class="contact-form__input" id="contact-email" placeholder="E-mail *"
                       required=""><span class="error-message" id="email-error"></span>
                <input type="tel" name="phone" class="contact-form__input" id="contact-phone" placeholder="Phone *"
                       required=""><span class="error-message" id="phone-error"></span>

                <div class="contact-form__part-password">
                    <input type="password" name="password" class="contact-form__input" id="contact-password"
                           minlength="8" placeholder="Password *" required="">
                    <span data-toggle="#contact-password"
                          class="fa-solid fa-eye eye-icon toggle-password toggle-password__js"></span>
                </div>
                <span class="error-message" id="password-error"></span>

                <div class="contact-form__part-password">
                    <input type="password" name="confirm_password" class="contact-form__input"
                           id="contact-confirm-password" minlength="8" placeholder="Password *" required="">
                    <span data-toggle="#contact-confirm-password"
                          class="fa-solid fa-eye eye-icon toggle-password toggle-password__js"></span>
                </div>
                <span class="error-message" id="confirm-password-error"></span>

                <div id="contact-city">
                    <multiselect
                            v-model="selectedCity"
                            :options="cities"
                            placeholder="Choise your city *"
                            class="contact-form__input"
                            label="name"
                            track-by="name"
                            @input="updateCity">
                    </multiselect>
                    <input type="hidden" name="city" id="select-city">
                </div>
                <span class="error-message" id="city-error"></span>
            </div>
            <div class="contact-form__footer">
                <input type="checkbox" name="privacy_policy" class="contact-form__input" id="privacy-policy"
                       required="">
                <label for="privacy-policy"><?php echo __( 'I have read and accepted ' ); ?>
                    <a href="#"><?php echo __( 'privacy policy' ); ?></a>
                </label><span class="error-message" id="privacy-policy-error"></span>
                <input type="submit" name="submit" value="Submit" class="contact-submit" id="form-submit">
            </div>
        </form>
    </section>
    <script>
        new Vue({
            el: '#contact-city',
            components: {
                Multiselect: window.VueMultiselect.default
            },
            data() {
                return {
                    selectedCity: '',
                    cities: <?php echo json_encode( array_map( function ( $city ) {
						return [ 'name' => $city ];
					}, $cities ) ); ?>,
                    cityError: ''
                };
            },
            methods: {
                updateCity(value) {
                    this.selectedCity = value;
                    document.getElementById('select-city').value = value ? value.name : '';
                }
            }
        });
    </script>
<?php
get_footer();