<?php include "functions.php" ?>

<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve form data
    $sku = $_POST['sku']; // Retrieve the product type from the form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $attribute = '';
    $measurementType = '';
    $measurement = '';


    if ($type === 'DVD' && isset($_POST['size'])) {
        $attribute = $_POST['size']; // If product type is DVD, attribute comes from 'size'
        $measurementType = 'Size';
        $measurement = 'MB';
    } elseif ($type === 'Book' && isset($_POST['weight'])) {
        $attribute = $_POST['weight']; // If product type is Book, attribute comes from 'weight'
        $measurementType = 'Weight';
        $measurement =  'Kg';
    } elseif ($type === 'Furniture' && isset($_POST['height']) && isset($_POST['width']) && isset($_POST['length'])) {
        // If product type is Furniture, attribute is a combination of height, width, and length
        $attribute = $_POST['height'] . 'x' . $_POST['width'] . 'x' . $_POST['length'];
        $measurementType = "Dimensions";
        $measurement = 'cm';
    }



    // Check if SKU already exists
    $check_sku = $conn->prepare("SELECT * FROM products WHERE SKU = ?");
    $check_sku->bind_param("s", $sku);
    $check_sku->execute();
    $result = $check_sku->get_result();
    if ($result->num_rows > 0) {
        echo '<script>alert("SKU already exists!"); window.location.href = "Products.php";</script>';
        exit(); // Exit to prevent further execution
    } else {
        // Insert new product if SKU doesn't exist
        $insert_product = $conn->prepare("INSERT INTO products (SKU, Name, Price, AttributeType, AttributeValue, MeasurementType, Measurement) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_product->bind_param("ssdssss", $sku, $name, $price, $type, $attribute, $measurementType, $measurement);


        if (!empty($sku) && !empty($name) && !empty($price) && !empty($type)) {
            // Execute SQL insert query only when form is submitted
            if ($insert_product->execute()) {
                header("Location: Products.php"); // Redirect to Product List page after adding
                exit();
            } else {
                echo "Error: " . $insert_product->error;
            }
        }
    }


    // Close prepared statements and database connection
    $check_sku->close();
    $insert_product->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productType').change(function() {
                var type = $(this).val();
                updateAttributeFields(type);
            });

            function updateAttributeFields(type) {
                var description = '';
                $('#size, #weight, #height, #width, #length').hide();

                switch (type) {
                    case 'DVD':
                        $('#size').show();
                        description = "Please provide size in MB";
                        break;
                    case 'Book':
                        $('#weight').show();
                        description = "Please provide weight in Kg";
                        break;
                    case 'Furniture':
                        $('#height, #width, #length').show();
                        description = "Please provide dimensions (HxWxL)";
                        break;
                }

                $('#attributeDesc').text(description);
            }

            $('#saveBtn').click(function() {
                var sku = $('#sku').val();
                var name = $('#name').val();
                var price = $('#price').val();
                var type = $('#productType').val();
                var attribute = '';


                switch (type) {
                    case 'DVD':
                        attribute = $('#size').val();
                        break;
                    case 'Book':
                        attribute = $('#weight').val();
                        break;
                    case 'Furniture':
                        var height = $('#height').val();
                        var width = $('#width').val();
                        var length = $('#length').val();
                        attribute = height + 'x' + width + 'x' + length;
                        break;
                }

                if (sku === '' || name === '' || price === '' || type === '' || attribute === '') {
                    alert('Please submit required data');
                    return false;
                }

                console.log(sku, name, price, type, attribute);
                // Validate attribute values based on type
                if ((type === 'DVD' && isNaN(attribute)) || (type === 'Book' && isNaN(attribute))) {
                    alert('Please provide the data of indicated type');
                    return false;
                }


                // If everything is validated, submit the form (Should be a PHP form submission)
                $('#productForm').submit();
                alert('Product saved successfully!');
                header("Location: Products.php");
                exit(); // Redirect to Product List page after adding

            });

            $('#cancelBtn').click(function() {
                window.location.href = 'Products.php';
            });
        });
    </script>
    <style>
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;

        }

        /* Style for buttons */
        .header-buttons {
            display: flex;
            margin-right: 20px;
        }

        .header-buttons button {
            margin-left: 10px;
        }


        /* Style for attribute container */
        .attributestyle {
            font-family: Arial, sans-serif;
            padding: 10px;
            width: 300px;
            display: flex;
            flex-wrap: wrap;
        }

        /* Style for attribute label */
        .attributestyle span {
            font-weight: bold;
            margin-right: 5px;
        }

        /* Style for input fields */
        .attributestyle input[type="text"] {
            flex: 1;
            padding: 8px;
            margin: 5px;
            box-sizing: border-box;
            border: none;
            outline: none;
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <header>
        <h1>Add Product</h1>
        <div class="header-buttons">
            <button type="submit" id="saveBtn">Save</button>

            <button type="button" id="cancelBtn">Cancel</button>
        </div>
    </header>
    <hr>

    <form id="productForm" method="POST" action="">
        SKU: <input type="text" id="sku" name="sku" required><br><br>
        Name: <input type="text" id="name" name="name" required><br><br>
        Price: <input type="text" id="price" name="price" required><br><br>
        Type:
        <select id="productType" name="type" required>
            <option value="">Select Type</option>
            <option value="DVD">DVD</option>
            <option value="Book">Book</option>
            <option value="Furniture">Furniture</option>
        </select><br><br>

        <input type="text" id="measurementType" name="measurementType" value="<?php echo $measurementType; ?>" style="display: none;">
        <input type="text" id="measurement" name="measurement" value="<?php echo $measurement; ?>" style="display: none;">
        <div class="attributestyle">
            <span id="attributeDesc"></span>
            <input type="text" id="size" name="size" placeholder="Size in MB" style="display: none;"><br><br>
            <input type="text" id="weight" name="weight" placeholder="Weight in Kg" style="display: none;"><br><br>
            <input type="text" id="height" name="height" placeholder="Height" style="display: none;">
            <input type="text" id="width" name="width" placeholder="Width" style="display: none;">
            <input type="text" id="length" name="length" placeholder="Length" style="display: none;"><br><br>
        </div>

    </form>
</body>

</html>