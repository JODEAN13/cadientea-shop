/* Category filter buttons — visual toggle */
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
            categoryButtons.forEach(function (btn) {
                btn.classList.remove("active");
            });
            button.classList.add("active");

            const selectedCategory = button.dataset.category;

            products.forEach(function (product) {
                const productCategory = product.dataset.category;
                if (selectedCategory === "all" || productCategory === selectedCategory) {
                    product.style.display = "";
                } else {
                    product.style.display = "none";
                }
            });
        });
    });

    // ── Add to Order Button ──────────────────────────────────────────────────
    document.querySelectorAll('.btn-add').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const card = this.closest('.menu-card');
            const sizeSelect = card.querySelector('.size-select');
            
            if (!sizeSelect) {
                alert('Please select a size.');
                return;
            }
            
            const sizeId = sizeSelect.value;
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            if (price <= 0) {
                alert('Error: Price not available. Please try again.');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('size_id', sizeId);
            formData.append('quantity', 1);
            
            // Show button feedback
            const originalText = this.textContent;
            this.textContent = '⏳ Adding...';
            this.style.background = '#f59e0b';
            this.style.color = '#fff';
            this.disabled = true;
            
            // Send to server
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // If redirected to login page, follow the redirect
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                return response.text();
            })
            .then(data => {
                if (data === null) return; // Was redirected
                
                // Show success feedback
                this.textContent = '✅ Added!';
                this.style.background = '#10b981';
                this.style.color = '#fff';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.color = '';
                    this.disabled = false;
                    location.reload();
                }, 1500);
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

});