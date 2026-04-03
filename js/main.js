/* ============================================
   BAKE'N FLAKE — Main JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initMobileMenu();
    initCarousel();
    initMenuTabs();
    initCategoryFilters();
    initCartActions();
    initScrollAnimations();
    initFlashDismiss();
    initSignupValidation();
    initSearch();
    initUserDropdown();
    initFavorites();
    initProductTabs();
    initStarSelector();
    initNewsletter();
    initOrderHistory();
    initCakeBuilder();
    initDiscountCode();
    initDeliverySlots();
    initPaymentFlow();
    initConfetti();
});

/* ---- CSRF Token ---- */
function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/* ---- Navigation ---- */
function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* ---- Mobile Menu ---- */
function initMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    const overlay = document.getElementById('mobileOverlay');
    if (!hamburger || !navLinks) return;

    const toggle = () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    };

    hamburger.addEventListener('click', toggle);
    if (overlay) overlay.addEventListener('click', toggle);

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) toggle();
        });
    });
}

/* ---- Image Carousel ---- */
function initCarousel() {
    const wrapper = document.querySelector('.carousel-wrapper');
    if (!wrapper) return;

    const slides = wrapper.querySelectorAll('.carousel-slide');
    const dots = wrapper.querySelectorAll('.carousel-dot');
    if (slides.length === 0) return;

    let current = 0;
    let interval;

    function goTo(index) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startAutoPlay() {
        interval = setInterval(() => goTo(current + 1), 4000);
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            clearInterval(interval);
            goTo(i);
            startAutoPlay();
        });
    });

    startAutoPlay();
}

/* ---- Menu Tabs (Home Page) ---- */
function initMenuTabs() {
    const tabs = document.querySelectorAll('.menu-tab-btn');
    const panels = document.querySelectorAll('.menu-panel');
    if (tabs.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.tab);
            if (target) target.classList.add('active');
        });
    });
}

/* ---- Category Filters (Menu Page) ---- */
function initCategoryFilters() {
    const pills = document.querySelectorAll('.filter-pill');
    const cards = document.querySelectorAll('.product-card[data-category]');
    if (pills.length === 0) return;

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            const cat = pill.dataset.category;

            cards.forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.classList.remove('hidden');
                    card.classList.add('fade-in');
                } else {
                    card.classList.add('hidden');
                    card.classList.remove('fade-in');
                }
            });
        });
    });
}

/* ---- Cart Actions ---- */
function initCartActions() {
    // Add to cart buttons
    document.addEventListener('click', (e) => {
        const addBtn = e.target.closest('.btn-add-cart');
        if (addBtn) {
            e.preventDefault();
            const productId = addBtn.dataset.productId;
            if (productId) addToCart(productId, 1);
        }

        // Quantity controls
        const qtyBtn = e.target.closest('.qty-btn');
        if (qtyBtn) {
            const productId = qtyBtn.dataset.productId;
            const action = qtyBtn.dataset.action;
            const qtyEl = qtyBtn.parentElement.querySelector('.qty-value');
            let qty = parseInt(qtyEl.textContent);

            if (action === 'plus') qty++;
            else if (action === 'minus') qty = Math.max(0, qty - 1);

            if (qty === 0) {
                removeFromCart(productId);
            } else {
                qtyEl.textContent = qty;
                updateCart(productId, qty);
            }
        }

        // Remove button
        const removeBtn = e.target.closest('.cart-item-remove');
        if (removeBtn) {
            const productId = removeBtn.dataset.productId;
            removeFromCart(productId);
        }
    });
}

function addToCart(productId, qty) {
    fetch(getSiteUrl() + '/api/cart_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCSRFToken()
        },
        body: JSON.stringify({ action: 'add', product_id: productId, quantity: qty, csrf_token: getCSRFToken() })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cartCount);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Error adding to cart', 'error');
        }
    })
    .catch(() => showToast('Network error', 'error'));
}

function updateCart(productId, qty) {
    fetch(getSiteUrl() + '/api/cart_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCSRFToken()
        },
        body: JSON.stringify({ action: 'update', product_id: productId, quantity: qty, csrf_token: getCSRFToken() })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cartCount);
            updateCartSummary(data);
            // Update line total
            const row = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
            if (row) {
                const priceText = row.querySelector('.cart-item-price').textContent;
                const unitPrice = parseFloat(priceText.replace('$', ''));
                const lineEl = row.querySelector('.cart-item-line');
                if (lineEl) lineEl.textContent = '$' + (unitPrice * qty).toFixed(2);
            }
        }
    });
}

