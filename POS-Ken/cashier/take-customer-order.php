<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier page was accessed
log_activity('accessed take customer order page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Customer Order</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }
    .sidebar {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      padding: 48px 0 0;
      box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
      background-color: #212529;
      width: 250px;
    }
    .sidebar-sticky {
      height: 100vh;
      overflow-x: hidden;
      overflow-y: auto;
    }
    .sidebar .nav-link {
      font-weight: 500;
      color: #adb5bd;
      padding: 0.75rem 1rem;
      margin-bottom: 0.25rem;
    }
    .sidebar .nav-link:hover {
      color: #fff;
    }
    .sidebar .nav-link.active {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.1);
      border-left: 4px solid #0d6efd;
    }
    .sidebar .nav-link .bi {
      margin-right: 0.5rem;
    }
    .sidebar-heading {
      font-size: .75rem;
      text-transform: uppercase;
      padding: 1rem;
      color: #6c757d;
    }
    .navbar-brand {
      padding: 1rem;
      font-weight: bold;
      color: white;
      text-align: center;
      display: block;
    }
    .main-content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
    }
    @media (max-width: 767.98px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        padding-top: 0;
      }
      .main-content {
        margin-left: 0;
        width: 100%;
      }
    }
    .feature-card {
      transition: transform 0.3s ease;
      margin-bottom: 20px;
      height: 100%;
    }
    .feature-card:hover {
      transform: translateY(-5px);
    }
    .card-icon {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #0d6efd;
    }
    .user-info {
      color: white;
      margin-right: 15px;
    }
    .card-body {
      display: flex;
      flex-direction: column;
    }
    .card-text {
      flex-grow: 1;
    }
    .row {
      width: 100%;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar col-md-3 col-lg-2 d-md-block">
    <div class="position-sticky sidebar-sticky">
      <a href="index.php" class="navbar-brand">Restaurant POS - Cashier</a>
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
        <li class="sidebar-heading">Transactions</li>
        <li class="nav-item">
          <a class="nav-link active" href="take-customer-order.php">
            <i class="bi bi-cart-plus"></i> Take Order
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="view-transactions.php">
            <i class="bi bi-search"></i> View Transactions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="void-transaction.php">
            <i class="bi bi-x-circle"></i> Void Transaction
          </a>
        </li>
        <?php if (is_admin()): ?>
        <li class="sidebar-heading">Administration</li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/dashboard.php">
            <i class="bi bi-gear"></i> Admin Panel
          </a>
        </li>
        <?php endif; ?>
        <li class="sidebar-heading">Account</li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <h2 class="mb-4">Take Customer Order</h2>
    
    <?php
    // Include database connection
    include '../admin/connection.php';
    
    // Get all available menu items
    $sql = "SELECT * FROM menu_items WHERE (number_of_servings - servings_sold) > 0 ORDER BY menu_name";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    ?>
    
    <div class="row">
      <!-- Menu Items Selection -->
      <div class="col-md-8">
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h4>Available Menu Items</h4>
          </div>
          <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
              <div class="row">
                <?php while($item = mysqli_fetch_assoc($result)): 
                  $available = $item['number_of_servings'] - $item['servings_sold'];
                ?>
                  <div class="col-md-4 mb-3">
                    <div class="card menu-item" data-id="<?php echo $item['id']; ?>" 
                         data-name="<?php echo $item['menu_name']; ?>"
                         data-price="<?php echo $item['price_per_serve']; ?>"
                         data-available="<?php echo $available; ?>">
                      <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $item['menu_name']; ?></h5>
                        <p class="card-text">
                          <span class="badge bg-primary">₱<?php echo number_format($item['price_per_serve'], 2); ?></span>
                        </p>
                        <p class="card-text text-muted">
                          Available: <?php echo $available; ?> servings
                        </p>
                        <div class="d-flex justify-content-center item-controls d-none">
                          <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                          <div class="item-qty-container">
                            <input type="number" class="form-control mx-2 item-qty" style="width: 60px;" value="1" 
                                  min="1" max="<?php echo $available; ?>" 
                                  onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                  onfocus="this.select()">
                          </div>
                          <button class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="alert alert-warning">No menu items available. Please check with the kitchen or add menu items.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Order Summary -->
      <div class="col-md-4">
        <div class="card order-summary">
          <div class="card-header bg-success text-white">
            <h4>Order Summary</h4>
          </div>
          <div class="card-body">
            <div id="orderItems">
              <p class="text-center text-muted" id="emptyOrder">No items selected</p>
              <!-- Selected items will appear here -->
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
              <h5>Total:</h5>
              <h5>₱<span id="orderTotal">0.00</span></h5>
            </div>
            
            <div class="d-grid gap-2 mt-3">
              <button id="processOrderBtn" class="btn btn-primary" disabled>Process Order</button>
              <button id="clearOrderBtn" class="btn btn-outline-secondary">Clear Order</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Payment Processing -->
  <div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Process Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="paymentForm">
            <div class="mb-3">
              <label class="form-label">Total Amount:</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="text" class="form-control" id="modalTotal" readonly>
              </div>
            </div>
            <div class="mb-3">
              <label for="amountPaid" class="form-label">Amount Paid:</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" id="amountPaid" step="0.01" min="0" required>
              </div>
              <div class="invalid-feedback" id="paymentError">
                Amount paid must be greater than or equal to the total amount.
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Change:</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="text" class="form-control" id="changeAmount" readonly>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="completeOrderBtn">Complete Transaction</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Variables
      const orderItems = {};
      let total = 0;
      
      // DOM Elements
      const menuItems = document.querySelectorAll('.menu-item');
      const orderItemsContainer = document.getElementById('orderItems');
      const emptyOrderMessage = document.getElementById('emptyOrder');
      const orderTotalElem = document.getElementById('orderTotal');
      const processOrderBtn = document.getElementById('processOrderBtn');
      const clearOrderBtn = document.getElementById('clearOrderBtn');
      const modalTotalElem = document.getElementById('modalTotal');
      const amountPaidInput = document.getElementById('amountPaid');
      const changeAmountElem = document.getElementById('changeAmount');
      const completeOrderBtn = document.getElementById('completeOrderBtn');
      const paymentError = document.getElementById('paymentError');
      
      // Add click event to menu items
      menuItems.forEach(item => {
        item.addEventListener('click', function() {
          this.classList.toggle('selected');
          
          const itemId = this.dataset.id;
          const controls = this.querySelector('.item-controls');
          
          if (this.classList.contains('selected')) {
            // Item selected
            controls.classList.remove('d-none');
            
            // Add to order
            if (!orderItems[itemId]) {
              orderItems[itemId] = {
                id: itemId,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                quantity: 1,
                available: parseInt(this.dataset.available)
              };
            }
          } else {
            // Item deselected
            controls.classList.add('d-none');
            
            // Remove from order
            if (orderItems[itemId]) {
              delete orderItems[itemId];
            }
          }
          
          updateOrderSummary();
        });
      });
      
      // Add click events to quantity controls
      document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const menuItem = this.closest('.menu-item');
          const itemId = menuItem.dataset.id;
          const qtyInput = menuItem.querySelector('.item-qty');
          
          if (orderItems[itemId]) {
            const newQty = Math.min(parseInt(qtyInput.value) + 1, orderItems[itemId].available);
            qtyInput.value = newQty;
            orderItems[itemId].quantity = newQty;
            updateOrderSummary();
          }
        });
      });
      
      document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const menuItem = this.closest('.menu-item');
          const itemId = menuItem.dataset.id;
          const qtyInput = menuItem.querySelector('.item-qty');
          
          if (orderItems[itemId]) {
            const newQty = Math.max(parseInt(qtyInput.value) - 1, 1);
            qtyInput.value = newQty;
            orderItems[itemId].quantity = newQty;
            updateOrderSummary();
          }
        });
      });
      
      // When menu item is clicked, update focus to quantity input for easier editing
      menuItems.forEach(item => {
        item.addEventListener('click', function() {
          if (this.classList.contains('selected')) {
            // After item is selected, focus on quantity input to make typing easier
            setTimeout(() => {
              const qtyInput = this.querySelector('.item-qty');
              if (qtyInput) {
                qtyInput.select(); // Select all text in the input
              }
            }, 100);
          }
        });
      });
      
      document.querySelectorAll('.item-qty').forEach(input => {
        // Handle 'change' event (when input loses focus)
        input.addEventListener('change', function(e) {
          // Force applying constraints on change
          const menuItem = this.closest('.menu-item');
          const itemId = menuItem.dataset.id;
          
          if (orderItems[itemId]) {
            let value = parseInt(this.value) || 1;
            // Always apply min/max constraints on change event
            value = Math.max(1, Math.min(value, orderItems[itemId].available));
            this.value = value;
            orderItems[itemId].quantity = value;
            updateOrderSummary();
          }
        });
        
        // Handle blur event to apply constraints
        input.addEventListener('blur', function(e) {
          // Force applying constraints when field loses focus
          const menuItem = this.closest('.menu-item');
          const itemId = menuItem.dataset.id;
          
          if (orderItems[itemId]) {
            let value = parseInt(this.value) || 1;
            // Always apply min/max constraints on blur
            value = Math.max(1, Math.min(value, orderItems[itemId].available));
            this.value = value;
            orderItems[itemId].quantity = value;
            updateOrderSummary();
          }
        });
        
        // Handle real-time updates as user types
        input.addEventListener('input', function(e) {
          updateQuantity(this);
        });
        
        // Handle keyboard input
        input.addEventListener('keyup', function(e) {
          if (e.key === 'Enter') {
            this.blur(); // Remove focus to trigger validation
          } else {
            updateQuantity(this);
          }
        });
      });
      
      // Unified function to update quantity
      function updateQuantity(inputElement) {
        const menuItem = inputElement.closest('.menu-item');
        const itemId = menuItem.dataset.id;
        
        if (orderItems[itemId]) {
          // Get input value
          let inputValue = inputElement.value.trim();
          
          // Handle empty input
          if (inputValue === '') {
            // Don't modify the input field while user is typing
            return;
          }
          
          // Parse as integer
          let newQty = parseInt(inputValue);
          
          // If valid number, update the order (don't constrain while actively typing)
          if (!isNaN(newQty)) {
            // Only apply immediate constraints if value is outside allowed range
            if (newQty > orderItems[itemId].available) {
              newQty = orderItems[itemId].available;
              inputElement.value = newQty;
            } else if (newQty < 1) {
              newQty = 1;
              inputElement.value = newQty;
            }
            
            // Update order quantity and summary
            orderItems[itemId].quantity = newQty;
            updateOrderSummary();
          }
        }
      }
      
      // Update order summary
      function updateOrderSummary() {
        // Clear existing items
        while (orderItemsContainer.contains(emptyOrderMessage)) {
          orderItemsContainer.removeChild(emptyOrderMessage);
        }
        
        const orderElements = orderItemsContainer.querySelectorAll('.order-item');
        orderElements.forEach(elem => elem.remove());
        
        // Calculate new total
        total = 0;
        const itemCount = Object.keys(orderItems).length;
        
        if (itemCount === 0) {
          orderItemsContainer.appendChild(emptyOrderMessage);
          processOrderBtn.disabled = true;
        } else {
          processOrderBtn.disabled = false;
          
          // Add items to summary
          for (const id in orderItems) {
            const item = orderItems[id];
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            const itemElem = document.createElement('div');
            itemElem.className = 'order-item mb-2';
            itemElem.innerHTML = `
              <div class="d-flex justify-content-between">
                <div>
                  <strong>${item.name}</strong>
                  <br>
                  <small class="text-muted">₱${item.price.toFixed(2)} x ${item.quantity}</small>
                </div>
                <div class="text-end">
                  <strong>₱${subtotal.toFixed(2)}</strong>
                  <br>
                  <button class="btn btn-sm btn-outline-danger remove-item" data-id="${id}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            `;
            
            orderItemsContainer.appendChild(itemElem);
            
            // Add remove button event
            itemElem.querySelector('.remove-item').addEventListener('click', function() {
              const itemId = this.dataset.id;
              
              // Deselect the menu item
              const menuItem = document.querySelector(`.menu-item[data-id="${itemId}"]`);
              if (menuItem) {
                menuItem.classList.remove('selected');
                menuItem.querySelector('.item-controls').classList.add('d-none');
              }
              
              // Remove from order
              delete orderItems[itemId];
              updateOrderSummary();
            });
          }
        }
        
        // Update total
        orderTotalElem.textContent = total.toFixed(2);
      }
      
      // Clear order
      clearOrderBtn.addEventListener('click', function() {
        // Deselect all menu items
        menuItems.forEach(item => {
          item.classList.remove('selected');
          item.querySelector('.item-controls').classList.add('d-none');
          item.querySelector('.item-qty').value = 1;
        });
        
        // Clear order items
        Object.keys(orderItems).forEach(key => delete orderItems[key]);
        updateOrderSummary();
      });
      
      // Process order button
      processOrderBtn.addEventListener('click', function() {
        // Populate modal
        modalTotalElem.value = total.toFixed(2);
        amountPaidInput.value = '';
        changeAmountElem.value = '';
        
        // Show payment modal
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
      });
      
      // Calculate change
      amountPaidInput.addEventListener('input', function() {
        const amountPaid = parseFloat(this.value) || 0;
        const change = amountPaid - total;
        
        if (change >= 0) {
          changeAmountElem.value = change.toFixed(2);
          this.classList.remove('is-invalid');
          paymentError.style.display = 'none';
          completeOrderBtn.disabled = false;
        } else {
          changeAmountElem.value = '0.00';
          this.classList.add('is-invalid');
          paymentError.style.display = 'block';
          completeOrderBtn.disabled = true;
        }
      });
      
      // Complete order
      completeOrderBtn.addEventListener('click', function() {
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        const change = amountPaid - total;
        
        if (change >= 0) {
          // Prepare order data
          const orderData = {
            total_amount: total,
            amount_paid: amountPaid,
            change_amount: change,
            items: []
          };
          
          // Add all items
          for (const id in orderItems) {
            const item = orderItems[id];
            orderData.items.push({
              id: item.id,
              quantity: item.quantity,
              price: item.price
            });
          }
          
          // Send the data to the server
          console.log('Sending order data:', JSON.stringify(orderData));
          fetch('save-transaction.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
          })
          .then(data => {
            console.log('Server response:', data);
            if (data.success) {
              // Redirect to display change page
              window.location.href = 'display-change.php?id=' + data.transaction_id;
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the transaction: ' + error.message);
          });
        }
      });
    });
  </script>
</body>
</html>
