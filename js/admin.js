/* ============================================
   BAKE'N FLAKE — Admin JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
    initAdminDashboard();
    initAnimatedCounters();
    initAdminModals();
    initAdminStatusChange();
    initAdminProductForm();
    initAdminDelete();
});

/* ---- CSRF Token ---- */
function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/* ---- Dashboard Charts ---- */
function initAdminDashboard() {
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const labels = JSON.parse(revenueCanvas.dataset.labels || '[]');
        const values = JSON.parse(revenueCanvas.dataset.values || '[]');
        const ctx = revenueCanvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(218, 165, 32, 0.3)');
        gradient.addColorStop(1, 'rgba(218, 165, 32, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: values,
                    borderColor: '#DAA520',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#DAA520',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => '$' + ctx.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => '$' + v.toLocaleString()
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const labels = JSON.parse(categoryCanvas.dataset.labels || '[]');
        const values = JSON.parse(categoryCanvas.dataset.values || '[]');

        const bakeryColors = [
            '#DAA520', '#8B4513', '#D2691E', '#CD853F',
            '#DEB887', '#F4A460', '#A0522D', '#BC8F8F'
        ];

        new Chart(categoryCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{
                    data: values,
                    backgroundColor: bakeryColors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' }
                    }
                },
                cutout: '60%'
            }
        });
    }
}

/* ---- Animated Counters ---- */
function initAnimatedCounters() {
    const counters = document.querySelectorAll('.counter-animate');
    if (!counters.length) return;

    counters.forEach(el => {
        const target = parseFloat(el.dataset.target) || 0;
        const isFloat = target % 1 !== 0;
        const duration = 1500;
        const startTime = performance.now();

        function easeOutExpo(t) {
            return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        }

        function tick(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutExpo(progress);
            const current = eased * target;

            if (isFloat) {
                el.textContent = current.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                el.textContent = Math.floor(current).toLocaleString();
            }

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        }

        requestAnimationFrame(tick);
    });
}

/* ---- Modals ---- */
function initAdminModals() {
    const modal = document.getElementById('productModal');
    if (!modal) return;

    const addBtn = document.getElementById('addProductBtn');
    const closeBtn = document.getElementById('productModalClose');
    const form = document.getElementById('productForm');
    const seasonalCheck = document.getElementById('prodSeasonal');
    const seasonalFields = document.getElementById('seasonalFields');

    // Toggle seasonal fields
    if (seasonalCheck) {
        seasonalCheck.addEventListener('change', () => {
            seasonalFields.style.display = seasonalCheck.checked ? 'block' : 'none';
        });
    }

    function openModal(isEdit, product) {
        document.getElementById('productModalTitle').textContent = isEdit ? 'Edit Product' : 'Add Product';
        document.getElementById('productAction').value = isEdit ? 'update_product' : 'create_product';
        document.getElementById('productId').value = isEdit ? product.id : '';
        document.getElementById('prodName').value = isEdit ? product.name : '';
        document.getElementById('prodDesc').value = isEdit ? product.description || '' : '';
        document.getElementById('prodPrice').value = isEdit ? product.price : '';
        document.getElementById('prodCategory').value = isEdit ? product.category : '';
        document.getElementById('prodImage').value = isEdit ? product.image || '' : '';
        document.getElementById('prodAllergens').value = isEdit ? product.allergens || '' : '';
        document.getElementById('prodNutrition').value = isEdit ? product.nutrition_tags || '' : '';

        const isSeasonal = isEdit && product.is_seasonal == 1;
        seasonalCheck.checked = isSeasonal;
        seasonalFields.style.display = isSeasonal ? 'block' : 'none';
        document.getElementById('prodSeasonalLabel').value = isEdit ? product.seasonal_label || '' : '';
        document.getElementById('prodSeasonalStart').value = isEdit ? product.seasonal_start || '' : '';
        document.getElementById('prodSeasonalEnd').value = isEdit ? product.seasonal_end || '' : '';

        modal.classList.add('open');
    }

    function closeModal() {
        modal.classList.remove('open');
        form.reset();
        seasonalFields.style.display = 'none';
    }

    if (addBtn) addBtn.addEventListener('click', () => openModal(false));
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // Edit buttons
    document.querySelectorAll('.edit-product-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const product = JSON.parse(btn.dataset.product);
            openModal(true, product);
        });
    });
}

