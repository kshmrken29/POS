<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Customer Order</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .nav-link {
      font-weight: 500;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .container {
      margin-top: 30px;
    }
    .menu-item {
      cursor: pointer;
      transition: all 0.2s;
    }
    .menu-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .menu-item.selected {
      border: 2px solid #0d6efd;
    }
    .order-summary {
      position: sticky;
      top: 20px;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cashierNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="cashierNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="take-customer-order.php">Take Order</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../admin/index.php">Admin Panel</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
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
                          <span class="badge bg-primary">$<?php echo number_format($item['price_per_serve'], 2); ?></span>
                        </p>
                        <p class="card-text text-muted">
                          Available: <?php echo $available; ?> servings
                        </p>
                        <div class="d-flex justify-content-center item-controls d-none">
                          <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                          <input type="number" class="form-control mx-2 item-qty" style="width: 60px;" value="1" min="1" max="<?php echo $available; ?>">
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
              <h5>$<span id="orderTotal">0.00</span></h5>
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
                <span class="input-group-text">$</span>
                <input type="text" class="form-control" id="modalTotal" readonly>
              </div>
            </div>
            <div class="mb-3">
              <label for="amountPaid" class="form-label">Amount Paid:</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="amountPaid" step="0.01" min="0" required>
              </div>
              <div class="invalid-feedback" id="paymentError">
                Amount paid must be greater than or equal to the total amount.
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Change:</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
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
      const orderTotal = document.getElementById('orderTotal');
      const emptyOrder = document.getElementById('emptyOrder');
      const processOrderBtn = document.getElementById('processOrderBtn');
      const clearOrderBtn = document.getElementById('clearOrderBtn');
      const menuItems = document.querySelectorAll('.menu-item');
      const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
      const amountPaidInput = document.getElementById('amountPaid');
      const changeAmountInput = document.getElementById('changeAmount');
      const modalTotalInput = document.getElementById('modalTotal');
      const completeOrderBtn = document.getElementById('completeOrderBtn');
      const paymentError = document.getElementById('paymentError');
      
      // Add menu item to order
      menuItems.forEach(item => {
        item.addEventListener('click', function() {
          const itemId = this.dataset.id;
          const itemName = this.dataset.name;
          const itemPrice = parseFloat(this.dataset.price);
          const maxQty = parseInt(this.dataset.available);
          
          if (!this.classList.contains('selected')) {
            // Add item to order
            this.classList.add('selected');
            const itemControls = this.querySelector('.item-controls');
            itemControls.classList.remove('d-none');
            
            // Add to order items
            orderItems[itemId] = {
              id: itemId,
              name: itemName,
              price: itemPrice,
              qty: 1,
              maxQty: maxQty
            };
          } else {
            // Remove item from order
            this.classList.remove('selected');
            const itemControls = this.querySelector('.item-controls');
            itemControls.classList.add('d-none');
            
            // Reset quantity
            const qtyInput = this.querySelector('.item-qty');
            qtyInput.value = 1;
            
            // Remove from order items
            delete orderItems[itemId];
          }
          
          updateOrderSummary();
        });
        
        // Handle quantity changes
        const decreaseBtn = item.querySelector('.decrease-qty');
        const increaseBtn = item.querySelector('.increase-qty');
        const qtyInput = item.querySelector('.item-qty');
        
        decreaseBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          let qty = parseInt(qtyInput.value);
          if (qty > 1) {
            qty--;
            qtyInput.value = qty;
            orderItems[itemId].qty = qty;
            updateOrderSummary();
          }
        });
        
        increaseBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          let qty = parseInt(qtyInput.value);
          const maxQty = parseInt(item.dataset.available);
          if (qty < maxQty) {
            qty++;
            qtyInput.value = qty;
            orderItems[itemId].qty = qty;
            updateOrderSummary();
          }
        });
        
        qtyInput.addEventListener('change', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          let qty = parseInt(this.value);
          const maxQty = parseInt(item.dataset.available);
          
          if (qty < 1) qty = 1;
          if (qty > maxQty) qty = maxQty;
          
          this.value = qty;
          orderItems[itemId].qty = qty;
          updateOrderSummary();
        });
      });
      
      // Update order summary
      function updateOrderSummary() {
        const orderItemsDiv = document.getElementById('orderItems');
        total = 0;
        
        // Clear previous items
        orderItemsDiv.innerHTML = '';
        
        if (Object.keys(orderItems).length === 0) {
          orderItemsDiv.innerHTML = '<p class="text-center text-muted" id="emptyOrder">No items selected</p>';
          processOrderBtn.disabled = true;
        } else {
          processOrderBtn.disabled = false;
          
          // Add each item to the summary
          for (const key in orderItems) {
            const item = orderItems[key];
            const subtotal = item.price * item.qty;
            total += subtotal;
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'd-flex justify-content-between align-items-center mb-2';
            itemDiv.innerHTML = `
              <div>
                <span class="fw-bold">${item.name}</span>
                <br>
                <small class="text-muted">$${item.price.toFixed(2)} x ${item.qty}</small>
              </div>
              <div>$${subtotal.toFixed(2)}</div>
            `;
            
            orderItemsDiv.appendChild(itemDiv);
          }
        }
        
        orderTotal.textContent = total.toFixed(2);
      }
      
      // Clear order
      clearOrderBtn.addEventListener('click', function() {
        // Reset all selections
        menuItems.forEach(item => {
          item.classList.remove('selected');
          const itemControls = item.querySelector('.item-controls');
          itemControls.classList.add('d-none');
          const qtyInput = item.querySelector('.item-qty');
          qtyInput.value = 1;
        });
        
        // Clear order items
        Object.keys(orderItems).forEach(key => delete orderItems[key]);
        updateOrderSummary();
      });
      
      // Process order
      processOrderBtn.addEventListener('click', function() {
        modalTotalInput.value = total.toFixed(2);
        amountPaidInput.value = '';
        changeAmountInput.value = '';
        amountPaidInput.classList.remove('is-invalid');
        paymentModal.show();
      });
      
      // Calculate change
      amountPaidInput.addEventListener('input', function() {
        const amountPaid = parseFloat(this.value) || 0;
        const orderTotalValue = parseFloat(modalTotalInput.value);
        
        if (amountPaid >= orderTotalValue) {
          const change = amountPaid - orderTotalValue;
          changeAmountInput.value = change.toFixed(2);
          this.classList.remove('is-invalid');
          completeOrderBtn.disabled = false;
        } else {
          changeAmountInput.value = '';
          this.classList.add('is-invalid');
          completeOrderBtn.disabled = true;
        }
      });
      
      // Complete transaction
      completeOrderBtn.addEventListener('click', function() {
        // Prepare data for submission
        const order = {
          total: total,
          amountPaid: parseFloat(amountPaidInput.value),
          change: parseFloat(changeAmountInput.value),
          items: []
        };
        
        // Add items to the order
        for (const key in orderItems) {
          const item = orderItems[key];
          order.items.push({
            id: item.id,
            qty: item.qty,
            price: item.price,
            subtotal: item.price * item.qty
          });
        }
        
        // Send order to server
        fetch('save-transaction.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(order)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Redirect to receipt page
            window.location.href = 'display-change.php?id=' + data.transaction_id;
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while processing the transaction.');
        });
      });
    });
  </script>
</body>
</html>
