<?php
$currentPage = 'products';
require_once __DIR__ . '/../includes/admin_header.php';
$conn = getDBConnection();
$products = getAllProductsAdmin($conn);

// Get distinct categories for the select
$catStmt = $conn->prepare("SELECT DISTINCT category FROM products ORDER BY category");
$catStmt->execute();
$categories = $catStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-top-bar">
    <h1 class="admin-page-title" style="margin-bottom:0">Products</h1>
    <button class="admin-btn admin-btn-primary" id="addProductBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </button>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Available</th>
                <th>Seasonal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr data-id="<?= (int)$p['id'] ?>">
                <td><img class="product-thumb" src="<?= SITE_URL ?>/img/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>"></td>
                <td><?= sanitize($p['name']) ?></td>
                <td><?= sanitize($p['category']) ?></td>
                <td><?= formatPrice($p['price']) ?></td>
                <td><span class="avail-dot <?= $p['is_available'] ? 'available' : 'unavailable' ?>"></span></td>
                <td><?= $p['is_seasonal'] ? 'Yes' : '—' ?></td>
                <td>
                    <div class="admin-actions-cell">
                        <button class="admin-btn admin-btn-primary admin-btn-sm edit-product-btn"
                            data-product='<?= sanitize(json_encode($p)) ?>'>Edit</button>
                        <button class="admin-btn admin-btn-danger admin-btn-sm delete-product-btn"
                            data-id="<?= (int)$p['id'] ?>">Delete</button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Product Modal -->
<div class="admin-modal-overlay" id="productModal">
    <div class="admin-modal">
        <button class="admin-modal-close" id="productModalClose">&times;</button>
        <h2 id="productModalTitle">Add Product</h2>
        <form id="productForm">
            <input type="hidden" name="action" id="productAction" value="create_product">
            <input type="hidden" name="product_id" id="productId" value="">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">

            <div class="admin-form-group">
                <label for="prodName">Name</label>
                <input type="text" id="prodName" name="name" required>
            </div>
            <div class="admin-form-group">
                <label for="prodDesc">Description</label>
                <textarea id="prodDesc" name="description"></textarea>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="prodPrice">Price</label>
                    <input type="number" step="0.01" id="prodPrice" name="price" required>
                </div>
                <div class="admin-form-group">
                    <label for="prodCategory">Category</label>
                    <select id="prodCategory" name="category" required>
                        <option value="">Select...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= sanitize($cat['category']) ?>"><?= sanitize(ucfirst($cat['category'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="admin-form-group">
                <label for="prodImage">Image filename</label>
                <input type="text" id="prodImage" name="image" placeholder="e.g. croissant.jpg">
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="prodAllergens">Allergens</label>
                    <input type="text" id="prodAllergens" name="allergens" placeholder="e.g. gluten, dairy">
                </div>
                <div class="admin-form-group">
                    <label for="prodNutrition">Nutrition Tags</label>
                    <input type="text" id="prodNutrition" name="nutrition_tags" placeholder="e.g. high-protein">
                </div>
            </div>
            <div class="admin-form-group admin-checkbox">
                <input type="checkbox" id="prodSeasonal" name="is_seasonal" value="1">
                <label for="prodSeasonal">Seasonal Product</label>
            </div>
            <div class="admin-form-group" id="seasonalFields" style="display:none">
                <div class="admin-form-group">
                    <label for="prodSeasonalLabel">Seasonal Label</label>
                    <input type="text" id="prodSeasonalLabel" name="seasonal_label" placeholder="e.g. Summer Special">
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="prodSeasonalStart">Start Date</label>
                        <input type="date" id="prodSeasonalStart" name="seasonal_start">
                    </div>
                    <div class="admin-form-group">
                        <label for="prodSeasonalEnd">End Date</label>
                        <input type="date" id="prodSeasonalEnd" name="seasonal_end">
                    </div>
                </div>
            </div>
            <button type="submit" class="admin-btn admin-btn-primary" style="width:100%;justify-content:center;margin-top:8px;">Save Product</button>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/admin_footer.php';
?>