/* ---- Status Change (Orders + Reservations) ---- */
function initAdminStatusChange() {
    document.addEventListener('change', e => {
        if (e.target.matches('.order-status-select')) {
            const orderId = e.target.dataset.id;
            const status = e.target.value;
            fetch(getSiteURL() + '/api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_order_status',
                    order_id: orderId,
                    status: status,
                    csrf_token: getCSRFToken()
                })
            })
            .then(r => r.json())
            .then(data => {
                showAdminToast(data.message, data.success);
            })
            .catch(() => showAdminToast('Network error.', false));
        }

        if (e.target.matches('.reservation-status-select')) {
            const resId = e.target.dataset.id;
            const status = e.target.value;
            fetch(getSiteURL() + '/api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_reservation_status',
                    reservation_id: resId,
                    status: status,
                    csrf_token: getCSRFToken()
                })
            })
            .then(r => r.json())
            .then(data => {
                showAdminToast(data.message, data.success);
            })
            .catch(() => showAdminToast('Network error.', false));
        }
    });
}

/* ---- Product Form Submit ---- */
function initAdminProductForm() {
    const form = document.getElementById('productForm');
    if (!form) return;

    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = {};
        formData.forEach((val, key) => { data[key] = val; });
        data.csrf_token = getCSRFToken();

        // Handle checkbox: if not checked, FormData won't include it
        if (!formData.has('is_seasonal')) {
            data.is_seasonal = 0;
        }

        fetch(getSiteURL() + '/api/admin_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(result => {
            showAdminToast(result.message, result.success);
            if (result.success) {
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => showAdminToast('Network error.', false));
    });
}

/* ---- Delete Product ---- */
function initAdminDelete() {
    document.addEventListener('click', e => {
        const btn = e.target.closest('.delete-product-btn');
        if (!btn) return;

        if (!confirm('Are you sure you want to remove this product?')) return;

        const productId = btn.dataset.id;
        fetch(getSiteURL() + '/api/admin_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete_product',
                product_id: productId,
                csrf_token: getCSRFToken()
            })
        })
        .then(r => r.json())
        .then(data => {
            showAdminToast(data.message, data.success);
            if (data.success) {
                const row = btn.closest('tr');
                if (row) row.remove();
            }
        })
        .catch(() => showAdminToast('Network error.', false));
    });

    // Toggle admin
    document.addEventListener('click', e => {
        const btn = e.target.closest('.toggle-admin-btn');
        if (!btn) return;

        if (!confirm('Are you sure you want to change this user\'s admin status?')) return;

        const userId = btn.dataset.id;
        fetch(getSiteURL() + '/api/admin_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_admin',
                user_id: userId,
                csrf_token: getCSRFToken()
            })
        })
        .then(r => r.json())
        .then(data => {
            showAdminToast(data.message, data.success);
            if (data.success) {
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => showAdminToast('Network error.', false));
    });
}

/* ---- Helpers ---- */
function getSiteURL() {
    // Derive from the current page path
    const path = window.location.pathname;
    const idx = path.indexOf('/Bakery');
    return idx !== -1 ? path.substring(0, idx + 7) : '/Bakery';
}

function showAdminToast(message, success) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; bottom: 24px; right: 24px;
        padding: 14px 24px; border-radius: 10px;
        color: #fff; font-size: 0.9rem; font-weight: 600;
        z-index: 9999; opacity: 0; transition: opacity 0.3s;
        background: ${success ? '#16a34a' : '#dc2626'};
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    `;
    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.opacity = '1'; });
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
