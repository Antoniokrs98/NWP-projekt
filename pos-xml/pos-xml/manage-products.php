<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$productsXml = simplexml_load_file('products.xml');
$products = $productsXml->product;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addProduct'])) {
        $newProduct = $productsXml->addChild('product');
        $newProduct->addChild('productName', $_POST['productName']);
        $newProduct->addChild('productPrice', $_POST['productPrice']);
        $productsXml->asXML('products.xml');
        header("Location: manage-products.php");
        exit;
    } elseif (isset($_POST['deleteProduct'])) {
        foreach ($productsXml->product as $product) {
            if ($product->productName == $_POST['productName']) {
                $dom = dom_import_simplexml($product);
                $dom->parentNode->removeChild($dom);
                $productsXml->asXML('products.xml');
                break;
            }
        }
        header("Location: manage-products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="dashboard.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
<section id="sidebar">
    <div id="sidebar-nav">
        <ul>
            <li class="active"><a href="manage-products.php"><i class="fa fa-box"></i> Manage Products</a></li>
            <li><a href="manage-receipts.php"><i class="fa fa-receipt"></i> Create Receipt</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>
</section>

<section id="content">
    <div class="content">


        <div class="containertable">
            <h2>Add New Product</h2>
            <form method="post">
                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" class="form-control" id="productName" name="productName" required>
                </div>
                <div class="form-group">
                    <label for="productPrice">Product Price:</label>
                    <input type="number" class="form-control" id="productPrice" name="productPrice" required>
                </div>
                <button type="submit" class="btn btn-primary" name="addProduct">Add Product</button>
            </form>
            <br>
            <table id="productsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product->productName) ?></td>
                        <td><?= htmlspecialchars($product->productPrice) ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="productName" value="<?= htmlspecialchars($product->productName) ?>">
                                <button type="submit" class="btn btn-danger" name="deleteProduct">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        "pageLength": 5
    });
});
</script>
</body>
</html>
