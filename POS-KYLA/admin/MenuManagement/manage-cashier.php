<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Cashier</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
      margin-top: 50px;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Restaurant POS - Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="../index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="input-daily-menu.php">Input Daily Menu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="edit-menu-details.php">Edit Menu Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="monitor-menu-sales.php">Monitor Menu Sales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-reporting.php">Sales Reporting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="manage-cashier.php">Manage Cashier</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h2 class="mb-4">Manage Cashiers</h2>
    
    <?php
    // Include database connection
    include '../../connection.php';
    
    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Create new cashier
        if (isset($_POST['add_cashier'])) {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
            $contact = $_POST['contact'];
            $address = $_POST['address'];
            $date_hired = date('Y-m-d');
            $status = 'Active';
            
            $sql = "INSERT INTO cashiers (name, username, password, contact, address, date_hired, status) 
                    VALUES ('$name', '$username', '$password', '$contact', '$address', '$date_hired', '$status')";
                    
            if (mysqli_query($conn, $sql)) {
                echo '<div class="alert alert-success" role="alert">Cashier added successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error adding cashier: ' . mysqli_error($conn) . '</div>';
            }
        }
        
        // Update cashier
        if (isset($_POST['update_cashier'])) {
            $cashier_id = $_POST['cashier_id'];
            $name = $_POST['name'];
            $username = $_POST['username'];
            $contact = $_POST['contact'];
            $address = $_POST['address'];
            $status = $_POST['status'];
            
            // Check if password should be updated
            $password_sql = "";
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $password_sql = ", password = '$password'";
            }
            
            $sql = "UPDATE cashiers SET 
                    name = '$name',
                    username = '$username',
                    contact = '$contact',
                    address = '$address',
                    status = '$status'
                    $password_sql
                    WHERE id = $cashier_id";
                    
            if (mysqli_query($conn, $sql)) {
                echo '<div class="alert alert-success" role="alert">Cashier updated successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error updating cashier: ' . mysqli_error($conn) . '</div>';
            }
        }
        
        // Delete cashier
        if (isset($_POST['delete_cashier'])) {
            $cashier_id = $_POST['cashier_id'];
            
            $sql = "DELETE FROM cashiers WHERE id = $cashier_id";
                    
            if (mysqli_query($conn, $sql)) {
                echo '<div class="alert alert-success" role="alert">Cashier deleted successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error deleting cashier: ' . mysqli_error($conn) . '</div>';
            }
        }
    }
    
    // Get cashier data for edit form if ID is provided
    $editCashier = null;
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $sql = "SELECT * FROM cashiers WHERE id = $edit_id";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $editCashier = mysqli_fetch_assoc($result);
        }
    }
    
    // Get all cashiers for the table
    $sql = "SELECT * FROM cashiers ORDER BY name";
    $cashiers = mysqli_query($conn, $sql);
    ?>
    
    <div class="row">
        <!-- Form Column -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4><?php echo ($editCashier) ? 'Edit Cashier' : 'Add New Cashier'; ?></h4>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <?php if ($editCashier): ?>
                            <input type="hidden" name="cashier_id" value="<?php echo $editCashier['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                value="<?php echo ($editCashier) ? $editCashier['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                value="<?php echo ($editCashier) ? $editCashier['username'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <?php echo ($editCashier) ? 'Password (leave blank to keep current)' : 'Password'; ?>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                <?php echo ($editCashier) ? '' : 'required'; ?>>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" 
                                value="<?php echo ($editCashier) ? $editCashier['contact'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo ($editCashier) ? $editCashier['address'] : ''; ?></textarea>
                        </div>
                        
                        <?php if ($editCashier): ?>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" <?php echo ($editCashier['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo ($editCashier['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="update_cashier" class="btn btn-primary">Update Cashier</button>
                            <a href="manage-cashier.php" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_cashier" class="btn btn-success">Add Cashier</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Table Column -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Cashier List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Contact</th>
                                    <th>Date Hired</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($cashiers) > 0) {
                                    while($cashier = mysqli_fetch_assoc($cashiers)) {
                                        $statusClass = ($cashier['status'] == 'Active') ? 'text-success' : 'text-danger';
                                        echo '<tr>
                                                <td>' . $cashier['name'] . '</td>
                                                <td>' . $cashier['username'] . '</td>
                                                <td>' . $cashier['contact'] . '</td>
                                                <td>' . $cashier['date_hired'] . '</td>
                                                <td class="' . $statusClass . '">' . $cashier['status'] . '</td>
                                                <td>
                                                    <a href="?edit_id=' . $cashier['id'] . '" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal' . $cashier['id'] . '">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>';
                                            
                                        // Delete confirmation modal
                                        echo '<div class="modal fade" id="deleteModal' . $cashier['id'] . '" tabindex="-1" aria-hidden="true">
                                              <div class="modal-dialog">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                    Are you sure you want to delete cashier: <strong>' . $cashier['name'] . '</strong>?
                                                  </div>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                                                        <input type="hidden" name="cashier_id" value="' . $cashier['id'] . '">
                                                        <button type="submit" name="delete_cashier" class="btn btn-danger">Confirm Delete</button>
                                                    </form>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">No cashiers found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
