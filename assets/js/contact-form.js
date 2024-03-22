document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('contact-form');
    const submit = document.getElementById('form-submit');

    submit.addEventListener('click', function(event) {
        event.preventDefault();
        let isValid = true;

        document.querySelectorAll('.error-message').forEach(function(msg) {
            msg.textContent = '';
        });

        const name = document.getElementById('contact-name');
        const email = document.getElementById('contact-email');
        const phone = document.getElementById('contact-phone');
        const password = document.getElementById('contact-password');
        const confirmPassword = document.getElementById('contact-confirm-password');
        const selectCity = document.getElementById('select-city');
        const privacyPolicy = document.getElementById('privacy-policy');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^\+?(\d{1,3})?[-.\s]?\(?\d{1,3}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/;

        if (name.value.trim() === '') {
            document.getElementById('name-error').textContent = 'Please enter your name';
            name.classList.add('input-error');
            isValid = false;
        } else {
            name.classList.remove('input-error');
        }

        if (email.value.trim() === '') {
            document.getElementById('email-error').textContent = 'The field cannot be empty';
            email.classList.add('input-error');
            isValid = false;
        } else {
            email.classList.remove('input-error');
        }

        if (!emailRegex.test(email.value)) {
            document.getElementById('email-error').textContent = 'Please enter a valid email address';
            email.classList.add('input-error');
            isValid = false;
        } else {
            email.classList.remove('input-error');
        }

        if (phone.value.trim() === '') {
            document.getElementById('phone-error').textContent = 'Please enter your phone number';
            phone.classList.add('input-error');
            isValid = false;
        } else {
            phone.classList.remove('input-error');
        }

        if (!phoneRegex.test(phone.value)) {
            document.getElementById('phone-error').textContent = 'Please enter a valid phone number';
            phone.classList.add('input-error');
            isValid = false;
        } else {
            phone.classList.remove('input-error');
        }

        if (password.value.length < 8 || password.value.trim() === '') {
            document.getElementById('password-error').textContent = 'Password must be at least 8 characters long';
            password.classList.add('input-error');
            isValid = false;
        } else {
            password.classList.remove('input-error');
        }

        if (password.value !== confirmPassword.value) {
            document.getElementById('confirm-password-error').textContent = 'passwords do not match';
            confirmPassword.classList.add('input-error');
            isValid = false;
        } else {
            confirmPassword.classList.remove('input-error');
        }

        if (selectCity.value === '') {
            document.getElementById('city-error').textContent = 'Please choose your city';
            selectCity.classList.add('input-error');
            isValid = false;
        } else {
            selectCity.classList.remove('input-error');
        }

        if (!privacyPolicy.checked) {
            document.getElementById('privacy-policy-error').textContent = 'You must accept the privacy policy';
            isValid = false;
        }

        if (isValid) {
            const formData = new FormData(form);
            formData.append('action', 'ib_submit_contact_form');

            fetch(ib_localize.admin_url, {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }
    });

    document.querySelectorAll('.toggle-password__js').forEach(function(toggleButton) {
        toggleButton.addEventListener('click', function() {
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');

            let inputId = this.getAttribute('data-toggle');
            let input = document.querySelector(inputId);

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    });
});