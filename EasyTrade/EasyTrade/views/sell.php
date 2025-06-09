
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Sales Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/sell.css">
</head>
 <header>
        <div class="container header-container">
            <a href="../index.php" class="logo">Easy<span>Trade</span></a>
            <div class="nav-links">
                <a href="../index.php">Home</a>
                <a href="Categories.php">Categories</a>
                <a href="seller_dashboard.php" class="nav-link">Dashboard</a>
                <!-- <a href="register.php">Register</a> -->
            </div>
        </div>
    </header>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Sell Your Items</h1>

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Item Listing Form -->
            <div class="bg-white p-6 rounded-lg shadow-md flex-1">
                <h2 class="text-xl font-semibold mb-4">Create New Listing</h2>

                <form method="POST" action="../controllers/sellController.php"
                    enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-gray-700 mb-1">Item Image</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <div id="image-preview" class="mb-2 hidden">
                                    <img id="preview" src="#" alt="Preview" class="max-h-48 mx-auto">
                                </div>
                                <div id="no-image" class="text-gray-500 mb-2">No image selected</div>
                                <input type="file" name="image" accept="image/*" class="hidden" id="image-upload"
                                    onchange="previewImage()">
                                <label for="image-upload"
                                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md cursor-pointer">
                                    Upload Image
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1">Item Name*</label>
                            <input type="text" name="itemName" value="" 
                                required class="w-full border border-gray-300 rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1">Category*</label>
                            <select name="category" required class="w-full border border-gray-300 rounded-md p-2">
                                <option value="">Select Category</option>
                                <option value="Electronic">Electronic</option>
                                <option value="clothes">clothes</option>
                                 <option value="Home&Garden">Home & Garden</option>
                                  <option value="Toys&Games">Toys & Games</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1">Condition*</label>
                            <select name="condition" required class="w-full border border-gray-300 rounded-md p-2">
                                <option value="">Select Condition</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1">Purchase Date</label>
                            <input type="date" name="purchaseDate"
                                value="<?php echo htmlspecialchars($purchaseDate); ?>"
                                class="w-full border border-gray-300 rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1">Price (R)*</label>
                            <input type="number" name="price" value="<?php echo htmlspecialchars($price); ?>" required
                                min="0.01" step="0.01" class="w-full border border-gray-300 rounded-md p-2">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 mb-1">Description*</label>
                            <textarea name="description" required rows="4"
                                class="w-full border border-gray-300 rounded-md p-2"></textarea>
                        </div>

                        <div class="col-span-2">
                            <button type="submit" name="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md">
                                List Item for Sale
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Listings -->
            <div class="bg-white p-6 rounded-lg shadow-md flex-1">
                <h2 class="text-xl font-semibold mb-4">Your Listings</h2>
                <div id="listing-container" class="space-y-4">
                    <div class="text-gray-500">Loading listings...</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function previewImage() {
            const preview = document.getElementById('preview');
            const fileInput = document.getElementById('image-upload');
            const previewContainer = document.getElementById('image-preview');
            const noImageText = document.getElementById('no-image');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    noImageText.classList.add('hidden');
                }

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

         function loadListings() {
        fetch('../controllers/fetch_listings.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('listing-container').innerHTML = html;

                // Attach event listeners for delete buttons
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const id = this.dataset.id;
                        if (confirm('Are you sure you want to remove this listing?')) {
                            deleteListing(id);
                        }
                    });
                });
            });
    }

    function deleteListing(id) {
        fetch('../controllers/delete_listing.php?id=' + id)
            .then(response => response.text())
            .then(result => {
                loadListings(); // Reload listings after deletion
            });
    }

    // Load on page load
    document.addEventListener('DOMContentLoaded', loadListings);
    </script>
</body>

</html>