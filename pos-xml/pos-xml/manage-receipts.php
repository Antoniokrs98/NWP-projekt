<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$productsXml = simplexml_load_file('products.xml');
$products = $productsXml->product;

$receiptsXml = simplexml_load_file('receipts.xml');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['createReceipt'])) {
        $newReceipt = $receiptsXml->addChild('receipt');
        $newReceipt->addChild('customerName', $_POST['customerName']);
        $newReceipt->addChild('product', implode(", ", $_POST['product']));
        $newReceipt->addChild('quantity', implode(", ", $_POST['quantity']));
        $newReceipt->addChild('totalPrice', $_POST['totalPrice']);
        $receiptsXml->asXML('receipts.xml');
        header("Location: manage-receipts.php");
        exit;
    } elseif (isset($_POST['deleteReceipt'])) {
        foreach ($receiptsXml->receipt as $receipt) {
            if ($receipt->customerName == $_POST['customerName'] && $receipt->product == $_POST['product']) {
                $dom = dom_import_simplexml($receipt);
                $dom->parentNode->removeChild($dom);
                $receiptsXml->asXML('receipts.xml');
                break;
            }
        }
        header("Location: manage-receipts.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Receipt</title>
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
            <li><a href="manage-products.php"><i class="fa fa-box"></i> Manage Products</a></li>
            <li class="active"><a href="manage-receipts.php"><i class="fa fa-receipt"></i> Create Receipt</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>
</section>

<section id="content">
    <div class="content">
        <div class="containertable">
            <h2>Create New Receipt</h2>
<form method="post" id="receiptForm">
    <div class="form-group">
        <label for="customerName">Customer Name:</label>
        <input type="text" class="form-control" id="customerName" name="customerName" required>
    </div>
    <div id="productsContainer">
        <div class="form-group product-item d-flex align-items-center">
            <div style="flex: 1;">
                <label for="product">Product:</label>
                <select class="form-control product-select" name="product[]" required>
                    <option value="" data-price="0" data-quantity="0">Select a product</option>
                    <?php foreach ($products as $product): ?>
                    <option value="<?= htmlspecialchars($product->productName) ?>" data-price="<?= htmlspecialchars($product->productPrice) ?>" data-quantity="<?= htmlspecialchars($product->productQuantity) ?>"><?= htmlspecialchars($product->productName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1;">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control quantity-input" name="quantity[]" min="1" required>
            </div>
            <div style="flex: 0;">
                <button type="button" class="btn btn-danger remove-product d-flex align-items-center" style="margin-top: 30px;">
                    <i class="fa fa-trash"></i> <span style="margin-left: 5px;">Remove</span>
                </button>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary d-flex align-items-center" id="addProduct">
        <i class="fa fa-plus"></i> <span style="margin-left: 5px;">Add Another Product</span>
    </button>
    <div class="form-group">
        <label for="totalPrice">Total Price:</label>
        <input type="number" class="form-control" id="totalPrice" name="totalPrice" readonly required>
    </div>
    <button type="submit" class="btn btn-primary" name="createReceipt">Create Receipt</button>
</form>

            <br>
            <table id="receiptsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receiptsXml->receipt as $receipt): ?>
                    <tr>
                        <td><?= htmlspecialchars($receipt->customerName) ?></td>
                        <td><?= htmlspecialchars($receipt->product) ?></td>
                        <td><?= htmlspecialchars($receipt->quantity) ?></td>
                        <td><?= htmlspecialchars($receipt->totalPrice) ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="customerName" value="<?= htmlspecialchars($receipt->customerName) ?>">
                                <input type="hidden" name="product" value="<?= htmlspecialchars($receipt->product) ?>">
                                <button type="submit" class="btn btn-danger" name="deleteReceipt">Delete</button>
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
    $('#receiptsTable').DataTable({
        "pageLength": 5
    });

    function calculateTotalPrice() {
        let totalPrice = 0;
        $('.product-item').each(function() {
            const productSelect = $(this).find('.product-select');
            const quantityInput = $(this).find('.quantity-input');
            const price = parseFloat(productSelect.find('option:selected').data('price'));
            const quantity = parseInt(quantityInput.val());
            if (!isNaN(price) && !isNaN(quantity)) {
                totalPrice += price * quantity;
            }
        });
        $('#totalPrice').val(totalPrice.toFixed(2));
    }

    $('#productsContainer').on('change', '.product-select, .quantity-input', function() {
        calculateTotalPrice();
    });

    $('#productsContainer').on('click', '.remove-product', function() {
        $(this).closest('.product-item').remove();
        calculateTotalPrice();
    });

    $('#addProduct').click(function() {
        const productItem = `<div class="form-group product-item">
            <select class="form-control product-select" name="product[]" required>
                <option value="" data-price="0" data-quantity="0">Select a product</option>
                <?php foreach ($products as $product): ?>
                <option value="<?= htmlspecialchars($product->productName) ?>" data-price="<?= htmlspecialchars($product->productPrice) ?>" data-quantity="<?= htmlspecialchars($product->productQuantity) ?>"><?= htmlspecialchars($product->productName) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" class="form-control quantity-input" name="quantity[]" min="1" required>
            <button type="button" class="btn btn-danger remove-product">Remove</button>
        </div>`;
        $('#productsContainer').append(productItem);
    });

    $('#product').trigger('change'); 
});
</script>
</body>
</html>