function removeFromCart(productId) {
    fetch(getSiteUrl() + '/api/cart_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCSRFToken()
        },
        body: JSON.stringify({ action: 'remove', product_id: productId, csrf_token: getCSRFToken() })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cartCount);
            const row = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-30px)';
                setTimeout(() => {
                    row.remove();
                    updateCartSummary(data);
                    // Show empty state if no items left
                    if (data.cartCount === 0) {
                        location.reload();
                    }
                }, 300);
            }
            showToast(data.message, 'success');
        }
    });
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
}

function updateCartSummary(data) {
    const els = {
        subtotal: document.getElementById('cartSubtotal'),
        tax: document.getElementById('cartTax'),
        shipping: document.getElementById('cartShipping'),
        total: document.getElementById('cartTotal')
    };
    if (els.subtotal) els.subtotal.textContent = data.subtotal;
    if (els.tax) els.tax.textContent = data.tax;
    if (els.shipping) els.shipping.textContent = data.shipping;
    if (els.total) els.total.textContent = data.total;
}

function getSiteUrl() {
    return '/Bakery';
}

/* ---- Toast Notifications ---- */
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* ---- Scroll Animations ---- */
function initScrollAnimations() {
    const elements = document.querySelectorAll('.animate-on-scroll, .stagger-children');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    elements.forEach(el => observer.observe(el));
}

/* ---- Flash Messages ---- */
function initFlashDismiss() {
    const flash = document.getElementById('flashMsg');
    if (!flash) return;
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transform = 'translate(-50%, -20px)';
        setTimeout(() => flash.remove(), 400);
    }, 4000);
}

/* ---- Signup Validation ---- */
function initSignupValidation() {
    const form = document.getElementById('signupForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        const pw = form.querySelector('[name="password"]').value;
        const cpw = form.querySelector('[name="confirm_password"]').value;
        if (pw !== cpw) {
            e.preventDefault();
            showToast('Passwords do not match', 'error');
        } else if (pw.length < 8) {
            e.preventDefault();
            showToast('Password must be at least 8 characters', 'error');
        }
    });
}

/* ---- Live Search ---- */
function initSearch() {
    const input = document.getElementById('navSearchInput');
    const dropdown = document.getElementById('searchDropdown');
    if (!input || !dropdown) return;

    let debounceTimer;
    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();
        if (q.length < 2) { dropdown.classList.remove('active'); return; }
        debounceTimer = setTimeout(() => {
            fetch(getSiteUrl() + '/api/search.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.results || data.results.length === 0) {
                    dropdown.innerHTML = '<div class="search-no-results">No results found</div>';
                } else {
                    dropdown.innerHTML = data.results.map(p =>
                        `<a href="${getSiteUrl()}/pages/product.php?id=${p.id}" class="search-result-item">
                            <img src="${getSiteUrl()}/img/${p.image}" alt="">
                            <div class="search-result-info">
                                <div class="name">${p.name}</div>
                                <div class="category">${p.category}</div>
                            </div>
                            <span class="search-result-price">$${parseFloat(p.price).toFixed(2)}</span>
                        </a>`
                    ).join('') + `<a href="${getSiteUrl()}/pages/search.php?q=${encodeURIComponent(q)}" class="search-view-all">View all results</a>`;
                }
                dropdown.classList.add('active');
            });
        }, 300);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = input.value.trim();
            if (q) window.location.href = getSiteUrl() + '/pages/search.php?q=' + encodeURIComponent(q);
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-search')) dropdown.classList.remove('active');
    });
}

/* ---- User Dropdown ---- */
function initUserDropdown() {
    const btn = document.getElementById('navUserBtn');
    const dropdown = document.getElementById('navUserDropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('active');
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-user-menu')) dropdown.classList.remove('active');
    });
}

/* ---- Favorites ---- */
function initFavorites() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-favorite');
        if (!btn) return;
        e.preventDefault();
        const productId = btn.dataset.productId;
        fetch(getSiteUrl() + '/api/favorites.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCSRFToken() },
            body: JSON.stringify({ product_id: productId, csrf_token: getCSRFToken() })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('active', data.favorited);
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Please log in to add favorites', 'error');
            }
        });
    });
}

/* ---- Product Tabs ---- */
function initProductTabs() {
    const tabs = document.querySelectorAll('.product-tab-btn');
    if (!tabs.length) return;
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.product-tab-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const panel = document.getElementById(tab.dataset.tab);
            if (panel) panel.classList.add('active');
        });
    });
}

