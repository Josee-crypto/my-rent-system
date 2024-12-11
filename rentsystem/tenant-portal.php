<?php
session_start();
include('config.php');

// Check if user is logged in and is a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit();
}

// Fetch tenant details from the database
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :userId");
$stmt->execute([':userId' => $userId]);
$tenant = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Portal - Jeff Rentals</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-section {
            margin-top: 50px;
        }
        .room-card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .room-card:hover {
            transform: translateY(-10px);
        }
        .navbar {
            background-color: #2c3e50 !important;
        }
        .features-icon {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .room-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-available {
            background-color: #2ecc71;
            color: white;
        }
        .status-unavailable {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="tenant-portal.php"><i class="fas fa-home"></i> Jeff Rentals</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Tenant Dashboard -->
<div class="container profile-section">
    <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($tenant['full_name']); ?>!</h2>

    <div class="row">
        <!-- Tenant Profile -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Your Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($tenant['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($tenant['phone_number']); ?></p>
                    <p><strong>User Type:</strong> <?php echo htmlspecialchars($tenant['user_type']); ?></p>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Rented Properties -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Your Rented Properties</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Check if the tenant has a house assigned
                    if ($tenant['house_number']) {
                        // If the tenant has a house number, fetch house details
                        $houseNumber = $tenant['house_number'];
                        // Example of fetching house details based on the house number
                        // Replace this with actual house data query if available
                        $houseDetails = "House number: " . htmlspecialchars($houseNumber); // Example, customize as needed

                        // Display the assigned house
                        echo "<div class='room-card'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>Assigned House</h5>";
                        echo "<p class='card-text'>{$houseDetails}</p>";
                        echo "<div class='room-status status-available'>Assigned</div>";
                        echo "</div></div>";
                    } else {
                        // If the tenant doesn't have a house assigned, show a message
                        echo "<div class='alert alert-info'>You will be assigned a house once available.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($tenant['full_name']); ?>" id="editFullName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($tenant['email']); ?>" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" value="<?php echo htmlspecialchars($tenant['phone_number']); ?>" id="editPhone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" id="editPassword">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="editConfirmPassword">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit profile form submission (AJAX)
    $('#editProfileForm').on('submit', function(e) {
        e.preventDefault();

        var fullName = $('#editFullName').val();
        var email = $('#editEmail').val();
        var phone = $('#editPhone').val();
        var password = $('#editPassword').val();
        var confirmPassword = $('#editConfirmPassword').val();

        // If passwords match, proceed
        if (password !== "" && password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        $.ajax({
            url: 'update-profile.php',  // Endpoint to update profile
            type: 'POST',
            data: {
                user_id: <?php echo $userId; ?>,
                full_name: fullName,
                email: email,
                phone: phone,
                password: password
            },
            success: function(response) {
                if (response === 'success') {
                    alert('Profile updated successfully!');
                    window.location.reload();  // Reload to reflect changes
                } else {
                    alert('Error updating profile.');
                }
            }
        });
    });
});
</script>

</body>
</html>
