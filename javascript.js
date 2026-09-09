/* Category filter buttons — visual toggle only */
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

document.addEventListener("DOMContentLoaded", function () {

    const categoryButtons = document.querySelectorAll(".cat-btn");
    const products = document.querySelectorAll(".menu-card");

    categoryButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            // Remove active from all buttons
            categoryButtons.forEach(function (btn) {
                btn.classList.remove("active");
            });

            // Add active to clicked button
            button.classList.add("active");

            const selectedCategory = button.dataset.category;

            // Show/hide products
            products.forEach(function (product) {

                const productCategory = product.dataset.category;

                if (
                    selectedCategory === "all" ||
                    productCategory === selectedCategory
                ) {
                    product.style.display = "";
                } else {
                    product.style.display = "none";
                }

            });

        });

    });

    // ── Add to Order Button (AJAX Version - NO POPUP) ────────────────────
    document.querySelectorAll('.btn-add').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get product info
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const card = this.closest('.menu-card');
            
            // Find the size select
            const sizeSelect = card.querySelector('.size-select');
            
            // If no size select, use default values (for products without sizes)
            let sizeId = 0;
            let sizeName = 'Default';
            let price = 0;
            
            if (sizeSelect) {
                const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
                if (selectedOption) {
                    sizeId = sizeSelect.value;
                    sizeName = selectedOption.text.split(' - ')[0] || 'Default';
                    price = parseFloat(selectedOption.dataset.price) || 0;
                } else {
                    // No option selected
                    return;
                }
            } else {
                // No size select - use default (if product has only one size)
                // You might want to handle this differently
                return;
            }
            
            // Validate
            if (!sizeId || sizeId <= 0) {
                alert('Please select a size.');
                return;
            }
            
            if (price <= 0) {
                alert('Price not available. Please try again.');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('size_id', sizeId);
            formData.append('quantity', 1);
            
            // Show button feedback (no popup)
            const originalText = this.textContent;
            this.textContent = '⏳ Adding...';
            this.style.background = '#f59e0b';
            this.style.color = '#fff';
            this.disabled = true;
            
            // Send to cart via AJAX
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                // Show success feedback
                this.textContent = '✅ Added!';
                this.style.background = '#10b981';
                this.style.color = '#fff';
                
                // Update cart count in nav (if exists)
                updateCartCount();
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.color = '';
                    this.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                this.textContent = '❌ Error';
                this.style.background = '#dc2626';
                this.style.color = '#fff';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.color = '';
                    this.disabled = false;
                }, 2000);
            });
        });
    });

    // ── Update Cart Count ──────────────────────────────────────────────────
    function updateCartCount() {
        // Simple reload to update the cart count in nav
        // Or you can use fetch to get the count without reloading
        setTimeout(() => {
            location.reload();
        }, 500);
    }

});