/* ---- Star Rating Selector ---- */
function initStarSelector() {
    const selector = document.querySelector('.star-selector');
    if (!selector) return;
    const stars = selector.querySelectorAll('.star');
    const input = document.getElementById('ratingInput');

    stars.forEach((star, i) => {
        star.addEventListener('click', () => {
            const val = i + 1;
            if (input) input.value = val;
            stars.forEach((s, j) => s.classList.toggle('active', j < val));
        });
        star.addEventListener('mouseenter', () => {
            stars.forEach((s, j) => s.style.color = j <= i ? '#DAA520' : '#ddd');
        });
    });
    selector.addEventListener('mouseleave', () => {
        const val = input ? parseInt(input.value) || 0 : 0;
        stars.forEach((s, j) => { s.style.color = ''; s.classList.toggle('active', j < val); });
    });
}

/* ---- Newsletter ---- */
function initNewsletter() {
    const form = document.getElementById('newsletterForm');
    if (!form) return;
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = form.querySelector('input[name="email"]').value;
        fetch(getSiteUrl() + '/api/newsletter.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                form.style.display = 'none';
                document.getElementById('newsletterSuccess').style.display = 'block';
                showToast('Subscribed! Welcome to the family.', 'success');
            } else {
                showToast(data.message || 'Already subscribed!', 'error');
            }
        });
    });
}

/* ---- Order History ---- */
function initOrderHistory() {
    document.addEventListener('click', (e) => {
        const header = e.target.closest('.order-card-header');
        if (!header) return;
        header.closest('.order-card').classList.toggle('expanded');
    });
}

/* ---- Custom Cake Builder ---- */
function initCakeBuilder() {
    const form = document.getElementById('cakeBuilderForm');
    if (!form) return;

    const preview = document.querySelector('.cake-preview');
    const priceEl = document.querySelector('.cake-price-estimate');
    const msgPreview = document.querySelector('.cake-message-preview');

    const prices = { '6"': 35, '8"': 50, '10"': 70, '12"': 95 };
    const tierMultiplier = { 1: 1, 2: 1.8, 3: 2.5 };
    const frostingColors = {
        'Buttercream': '#FFF5E1', 'Fondant': '#FFFFFF', 'Cream Cheese': '#FFFDE7',
        'Whipped Cream': '#FAFAFA', 'Chocolate': '#5C3317', 'Naked': '#D2A679'
    };

    // Option card selection
    document.querySelectorAll('.cake-option-card').forEach(card => {
        card.addEventListener('click', () => {
            const group = card.closest('.cake-option-cards');
            group.querySelectorAll('.cake-option-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            const input = document.getElementById(card.dataset.input);
            if (input) input.value = card.dataset.value;
            updateCakePreview();
        });
    });

    // Text inputs
    form.querySelectorAll('select, input').forEach(el => {
        el.addEventListener('change', updateCakePreview);
        el.addEventListener('input', updateCakePreview);
    });

    // Image upload
    const uploadZone = document.querySelector('.image-upload-zone');
    const fileInput = document.getElementById('referenceImage');
    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', () => fileInput.click());
        uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('dragover'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; showUploadPreview(fileInput); }
        });
        fileInput.addEventListener('change', () => showUploadPreview(fileInput));
    }

    function showUploadPreview(input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            let img = uploadZone.querySelector('.upload-preview');
            if (!img) { img = document.createElement('img'); img.className = 'upload-preview'; uploadZone.appendChild(img); }
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

    function updateCakePreview() {
        const size = form.querySelector('[name="size"]')?.value || '8"';
        const tiers = parseInt(form.querySelector('[name="tiers"]')?.value || 1);
        const frosting = form.querySelector('[name="frosting"]')?.value || 'Buttercream';
        const message = form.querySelector('[name="cake_message"]')?.value || '';

        // Update tiers
        if (preview) {
            const tierEls = preview.querySelectorAll('.cake-tier');
            tierEls.forEach((t, i) => {
                t.style.display = i < tiers ? 'block' : 'none';
                t.style.backgroundColor = frostingColors[frosting] || '#FFF5E1';
            });
        }

        // Update message
        if (msgPreview) msgPreview.textContent = message;

        // Update price
        const basePrice = prices[size] || 50;
        const total = basePrice * (tierMultiplier[tiers] || 1);
        if (priceEl) priceEl.textContent = '$' + total.toFixed(2);

        // Update hidden price
        const priceInput = form.querySelector('[name="estimated_price"]');
        if (priceInput) priceInput.value = total.toFixed(2);
    }

    updateCakePreview();
}

