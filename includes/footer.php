    </main>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content animate-on-scroll">
                <h2>Stay in the Loop</h2>
                <p>Get fresh recipes, exclusive offers, and bakery news delivered to your inbox.</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-gold">Subscribe</button>
                </form>
                <p class="newsletter-success" id="newsletterSuccess" style="display:none">Welcome to the family! Check your inbox for a sweet surprise.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-wave">
            <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
                <path d="M0,40 C360,100 1080,0 1440,60 L1440,100 L0,100 Z" fill="#2C1810"/>
            </svg>
        </div>
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Bake'n Flake</h3>
                    <p>Handcrafted with love. Every pastry tells a story of passion, quality ingredients, and the joy of baking.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="#" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                        </a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/pages/home.php">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/pages/menu.php">Our Menu</a></li>
                        <li><a href="<?= SITE_URL ?>/pages/custom-cake.php">Custom Cakes</a></li>
                        <li><a href="<?= SITE_URL ?>/pages/blog.php">Blog</a></li>
                        <li><a href="<?= SITE_URL ?>/pages/about.php">Our Story</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Visit Us</h4>
                    <p>247 Bakers Lane<br>Downtown, NY 10012</p>
                    <p>Mon-Sat: 7am - 8pm<br>Sun: 8am - 6pm</p>
                    <p><a href="tel:+12125551234">(212) 555-1234</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Bake'n Flake. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
