<?php include "functions.php" ?>

<?php

// / Check if request method is POST and 'selectedProducts' parameter is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedProducts'])) {
    
    // Get selected product IDs from POST data
    $selectedProducts = $_POST['selectedProducts'];

    // Delete selected products from the database
    foreach ($selectedProducts as $productId) {
        // Example query to delete products
        $sql = "DELETE FROM products WHERE id = $productId";
        if ($conn->query($sql) !== TRUE) {
            echo "Error deleting product with ID: $productId - " . $conn->error;
            exit();
        }
    }
    

    // Close the database connection
    $conn->close();
   
    // Echo success message
    echo "Selected products deleted successfully.";
} else {
    // Echo an error message for invalid request
    echo "Invalid request.";
}


?>