/* ---- Discount Code ---- */
function initDiscountCode() {
    const btn = document.getElementById('applyDiscountBtn');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const input = document.getElementById('discountInput');
        const code = input.value.trim().toUpperCase();
        if (!code) return;

        fetch(getSiteUrl() + '/api/discount.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCSRFToken() },
            body: JSON.stringify({ code: code, csrf_token: getCSRFToken() })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.classList.add('valid');
                input.classList.remove('invalid');
                const discountRow = document.getElementById('discountRow');
                if (discountRow) { discountRow.style.display = 'flex'; discountRow.querySelector('span:last-child').textContent = '-' + data.discount_display; }
                const totalEl = document.getElementById('cartTotal');
                if (totalEl) totalEl.textContent = data.new_total;
                const hiddenInput = document.getElementById('discountCodeHidden');
                if (hiddenInput) hiddenInput.value = code;
                showToast('Discount applied: ' + data.description, 'success');
                btn.disabled = true;
                btn.textContent = 'Applied';
            } else {
                input.classList.add('invalid');
                input.classList.remove('valid');
                showToast(data.message || 'Invalid code', 'error');
            }
        });
    });
}

/* ---- Delivery Slots ---- */
function initDeliverySlots() {
    const dateInput = document.getElementById('deliveryDate');
    const slotsGrid = document.getElementById('deliverySlotsGrid');
    const slotInput = document.getElementById('deliverySlotHidden');
    if (!dateInput || !slotsGrid) return;

    dateInput.addEventListener('change', () => {
        const date = dateInput.value;
        if (!date) return;
        slotsGrid.innerHTML = '<div class="skeleton skeleton-text" style="width:100%;height:40px"></div>';
        fetch(getSiteUrl() + '/api/delivery_slots.php?date=' + date)
        .then(r => r.json())
        .then(data => {
            if (data.slots && data.slots.length > 0) {
                slotsGrid.innerHTML = data.slots.map(s =>
                    `<button type="button" class="delivery-slot-btn${s.available ? '' : ' full'}" data-slot="${s.slot_time}">${s.slot_time}${s.available ? '' : ' (Full)'}</button>`
                ).join('');
            } else {
                slotsGrid.innerHTML = '<p style="color:var(--color-text-muted);font-size:0.9rem">No slots available for this date</p>';
            }
        });
    });

    slotsGrid.addEventListener('click', (e) => {
        const btn = e.target.closest('.delivery-slot-btn:not(.full)');
        if (!btn) return;
        slotsGrid.querySelectorAll('.delivery-slot-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        if (slotInput) slotInput.value = btn.dataset.slot;
    });
}

/* ---- Payment Flow ---- */
function initPaymentFlow() {
    const cardInput = document.getElementById('cardNumber');
    const expiryInput = document.getElementById('cardExpiry');
    const cvcInput = document.getElementById('cardCvc');
    const payBtn = document.getElementById('payBtn');
    if (!cardInput) return;

    // Card number formatting
    cardInput.addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 16);
        e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
        // Brand detection
        const brandEl = document.querySelector('.card-brand-icon');
        if (brandEl) {
            if (v.startsWith('4')) brandEl.textContent = '💳 Visa';
            else if (/^5[1-5]/.test(v)) brandEl.textContent = '💳 MC';
            else if (/^3[47]/.test(v)) brandEl.textContent = '💳 Amex';
            else brandEl.textContent = '💳';
        }
    });

    if (expiryInput) {
        expiryInput.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
            e.target.value = v;
        });
    }

    if (cvcInput) {
        cvcInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    if (payBtn) {
        payBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const num = cardInput.value.replace(/\s/g, '');
            if (num.length < 16) { showToast('Please enter a valid card number', 'error'); return; }
            payBtn.classList.add('processing');
            setTimeout(() => {
                payBtn.classList.remove('processing');
                payBtn.classList.add('success');
                setTimeout(() => {
                    const form = document.getElementById('checkoutForm');
                    if (form) form.submit();
                }, 1000);
            }, 2000);
        });
    }
}

/* ---- Confetti ---- */
function initConfetti() {
    const el = document.querySelector('.order-confirmed');
    if (!el || typeof confetti === 'undefined') return;
    const colors = ['#DAA520', '#E8B0AF', '#FDDAC4', '#8B4513', '#D2691E'];
    setTimeout(() => confetti({ particleCount: 80, spread: 70, origin: { y: 0.6 }, colors: colors }), 300);
    setTimeout(() => confetti({ particleCount: 50, angle: 60, spread: 55, origin: { x: 0 }, colors: colors }), 700);
    setTimeout(() => confetti({ particleCount: 50, angle: 120, spread: 55, origin: { x: 1 }, colors: colors }), 1100);
}
