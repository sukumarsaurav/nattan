<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['consultant_id'])) {
    header("Location: consultant-dashboard.php");
    exit();
}

$page_title = "Consultant Login | CANEXT Immigration";
include('../includes/header.php');

// Database connection
$conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM consultants WHERE email = '$email' AND status = 'approved'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $consultant = mysqli_fetch_assoc($result);
        if (password_verify($password, $consultant['password'])) {
            // Set session variables
            $_SESSION['consultant_id'] = $consultant['id'];
            $_SESSION['consultant_name'] = $consultant['first_name'] . ' ' . $consultant['last_name'];
            
            // Redirect to dashboard
            header("Location: consultant-dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found or account not approved";
    }
}
?>

<!-- Login Section -->
<section class="section" style="padding: 60px 0; min-height: calc(100vh - 200px); display: flex; align-items: center;">
    <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
            <h1 style="text-align: center; margin-bottom: 30px; color: var(--color-burgundy);">Consultant Login</h1>
            
            <?php if ($error): ?>
                <div style="background-color: #ffe6e6; color: #dc3545; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px;">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 15px;">
                    Login
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <p style="margin-bottom: 10px;">Don't have an account?</p>
                <a href="consultant-register.php" style="color: var(--color-burgundy); text-decoration: none; font-weight: 500;">
                    Register as a Consultant
                </a>
            </div>
        </div>
    </div>
</section>

<?php 
mysqli_close($conn);
include('../includes/footer.php'); 
?> 