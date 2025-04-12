<?php
$page_title = "Consultant Profile | Admin Dashboard";
$current_page = 'consultant_profile.php';
include('includes/header.php');
include('includes/db_connection.php');

// Get consultant ID from URL
if (!isset($_GET['id'])) {
    header("Location: consultants.php");
    exit();
}

$consultant_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM consultants WHERE id = '$consultant_id'";
$result = executeQuery($query);

if (!$result || $result->num_rows === 0) {
    header("Location: consultants.php");
    exit();
}

$consultant = $result->fetch_assoc();

// Get consultant's reviews
$reviews_query = "SELECT * FROM consultant_reviews WHERE consultant_id = '$consultant_id' ORDER BY created_at DESC";
$reviews_result = executeQuery($reviews_query);
?>

<div class="admin-content-header">
    <div class="header-title">
        <h1><i class="fas fa-user-tie"></i> Consultant Profile</h1>
        <p>View and manage consultant details</p>
    </div>
    <div class="header-actions">
        <a href="consultants.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Consultants
        </a>
    </div>
</div>

<div class="admin-content-body">
    <!-- Basic Information -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Basic Information</h2>
            <span class="status-badge status-<?php echo $consultant['status']; ?>">
                <?php echo ucfirst($consultant['status']); ?>
            </span>
        </div>
        <div class="admin-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Full Name</label>
                    <p><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($consultant['email']); ?></p>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <p><?php echo htmlspecialchars($consultant['phone']); ?></p>
                </div>
                <div class="info-item">
                    <label>RCIC Number</label>
                    <p><?php echo htmlspecialchars($consultant['rcic_number']); ?></p>
                </div>
                <div class="info-item">
                    <label>Registration Date</label>
                    <p><?php echo date('F d, Y', strtotime($consultant['created_at'])); ?></p>
                </div>
                <div class="info-item">
                    <label>Membership Plan</label>
                    <p><?php echo ucfirst($consultant['membership_plan'] ?? 'None'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Information -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Professional Information</h2>
        </div>
        <div class="admin-card-body">
            <div class="info-item">
                <label>Specializations</label>
                <div class="tags">
                    <?php 
                    $specializations = explode(',', $consultant['specialization']);
                    foreach($specializations as $spec): 
                        if(trim($spec)):
                    ?>
                        <span class="tag"><?php echo htmlspecialchars(trim($spec)); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <div class="info-item">
                <label>Languages</label>
                <div class="tags">
                    <?php 
                    $languages = explode(',', $consultant['languages']);
                    foreach($languages as $lang): 
                        if(trim($lang)):
                    ?>
                        <span class="tag"><?php echo htmlspecialchars(trim($lang)); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <div class="info-item">
                <label>Bio</label>
                <p class="bio-text"><?php echo nl2br(htmlspecialchars($consultant['bio'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Client Reviews</h2>
        </div>
        <div class="admin-card-body">
            <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                <div class="reviews-list">
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <strong><?php echo htmlspecialchars($review['client_name']); ?></strong>
                                    <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Actions</h2>
        </div>
        <div class="admin-card-body">
            <div class="action-buttons">
                <?php if ($consultant['status'] === 'pending'): ?>
                    <form method="POST" action="consultants.php">
                        <input type="hidden" name="consultant_id" value="<?php echo $consultant['id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="action-btn approve-btn" onclick="return confirm('Are you sure you want to approve this consultant?')" title="Approve Consultant">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    <form method="POST" action="consultants.php">
                        <input type="hidden" name="consultant_id" value="<?php echo $consultant['id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="action-btn reject-btn" onclick="return confirm('Are you sure you want to reject this consultant?')" title="Reject Consultant">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="consultants.php">
                    <input type="hidden" name="consultant_id" value="<?php echo $consultant['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this consultant? This action cannot be undone.')" title="Delete Consultant">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.admin-content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-title h1 {
    font-size: 1.8rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-title p {
    color: #666;
    margin: 5px 0 0;
}

.admin-content-body {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.admin-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.admin-card-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-card-header h2 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.admin-card-body {
    padding: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    margin-bottom: 15px;
}

.info-item label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #666;
}

.info-item p {
    margin: 0;
}

.tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag {
    background: #f0f0f0;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.bio-text {
    line-height: 1.6;
    color: #444;
}

.review-item {
    border-bottom: 1px solid #eee;
    padding: 15px 0;
}

.review-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.review-date {
    color: #666;
    font-size: 0.9em;
    margin-left: 10px;
}

.review-rating .fa-star {
    color: #ddd;
}

.review-rating .fa-star.filled {
    color: #ffd700;
}

.review-text {
    margin: 0;
    line-height: 1.5;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: opacity 0.3s;
}

.action-btn:hover {
    opacity: 0.9;
}

.approve-btn {
    background-color: #28a745;
}

.reject-btn {
    background-color: #ffc107;
    color: #000;
}

.delete-btn {
    background-color: #dc3545;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
}

.status-pending {
    background-color: #ffd700;
    color: #000;
}

.status-approved {
    background-color: #28a745;
    color: white;
}

.status-rejected {
    background-color: #dc3545;
    color: white;
}

.no-data {
    color: #666;
    font-style: italic;
    text-align: center;
    padding: 20px;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include('includes/footer.php'); ?> 