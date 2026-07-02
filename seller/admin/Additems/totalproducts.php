<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../TheSpiceNepal/frontend/login.html");
    exit;
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sample data for demonstration (since PostgreSQL extension is not available)
$products = [
    [
        'id' => 1,
        'name' => 'Organic Turmeric Powder',
        'category' => 'Spices',
        'price' => 15.99,
        'old_price' => 19.99,
        'image_path' => '../img/turmeric.jpg',
        'in_stock' => true,
        'on_sale' => true
    ],
    [
        'id' => 2,
        'name' => 'Black Pepper Whole',
        'category' => 'Spices',
        'price' => 12.50,
        'old_price' => null,
        'image_path' => '../img/Black-peper.jpg',
        'in_stock' => true,
        'on_sale' => false
    ],
    [
        'id' => 3,
        'name' => 'Cinnamon Sticks',
        'category' => 'Spices',
        'price' => 8.99,
        'old_price' => 10.99,
        'image_path' => '../img/vietnamese-cinnamon.jpg',
        'in_stock' => false,
        'on_sale' => true
    ],
    [
        'id' => 4,
        'name' => 'Cardamom Pods',
        'category' => 'Spices',
        'price' => 25.00,
        'old_price' => 30.00,
        'image_path' => '../img/Black-cardamon-elaichi.jpg',
        'in_stock' => true,
        'on_sale' => true
    ],
    [
        'id' => 5,
        'name' => 'Saffron Threads',
        'category' => 'Herbs',
        'price' => 45.99,
        'old_price' => null,
        'image_path' => '../img/saffron10.jpg',
        'in_stock' => true,
        'on_sale' => false
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stock-high { color: #198754; }
        .stock-medium { color: #ffc107; }
        .stock-low { color: #dc3545; }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .action-buttons .btn {
            margin: 0 2px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-boxes"></i> Total Products</h2>
            </div>
            <div class="col-md-4">
                <a href="adminadd.html" class="btn btn-primary float-end">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>

        <!-- Filter by Category -->
        <div class="mb-3">
            <form method="GET" class="d-inline">
                <select name="category" class="form-select d-inline-block w-auto" onchange="this.form.submit();">
                    <option value="">All Categories</option>
                    <?php
                    // Get unique categories
                    $categories = array_unique(array_column($products, 'category'));
                    foreach ($categories as $cat) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat) ? 'selected' : '';
                        echo "<option value='$cat' $selected>$cat</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Old Price</th>
                        <th>Stock Status</th>
                        <th>On Sale</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            // Filter by category if selected
                            if (isset($_GET['category']) && !empty($_GET['category']) && $product['category'] != $_GET['category']) {
                                continue;
                            }

                            // Determine stock status (assuming in_stock is boolean)
                            $stockStatus = $product['in_stock'] ? 'In Stock' : 'Out of Stock';
                            $stockClass = $product['in_stock'] ? 'stock-high' : 'stock-low';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td>
                                    <?php if ($product['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                                             alt="Product Image" class="product-image">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($product['category']); ?></span></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php if ($product['old_price']): ?>
                                        $<?php echo number_format($product['old_price'], 2); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $stockClass; ?>">
                                        <?php echo $stockStatus; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['on_sale']): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-sm btn-warning" onclick="editProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo count($products); ?></h5>
                        <p class="card-text">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo count(array_filter($products, function($p) { return $p['in_stock']; })); ?>
                        </h5>
                        <p class="card-text">In Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo count(array_filter($products, function($p) { return $p['on_sale']; })); ?>
                        </h5>
                        <p class="card-text">On Sale</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php
                            $categories = array_unique(array_column($products, 'category'));
                            echo count($categories);
                            ?>
                        </h5>
                        <p class="card-text">Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="editProductId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="editName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" class="form-control" id="editCategory" name="category" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control" id="editPrice" name="price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Old Price (Optional)</label>
                                    <input type="number" class="form-control" id="editOldPrice" name="oldPrice" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                            <div id="currentImage" class="mt-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="editOnSale" name="onSale">
                                    <label class="form-check-label">On Sale</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="editInStock" name="inStock">
                                    <label class="form-check-label">In Stock</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(productId) {
            // Fetch product data
            fetch(`fetch_product.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const product = data[0];
                        document.getElementById('editProductId').value = product.id;
                        document.getElementById('editName').value = product.name;
                        document.getElementById('editCategory').value = product.category;
                        document.getElementById('editPrice').value = product.price;
                        document.getElementById('editOldPrice').value = product.old_price || '';
                        document.getElementById('editOnSale').checked = product.on_sale;
                        document.getElementById('editInStock').checked = product.in_stock;

                        // Show current image
                        const currentImageDiv = document.getElementById('currentImage');
                        if (product.image_path) {
                            currentImageDiv.innerHTML = `<img src="${product.image_path}" class="product-image" alt="Current Image">`;
                        } else {
                            currentImageDiv.innerHTML = '<span class="text-muted">No image</span>';
                        }

                        // Show modal
                        new bootstrap.Modal(document.getElementById('editModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error fetching product:', error);
                    alert('Error loading product data');
                });
        }

        function deleteProduct(productId, productName) {
            if (confirm(`Are you sure you want to delete "${productName}"?`)) {
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully');
                        location.reload();
                    } else {
                        alert('Error deleting product: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting product');
                });
            }
        }

        // Handle edit form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product updated successfully');
                    location.reload();
                } else {
                    alert('Error updating product: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating product');
            });
        });
    </script>
</body>
</html>
