// cart.js
document.addEventListener('DOMContentLoaded', function() {
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    const cartTableBody = document.getElementById('cart-items');

    function renderCart() {
        cartTableBody.innerHTML = '';
        cartItems.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.product}</td>
                <td>${item.price}</td>
                <td>1</td>
                <td>${item.price}</td>
            `;
            cartTableBody.appendChild(row);
        });
    }

    document.querySelectorAll('a[data-product]').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const product = button.getAttribute('data-product');
            const price = button.getAttribute('data-price');

            cartItems.push({ product, price });
            localStorage.setItem('cartItems', JSON.stringify(cartItems));

            window.location.href = 'addcart.html';
        });
    });

    if (cartTableBody) {
        renderCart();
    }
});
