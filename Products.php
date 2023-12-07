<?php include "functions.php" ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <title>Product List</title> -->
    <!-- <h1>Product List</h1> -->
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- <title>Product List</title> -->
    <style>
        /* Style for header */
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

        /* Style for product squares */
        .product {
            width: 23%;
            border: 1px solid #ccc;
            margin: 8px;
            padding: 30px 10px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            position: relative;
            line-height: 0.5;
            /* Adjust line height */

        }

        /* Style for checkbox */
        .delete-checkbox {
            position: absolute;
            top: 5px;
            /* Adjust top position */
            left: 5px;
            /* Adjust left position */
        }

        /* Style for name and price */
        .name {
            font-weight: bold;
        }

        .price {
            color: green;
        }

        /* Style for measurement information */
        .measurement-info {
            font-style: italic;
        }
    </style>
</head>

<body>
    <header>
        <h1>Product List</h1>
        <div class="header-buttons">
            <form method="POST" action="products_add.php">
                <button id="add-button">ADD</button>
            </form>

            <button id="mass-delete-button" onclick="deleteProducts()">MASS DELETE</button>
        </div>
    </header>
    <hr>


    <?php
    // Fetch products from database
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);



    if ($result->num_rows > 0) {


        while ($row = $result->fetch_assoc()) {
            echo "<div class='product'>";
            echo "<input type='checkbox' class='delete-checkbox'data-id='" . $row['id'] . "'>";
            echo "<p>" . $row['SKU'] . "</p>";
            echo "<p class='name'> " . $row['Name'] . "</p>";
            echo "<p class ='price'>$" . $row['Price'] . "</p>";
            echo "<p class='measurement-info'>" . $row['MeasurementType'] . ": " . $row['AttributeValue'] . " " . $row['Measurement'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>



    <script>
        document.getElementById("add-button").addEventListener("click", function() {
            window.location.href = "Products.php";
        });

        function deleteProducts() {
            const checkboxes = document.querySelectorAll('.delete-checkbox');
            const selectedProducts = [];
            

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedProducts.push(checkbox.dataset.id);
                }
            });

            if (selectedProducts.length === 0) {
                alert("Please select products to delete.");
                return;
            }
            if (confirm) {
                $.ajax({
                    type: 'POST',
                    url: 'delete_box.php',
                    data: {
                        selectedProducts: selectedProducts
                    },
                    success: function(response) {
                        // alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert("Error occurred while deleting products.");
                    }
                });
            }
        }
    </script>
<!-- ("Are you sure you want to delete selected products?") -->


    <!-- Bootstrap and other JS libraries -->


    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>
