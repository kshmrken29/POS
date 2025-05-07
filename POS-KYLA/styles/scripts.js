// Main JavaScript file for Restaurant POS System - Simplified

document.addEventListener('DOMContentLoaded', function() {
  // Show loading message for all form submissions
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function() {
      showLoadingMessage();
    });
  });
  
  // Form validation
  const formValidation = document.querySelectorAll('.needs-validation');
  Array.from(formValidation).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
  
  // Confirmation dialogs for delete/void actions
  const confirmButtons = document.querySelectorAll('.btn-confirm-action');
  confirmButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      if (!confirm(this.getAttribute('data-confirm-message') || 'Are you sure you want to proceed with this action?')) {
        e.preventDefault();
      }
    });
  });
  
  // Toggle password visibility
  const passwordToggles = document.querySelectorAll('.password-toggle');
  passwordToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const inputField = document.querySelector(this.getAttribute('data-target'));
      if (inputField) {
        if (inputField.type === 'password') {
          inputField.type = 'text';
          this.textContent = 'Hide Password';
        } else {
          inputField.type = 'password';
          this.textContent = 'Show Password';
        }
      }
    });
  });
});

// Show loading message
function showLoadingMessage() {
  const messageDiv = document.createElement('div');
  messageDiv.className = 'loading-message';
  messageDiv.innerHTML = `
    <div class="spinner-overlay">
      <div>Loading... Please wait</div>
    </div>
  `;
  document.body.appendChild(messageDiv);
}

// Hide loading message
function hideLoadingMessage() {
  const messageDiv = document.querySelector('.loading-message');
  if (messageDiv) {
    messageDiv.remove();
  }
}

// Format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

// Format date
function formatDate(dateString, format = 'medium') {
  const date = new Date(dateString);
  const options = {
    short: { month: 'short', day: 'numeric', year: 'numeric' },
    medium: { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' },
    long: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }
  };
  
  return date.toLocaleDateString('en-US', options[format] || options.medium);
}

// Add items to cart
function addToCart(itemId, itemName, price, maxQuantity) {
  const cart = document.getElementById('cart-items');
  const quantityInput = document.getElementById('quantity-' + itemId);
  const quantity = parseInt(quantityInput.value);
  
  if (quantity <= 0 || quantity > maxQuantity) {
    alert(`Please enter a valid quantity (1-${maxQuantity}).`);
    return;
  }
  
  const total = price * quantity;
  
  // Check if item already exists in cart
  const existingItem = document.getElementById('cart-item-' + itemId);
  if (existingItem) {
    // Update existing item
    const currentQty = parseInt(existingItem.getAttribute('data-quantity'));
    const newQty = currentQty + quantity;
    
    if (newQty > maxQuantity) {
      alert(`Cannot add more than ${maxQuantity} units of this item.`);
      return;
    }
    
    existingItem.setAttribute('data-quantity', newQty);
    existingItem.querySelector('.item-quantity').textContent = newQty;
    existingItem.querySelector('.item-total').textContent = formatCurrency(price * newQty);
  } else {
    // Add new item to cart
    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom';
    cartItem.id = 'cart-item-' + itemId;
    cartItem.setAttribute('data-id', itemId);
    cartItem.setAttribute('data-price', price);
    cartItem.setAttribute('data-quantity', quantity);
    
    cartItem.innerHTML = `
      <div>
        <h6 class="mb-0">${itemName}</h6>
        <small class="text-muted">${quantity} × ${formatCurrency(price)}</small>
      </div>
      <div class="text-end">
        <div class="item-total">${formatCurrency(total)}</div>
        <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-1" onclick="removeFromCart('${itemId}')">
          Remove
        </button>
      </div>
    `;
    
    cart.appendChild(cartItem);
  }
  
  // Reset quantity input
  quantityInput.value = 1;
  
  // Update cart total
  updateCartTotal();
}

// Remove item from cart
function removeFromCart(itemId) {
  const cartItem = document.getElementById('cart-item-' + itemId);
  if (cartItem) {
    cartItem.remove();
    updateCartTotal();
  }
}

// Update cart total
function updateCartTotal() {
  const cartItems = document.querySelectorAll('.cart-item');
  let total = 0;
  
  cartItems.forEach(item => {
    const price = parseFloat(item.getAttribute('data-price'));
    const quantity = parseInt(item.getAttribute('data-quantity'));
    total += price * quantity;
  });
  
  // Update total display
  const totalElement = document.getElementById('cart-total');
  if (totalElement) {
    totalElement.textContent = formatCurrency(total);
  }
  
  // Update hidden input for form submission
  const totalInput = document.getElementById('total-amount');
  if (totalInput) {
    totalInput.value = total.toFixed(2);
  }
  
  // Update hidden input with order items
  const orderItemsInput = document.getElementById('order-items');
  if (orderItemsInput) {
    const orderItems = [];
    cartItems.forEach(item => {
      orderItems.push({
        id: item.getAttribute('data-id'),
        quantity: item.getAttribute('data-quantity')
      });
    });
    orderItemsInput.value = JSON.stringify(orderItems);
  }
  
  // Enable/disable checkout button
  const checkoutButton = document.getElementById('checkout-button');
  if (checkoutButton) {
    checkoutButton.disabled = cartItems.length === 0;
  }
} 