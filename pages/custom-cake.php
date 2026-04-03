<?php
$pageTitle = 'Custom Cake Builder';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$minDate = date('Y-m-d', strtotime('+5 days'));
?>

<section class="custom-cake-page">
    <div class="container">
        <div class="page-header">
            <h1>Build Your Dream Cake</h1>
            <div class="section-divider"></div>
            <p>Design your perfect custom cake for any occasion.</p>
        </div>

        <div class="cake-builder-layout">
            <!-- Left Column: Form -->
            <form id="cakeBuilderForm" class="cake-builder-form" method="POST" action="<?= SITE_URL ?>/api/custom_order.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                <input type="hidden" name="occasion" id="occasion" value="">
                <input type="hidden" name="flavor_base" id="flavor_base" value="">
                <input type="hidden" name="frosting" id="frosting" value="">
                <input type="hidden" name="size" id="size" value="8&quot;">
                <input type="hidden" name="estimated_price" id="estimated_price" value="50.00">

                <!-- Occasion -->
                <div class="cake-builder-section">
                    <h3>Occasion</h3>
                    <div class="cake-option-cards">
                        <div class="cake-option-card" data-input="occasion" data-value="Birthday">
                            <span class="cake-option-emoji">&#127874;</span>
                            <span>Birthday</span>
                        </div>
                        <div class="cake-option-card" data-input="occasion" data-value="Wedding">
                            <span class="cake-option-emoji">&#9962;</span>
                            <span>Wedding</span>
                        </div>
                        <div class="cake-option-card" data-input="occasion" data-value="Anniversary">
                            <span class="cake-option-emoji">&#128157;</span>
                            <span>Anniversary</span>
                        </div>
                        <div class="cake-option-card" data-input="occasion" data-value="Baby Shower">
                            <span class="cake-option-emoji">&#127868;</span>
                            <span>Baby Shower</span>
                        </div>
                        <div class="cake-option-card" data-input="occasion" data-value="Graduation">
                            <span class="cake-option-emoji">&#127891;</span>
                            <span>Graduation</span>
                        </div>
                        <div class="cake-option-card" data-input="occasion" data-value="Other">
                            <span class="cake-option-emoji">&#10024;</span>
                            <span>Other</span>
                        </div>
                    </div>
                </div>

                <!-- Flavor -->
                <div class="cake-builder-section">
                    <h3>Flavor</h3>
                    <div class="cake-option-cards">
                        <div class="cake-option-card" data-input="flavor_base" data-value="Vanilla">
                            <span class="color-swatch" style="background-color:#FFF5E1"></span>
                            <span>Vanilla</span>
                        </div>
                        <div class="cake-option-card" data-input="flavor_base" data-value="Chocolate">
                            <span class="color-swatch" style="background-color:#5C3317"></span>
                            <span>Chocolate</span>
                        </div>
                        <div class="cake-option-card" data-input="flavor_base" data-value="Red Velvet">
                            <span class="color-swatch" style="background-color:#8B0000"></span>
                            <span>Red Velvet</span>
                        </div>
                        <div class="cake-option-card" data-input="flavor_base" data-value="Lemon">
                            <span class="color-swatch" style="background-color:#FFF44F"></span>
                            <span>Lemon</span>
                        </div>
                        <div class="cake-option-card" data-input="flavor_base" data-value="Carrot">
                            <span class="color-swatch" style="background-color:#ED9121"></span>
                            <span>Carrot</span>
                        </div>
                    </div>
                </div>

                <!-- Filling -->
                <div class="cake-builder-section">
                    <h3>Filling</h3>
                    <div class="form-group">
                        <select name="filling" id="filling">
                            <option value="Chocolate Ganache">Chocolate Ganache</option>
                            <option value="Fruit Jam">Fruit Jam</option>
                            <option value="Cream Cheese">Cream Cheese</option>
                            <option value="Custard">Custard</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>

                <!-- Frosting -->
                <div class="cake-builder-section">
                    <h3>Frosting</h3>
                    <div class="cake-option-cards">
                        <div class="cake-option-card selected" data-input="frosting" data-value="Buttercream">
                            <span>Buttercream</span>
                        </div>
                        <div class="cake-option-card" data-input="frosting" data-value="Fondant">
                            <span>Fondant</span>
                        </div>
                        <div class="cake-option-card" data-input="frosting" data-value="Cream Cheese">
                            <span>Cream Cheese</span>
                        </div>
                        <div class="cake-option-card" data-input="frosting" data-value="Whipped Cream">
                            <span>Whipped Cream</span>
                        </div>
                        <div class="cake-option-card" data-input="frosting" data-value="Chocolate">
                            <span>Chocolate</span>
                        </div>
                        <div class="cake-option-card" data-input="frosting" data-value="Naked">
                            <span>Naked</span>
                        </div>
                    </div>
                </div>

                <!-- Size -->
                <div class="cake-builder-section">
                    <h3>Size</h3>
                    <div class="cake-option-cards">
                        <div class="cake-option-card" data-input="size" data-value='6"'>
                            <span class="cake-option-size">6"</span>
                            <span>Serves 8</span>
                        </div>
                        <div class="cake-option-card selected" data-input="size" data-value='8"'>
                            <span class="cake-option-size">8"</span>
                            <span>Serves 12</span>
                        </div>
                        <div class="cake-option-card" data-input="size" data-value='10"'>
                            <span class="cake-option-size">10"</span>
                            <span>Serves 20</span>
                        </div>
                        <div class="cake-option-card" data-input="size" data-value='12"'>
                            <span class="cake-option-size">12"</span>
                            <span>Serves 30</span>
                        </div>
                    </div>
                </div>

                <!-- Tiers -->
                <div class="cake-builder-section">
                    <h3>Tiers</h3>
                    <div class="form-group">
                        <select name="tiers" id="tiers">
                            <option value="1">1 Tier</option>
                            <option value="2">2 Tiers</option>
                            <option value="3">3 Tiers</option>
                        </select>
                    </div>
                </div>

                <!-- Message on Cake -->
                <div class="cake-builder-section">
                    <h3>Message on Cake</h3>
                    <div class="form-group">
                        <input type="text" name="cake_message" id="cakeMessage" placeholder="e.g., Happy Birthday Sarah!" maxlength="50">
                    </div>
                </div>

                <!-- Reference Image -->
                <div class="cake-builder-section">
                    <h3>Reference Image (Optional)</h3>
                    <div class="image-upload-zone">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--color-gold)">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p>Click or drag an image here</p>
                        <input type="file" name="reference_image" id="referenceImage" accept="image/*" style="display:none">
                    </div>
                </div>

                <!-- Date Needed -->
                <div class="cake-builder-section">
                    <h3>Date Needed</h3>
                    <div class="form-group">
                        <input type="date" name="date_needed" id="dateNeeded" min="<?= $minDate ?>" required>
                    </div>
                </div>

                <!-- Special Notes -->
                <div class="cake-builder-section">
                    <h3>Special Notes</h3>
                    <div class="form-group">
                        <textarea name="special_notes" id="specialNotes" rows="3" placeholder="Allergies, dietary restrictions, design details..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold btn-full">Submit Custom Order</button>
            </form>

            <!-- Right Column: Preview -->
            <div class="cake-preview-wrapper">
                <h3>Preview</h3>
                <div class="cake-preview">
                    <div class="cake-tier" style="display:block"></div>
                    <div class="cake-tier" style="display:none"></div>
                    <div class="cake-tier" style="display:none"></div>
                    <div class="cake-plate"></div>
                    <span class="cake-message-preview"></span>
                </div>
                <div class="cake-price-section">
                    <span>Estimated Price:</span>
                    <span class="cake-price-estimate">$50.00</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
