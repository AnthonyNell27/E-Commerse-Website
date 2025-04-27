<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container text-center">
        <h1 class="fw-bold">Get in Touch with Us</h1>
        <p class="lead">We're here to help. Fill out the form, and we'll get back to you shortly.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="container contact-container">
    <div class="row align-items-center">
        <!-- Left Column: Google Map -->
        <div class="col-md-6">
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.6800470034927!2d121.05925511604116!3d14.586787289810478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c82e6f2b5cbd%3A0x940c6d20a4f648d!2sAIC%20Burgundy%20Empire%20Tower!5e0!3m2!1sen!2sph!4v1644339532895!5m2!1sen!2sph" 
                    width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            <div class="contact-info">
                <h3>📍 Our Office</h3>
                <p>4th UNIT 416 AIC BURGUNDY EMPIRE TOWER ADBI AVE SAPPHIRE ROAD, ORTIGAS CENTER PASIG CITY</p>
                
                <h3>📞 Contact</h3>
                <p>Email: <a href="mailto:info@slash.ph">info@slash.ph</a></p>
                <p>Phone: +63 2 1234 5678</p>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="col-md-6">
            <div class="contact-form shadow p-4 bg-white">
                <h3 class="mb-3">Send Us a Message</h3>
                <form action="process_contact.php" method="POST">
                    <div class="mb-3">
                       
                        <input type="text" id="name" name="name" class="form-control form-input" placeholder="Enter your full name" required>
                    </div>

                    <div class="mb-3">
                     
                        <input type="email" id="email" name="email" class="form-control form-input" placeholder="Enter your email" required>
                    </div>

                    <div class="mb-3">
               
                        <input type="tel" id="contact_number" name="contact_number" class="form-control form-input" placeholder="Enter your phone number" required>
                    </div>

                    <div class="mb-3">
                
                        <textarea id="message" name="message" class="form-control form-input" placeholder="Type your message here..." rows="4" required></textarea>
                    </div>

                    <div class="privacy-checkbox">
                    <input type="checkbox" id="privacy-policy-contact" required>
                 <label for="privacy-policy-contact">
                                 I agree to the <a href="https://www.slash.ph/SlashDotPH_privacypolicy.pdf" target="_blank" download="Privacy-Policy.pdf">Privacy Policy</a> and <a href="#">Terms of Use</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-black w-100" id="submit-contact" disabled>Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    document.getElementById("privacy-policy-contact").addEventListener("change", function () {
    document.getElementById("submit-contact").disabled = !this.checked;
});

</script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php include 'footer.php'; ?>
