<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTrade - Online Shopping</title>
    <link rel="stylesheet" href="..\public\css\Categories.css">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="../index.php" class="logo-link">EasyTrade</a>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search for products...">
            </div>
            <div class="nav-buttons">
                <button class="nav-btn">Account</button>
                <button class="nav-btn">Cart</button>
            </div>
        </div>
    </header>

    <section class="hero">
        <h1>Welcome to EasyTrade</h1>
        <p>Shop the best Electronics, Clothes, Home & Garden, and Toys & Games</p>
        <button class="hero-btn">Shop Now</button>
    </section>
<?php
        session_start();

        if (isset($_SESSION['buy_message_error'])) {
            // Decode JSON
            $json = json_decode($_SESSION['buy_message_error'], true);

            if (isset($json['message'])) {
                echo "<div style='background: #e0ffe0; padding: 10px; border: 1px solid green;'>
                        " . htmlspecialchars($json['message']) . "
                    </div>";
            }

            // Clear the session message after displaying
            unset($_SESSION['buy_message_error']);
        }
        ?>

    <section class="categories">
        <div class="loading">Loading products...</div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>About EasyTrade</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Categories</h3>
                <ul>
                    <li><a href="#">Electronics</a></li>
                    <li><a href="#">Clothes</a></li>
                    <li><a href="#">Home & Garden</a></li>
                    <li><a href="#">Toys & Games</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">YouTube</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 EasyTrade. All Rights Reserved.
        </div>
    </footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const categoriesSection = document.querySelector(".categories");

    if (categoriesSection) {
        fetch("../controllers/fetch_products.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                categoriesSection.innerHTML = "";

                if (Object.keys(data).length === 0) {
                    categoriesSection.innerHTML = `
                        <div class="error-message">
                            No products found. Please check back later.
                        </div>
                    `;
                    return;
                }

                for (const category in data) {
                    let categoryHTML = `
                        <div class="category-header">
                            <h2>${category}</h2>
                            <a href="#" class="view-all">View All</a>
                        </div>
                        <div class="product-grid">
                    `;

                    let products = data[category];
                    if (!Array.isArray(products)) {
                        products = [products];
                    }

                    products.forEach(product => {
                        const currentUserId = document.body.getAttribute('data-user-id');
                        const isSeller = currentUserId === product.seller_id;

                        categoryHTML += `
                            <div class="product-card">
                                <img src="${product.image_path}" class="product-image" alt="${product.name}">
                                <div class="product-info">
                                    <div class="product-name">${product.name}</div>
                                    <div class="product-price">R${parseFloat(product.price).toFixed(2)}</div>
                                    ${
                                        !isSeller
                                            ? `<a href="chat.php?listing_id=${product.id}&user_id=${product.seller_id}" class="contact-seller-button">
                                                    <button class="btn btn-outline-primary w-100 add-to-cart">
                                                        <i class="bi bi-chat-dots"></i> Contact Seller
                                                    </button>
                                                </a>`
                                            : `<div class="text-muted small">This is your product</div>`
                                    }
                                </div>
                            </div>
                        `;
                    });

                    categoryHTML += `</div>`;
                    categoriesSection.innerHTML += categoryHTML;
                }
            })
            .catch(error => {
                console.error("Error loading products:", error);
                categoriesSection.innerHTML = `
                    <div class="error-message">
                        Failed to load products. Please try again later.<br>
                        Error: ${error.message}
                    </div>
                `;
            });
    }

    // Product Details Page Chat Button
    const productDetails = document.querySelector('.product-details');
    if (productDetails) {
        const listingId = productDetails.getAttribute('data-listing-id');
        const sellerId = productDetails.getAttribute('data-seller-id');
        const currentUserId = document.body.getAttribute('data-user-id');

        if (currentUserId !== sellerId) {
            const chatButton = document.createElement('a');
            chatButton.href = `chat.php?listing_id=${listingId}&user_id=${sellerId}`;
            chatButton.className = 'btn btn-primary mt-3 w-100';
            chatButton.innerHTML = '<i class="bi bi-chat-dots"></i> Contact Seller';

            const actionButton = document.querySelector('.product-actions .btn');
            if (actionButton) {
                actionButton.parentNode.appendChild(chatButton);
            }
        }
    }
});
</script>



</body>

</html>

