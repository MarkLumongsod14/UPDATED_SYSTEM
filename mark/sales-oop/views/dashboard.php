<?php 
session_start();

// Ensure the user is logged in
if(empty($_SESSION)){
    header("location: ../views/");
    exit;
}

// Include the Product class
include "../classes/Product.php";

$product = new Product;

// Fetch all products
$product_list = $product->displayProducts();

// Handle add product request
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $product->addProduct($product_name, $price, $quantity); // Add product method
    header("Location: dashboard.php"); // Redirect after adding
    exit;
}

// Handle delete product request
if (isset($_GET['delete_id'])) {
    $product->deleteProduct($_GET['delete_id']); // Delete product method
    header("Location: dashboard.php"); // Redirect after deleting
    exit;
}

// Handle edit product request
$product_to_edit = null; // Initialize variable for editing
if (isset($_GET['edit_id'])) {
    //$product_to_edit = $product->getProductById($_GET['edit_id']); // Fetch product for editing
}

// Handle buy product request
if (isset($_POST['buy_product'])) {
    $product_id = $_POST['product_id'];
    $buy_quantity = $_POST['buy_quantity'];
    //$product->buyProduct($product_id, $buy_quantity); // Add buy product method
    header("Location: dashboard.php"); // Redirect after purchase
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar.min.css">

    <style>
        body {
            background-image: url('img/bgpic.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
        }

        .no-records {
            font-size: 1.5rem; /* Smaller font for "No Records Found" */
        }
    </style>
</head>
<body>
    <!-- Static Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                </ul>
                <div class="ms-auto">
                    <a href="../actions/logout.php" class="btn btn-outline-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-expand-lg navbar-light bg-white justify-content-between">
        <a href="dashboard.php" class="ms-3" title="Home">
            <i class="fa-solid fa-house fa-2x text-dark"></i>
        </a>
        <a href="profile.php" class="nav-link text-secondary">
            <span class="fw-bold fs-5">Welcome, <?= ucfirst($_SESSION['username']) ?></span>
        </a>
        <a href="../actions/logout.php" class="me-3" title="Logout">
            <i class="fa-solid fa-user-xmark fa-2x text-danger"></i>
        </a>
    </nav>

    <div class="container mt-5">
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= date('l') . ', ' . date('d M Y') ?></h5>
                        <h1 class="display-4"><?= date('d') ?></h1>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" style="background-color:#245580;">
                    <div class="card-body text-center text-white">
                        <h5 class="card-title">Total Revenue</h5>
                        <h1 class="display-4">₱0</h1> <!-- Placeholder for total revenue -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text display-4">0</p> <!-- Placeholder for total orders -->
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Low Stock</h5>
                        <p class="card-text display-4">0</p> <!-- Placeholder for low stock -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="add-product" tabindex="-1" aria-labelledby="registration" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <h1 class="display-4 fw-bold text-info text-center"><i class="fa-solid fa-box"></i> Add Product</h1>
                        <form action="dashboard.php" method="post" class="w-75 mx-auto pt-4">
                            <div class="row mb-3">
                                <div class="col-md">
                                    <label for="product-name" class="form-label small text-secondary">Product Name</label>
                                    <input type="text" name="product_name" id="product-name" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label small text-secondary">Price</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="price-tag">$</span>
                                        <input type="number" name="price" id="price" class="form-control" aria-describedby="price-tag" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label small text-secondary">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md">
                                    <button type="submit" class="btn btn-info w-100" name="add_product">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <?php if (isset($product_to_edit)): ?>
        <div class="modal fade" id="edit-product" tabindex="-1" aria-labelledby="editProduct" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <h1 class="display-4 fw-bold text-warning text-center"><i class="fa-solid fa-box"></i> Edit Product</h1>
                        <form action="dashboard.php?edit_id=<?= $product_to_edit['id'] ?>" method="post" class="w-75 mx-auto pt-4">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($product_to_edit['id']) ?>">
                            <div class="row mb-3">
                                <div class="col-md">
                                    <label for="edit-product-name" class="form-label small text-secondary">Product Name</label>
                                    <input type="text" name="product_name" id="edit-product-name" class="form-control" value="<?= htmlspecialchars($product_to_edit['product_name']) ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit-price" class="form-label small text-secondary">Price</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="edit-price-tag">$</span>
                                        <input type="number" name="price" id="edit-price" class="form-control" aria-describedby="edit-price-tag" value="<?= htmlspecialchars($product_to_edit['price']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit-quantity" class="form-label small text-secondary">Quantity</label>
                                    <input type="number" name="quantity" id="edit-quantity" class="form-control" value="<?= htmlspecialchars($product_to_edit['quantity']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md">
                                    <button type="submit" class="btn btn-warning w-100" name="edit_product">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Buy Product Modal -->
        <div class="modal fade" id="buy-product" tabindex="-1" aria-labelledby="buyProduct" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <h1 class="display-4 fw-bold text-success text-center"><i class="fa-solid fa-shopping-cart"></i> Buy Product</h1>
                        <form action="dashboard.php" method="post" class="w-75 mx-auto pt-4">
                            <input type="hidden" name="product_id" id="buy-product-id">
                            <div class="row mb-3">
                                <div class="col-md">
                                    <label for="buy_quantity" class="form-label small text-secondary">Quantity</label>
                                    <input type="number" name="buy_quantity" id="buy_quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md">
                                    <button type="submit" class="btn btn-success w-100" name="buy_product">Buy</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Products</h4>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#add-product">
                    <i class="fa-solid fa-plus"></i> Add Product
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($product_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center no-records">No Records Found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($product_list as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td>$<?= htmlspecialchars($item['price']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#buy-product" data-id="<?= $item['id'] ?>">
                                            <i class="fa-solid fa-shopping-cart"></i> Buy
                                        </button>
                                        <a href="?edit_id=<?= $item['id'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit-product">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete_id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
        // Handle the Buy Product modal
        const buyProductButtons = document.querySelectorAll('[data-bs-target="#buy-product"]');
        buyProductButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                document.getElementById('buy-product-id').value = productId;
            });
        });
    </script>
</body>
</html>
