<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Take Customer Order</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .menu-item {
      cursor: pointer;
      border: 1px solid #ddd;
      padding: 10px;
      margin-bottom: 15px;
      background-color: white;
    }
    .menu-item:hover {
      background-color: #f9f9f9;
    }
    .menu-item.selected {
      border: 2px solid #4169e1;
    }
    .order-summary {
      position: sticky;
      top: 20px;
    }
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
    }
    .item-controls {
      display: flex;
      justify-content: center;
      margin-top: 10px;
      display: none;
    }
    .item-controls.show {
      display: flex;
    }
    .item-qty {
      width: 50px;
      text-align: center;
      margin: 0 5px;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: white;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #ddd;
      width: 80%;
      max-width: 500px;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .modal-footer {
      border-top: 1px solid #ddd;
      padding-top: 15px;
      margin-top: 20px;
      text-align: right;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="take-customer-order.php">Take Order</a></li>
        <li class="nav-item"><a class="nav-link" href="view-transactions.php">Transactions</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

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
    
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
      <!-- Menu Items Selection -->
      <div style="flex: 3; min-width: 300px;">
        <div class="card">
          <h3 class="card-title">Available Menu Items</h3>
          <div>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <div class="menu-grid">
                <?php while($item = mysqli_fetch_assoc($result)): 
                  $available = $item['number_of_servings'] - $item['servings_sold'];
                ?>
                  <div class="menu-item" data-id="<?php echo $item['id']; ?>" 
                       data-name="<?php echo $item['menu_name']; ?>"
                       data-price="<?php echo $item['price_per_serve']; ?>"
                       data-available="<?php echo $available; ?>">
                    <h4><?php echo $item['menu_name']; ?></h4>
                    <p>
                      Price: $<?php echo number_format($item['price_per_serve'], 2); ?>
                    </p>
                    <p>
                      Available: <?php echo $available; ?> servings
                    </p>
                    <div class="item-controls">
                      <button class="btn btn-secondary decrease-qty">-</button>
                      <input type="number" class="item-qty" value="1" min="1" max="<?php echo $available; ?>">
                      <button class="btn btn-secondary increase-qty">+</button>
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
      <div style="flex: 1; min-width: 250px;">
        <div class="card order-summary">
          <h3 class="card-title">Order Summary</h3>
          <div>
            <div id="orderItems">
              <p id="emptyOrder">No items selected</p>
              <!-- Selected items will appear here -->
            </div>
            
            <hr>
            
            <div style="display: flex; justify-content: space-between;">
              <h4>Total:</h4>
              <h4>$<span id="orderTotal">0.00</span></h4>
            </div>
            
            <div style="margin-top: 20px;">
              <button id="processOrderBtn" class="btn btn-primary w-100" disabled>Process Order</button>
              <button id="clearOrderBtn" class="btn btn-secondary w-100" style="margin-top: 10px;">Clear Order</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Payment Processing -->
  <div class="modal" id="paymentModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Process Payment</h3>
        <button type="button" class="btn-close" id="closeModal">×</button>
      </div>
      <div>
        <form id="paymentForm">
          <div class="form-group">
            <label class="form-label">Total Amount:</label>
            <div style="display: flex; align-items: center;">
              <span style="margin-right: 5px;">$</span>
              <input type="text" class="form-control" id="modalTotal" readonly>
            </div>
          </div>
          <div class="form-group">
            <label for="amountPaid" class="form-label">Amount Paid:</label>
            <div style="display: flex; align-items: center;">
              <span style="margin-right: 5px;">$</span>
              <input type="number" class="form-control" id="amountPaid" step="0.01" min="0" required>
            </div>
            <div id="paymentError" style="color: red; display: none;">
              Amount paid must be greater than or equal to the total amount.
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Change:</label>
            <div style="display: flex; align-items: center;">
              <span style="margin-right: 5px;">$</span>
              <input type="text" class="form-control" id="changeAmount" readonly>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelPayment">Cancel</button>
        <button type="button" class="btn btn-primary" id="completeOrderBtn">Complete Transaction</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Variables
      const orderItems = {};
      let total = 0;
      
      // Elements
      const menuItems = document.querySelectorAll('.menu-item');
      const orderItemsContainer = document.getElementById('orderItems');
      const emptyOrderMessage = document.getElementById('emptyOrder');
      const orderTotalDisplay = document.getElementById('orderTotal');
      const processOrderBtn = document.getElementById('processOrderBtn');
      const clearOrderBtn = document.getElementById('clearOrderBtn');
      const paymentModal = document.getElementById('paymentModal');
      const closeModal = document.getElementById('closeModal');
      const cancelPayment = document.getElementById('cancelPayment');
      const modalTotalDisplay = document.getElementById('modalTotal');
      const amountPaidInput = document.getElementById('amountPaid');
      const changeDisplay = document.getElementById('changeAmount');
      const paymentError = document.getElementById('paymentError');
      const completeOrderBtn = document.getElementById('completeOrderBtn');
      
      // Add click event to menu items
      menuItems.forEach(item => {
        item.addEventListener('click', function() {
          const itemId = this.dataset.id;
          
          if (this.classList.contains('selected')) {
            // Already selected, toggle controls
            const controls = this.querySelector('.item-controls');
            controls.classList.toggle('show');
          } else {
            // Select the item
            this.classList.add('selected');
            const controls = this.querySelector('.item-controls');
            controls.classList.add('show');
            
            // Add to order
            orderItems[itemId] = {
              id: itemId,
              name: this.dataset.name,
              price: parseFloat(this.dataset.price),
              quantity: 1,
              available: parseInt(this.dataset.available)
            };
            
            updateOrderSummary();
          }
        });
        
        // Decrease quantity button
        const decreaseBtn = item.querySelector('.decrease-qty');
        decreaseBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          const qtyInput = item.querySelector('.item-qty');
          let newQty = parseInt(qtyInput.value) - 1;
          
          if (newQty <= 0) {
            // Remove item from order
            item.classList.remove('selected');
            item.querySelector('.item-controls').classList.remove('show');
            delete orderItems[itemId];
          } else {
            qtyInput.value = newQty;
            orderItems[itemId].quantity = newQty;
          }
          
          updateOrderSummary();
        });
        
        // Increase quantity button
        const increaseBtn = item.querySelector('.increase-qty');
        increaseBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          const qtyInput = item.querySelector('.item-qty');
          let newQty = parseInt(qtyInput.value) + 1;
          const available = parseInt(item.dataset.available);
          
          if (newQty <= available) {
            qtyInput.value = newQty;
            orderItems[itemId].quantity = newQty;
            updateOrderSummary();
          }
        });
        
        // Quantity input change
        const qtyInput = item.querySelector('.item-qty');
        qtyInput.addEventListener('change', function(e) {
          e.stopPropagation();
          const itemId = item.dataset.id;
          let newQty = parseInt(this.value);
          const available = parseInt(item.dataset.available);
          
          if (isNaN(newQty) || newQty <= 0) {
            // Remove item from order
            item.classList.remove('selected');
            item.querySelector('.item-controls').classList.remove('show');
            delete orderItems[itemId];
          } else if (newQty > available) {
            this.value = available;
            orderItems[itemId].quantity = available;
          } else {
            orderItems[itemId].quantity = newQty;
          }
          
          updateOrderSummary();
        });
      });
      
      // Update order summary
      function updateOrderSummary() {
        orderItemsContainer.innerHTML = '';
        total = 0;
        
        if (Object.keys(orderItems).length === 0) {
          orderItemsContainer.appendChild(emptyOrderMessage);
          processOrderBtn.disabled = true;
        } else {
          emptyOrderMessage.remove();
          processOrderBtn.disabled = false;
          
          for (const itemId in orderItems) {
            const item = orderItems[itemId];
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const itemElement = document.createElement('div');
            itemElement.style.marginBottom = '10px';
            itemElement.style.display = 'flex';
            itemElement.style.justifyContent = 'space-between';
            
            itemElement.innerHTML = `
              <div>
                <strong>${item.name}</strong><br>
                $${item.price.toFixed(2)} × ${item.quantity}
              </div>
              <div>$${itemTotal.toFixed(2)}</div>
            `;
            
            orderItemsContainer.appendChild(itemElement);
          }
        }
        
        orderTotalDisplay.textContent = total.toFixed(2);
      }
      
      // Clear order button
      clearOrderBtn.addEventListener('click', function() {
        menuItems.forEach(item => {
          item.classList.remove('selected');
          item.querySelector('.item-controls').classList.remove('show');
          item.querySelector('.item-qty').value = 1;
        });
        
        orderItems = {};
        updateOrderSummary();
      });
      
      // Process order button
      processOrderBtn.addEventListener('click', function() {
        modalTotalDisplay.value = total.toFixed(2);
        amountPaidInput.value = '';
        changeDisplay.value = '';
        paymentError.style.display = 'none';
        paymentModal.style.display = 'block';
      });
      
      // Close modal
      closeModal.addEventListener('click', function() {
        paymentModal.style.display = 'none';
      });
      
      cancelPayment.addEventListener('click', function() {
        paymentModal.style.display = 'none';
      });
      
      // Calculate change
      amountPaidInput.addEventListener('input', function() {
        const amountPaid = parseFloat(this.value) || 0;
        const change = amountPaid - total;
        
        if (change >= 0) {
          changeDisplay.value = change.toFixed(2);
          paymentError.style.display = 'none';
          completeOrderBtn.disabled = false;
        } else {
          changeDisplay.value = '0.00';
          paymentError.style.display = 'block';
          completeOrderBtn.disabled = true;
        }
      });
      
      // Complete order
      completeOrderBtn.addEventListener('click', function() {
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        if (amountPaid < total) {
          paymentError.style.display = 'block';
          return;
        }
        
        // Prepare order data
        const orderData = {
          items: Object.values(orderItems),
          total: total,
          amountPaid: amountPaid,
          change: amountPaid - total
        };
        
        // Send to server
        fetch('save-transaction.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Redirect to display change page
            window.location.href = `display-change.php?change=${orderData.change.toFixed(2)}&transaction_id=${data.transaction_id}`;
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while processing the transaction.');
        });
      });
      
      // Click outside modal to close
      window.addEventListener('click', function(event) {
        if (event.target === paymentModal) {
          paymentModal.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>
