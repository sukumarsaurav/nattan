<?php
$page_title = "Manage Consultants | Admin Dashboard";
$current_page = 'consultants.php';
include('includes/header.php');
include('includes/db_connection.php');

// Handle AJAX requests
if (isset($_POST['ajax_action'])) {
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['consultant_id'])) {
        $consultant_id = mysqli_real_escape_string($conn, $_POST['consultant_id']);
        $action = $_POST['action'];
        
        try {
            if ($action === 'approve') {
                executeQuery("UPDATE consultants SET status = 'approved' WHERE id = '$consultant_id'");
            } else if ($action === 'reject') {
                executeQuery("UPDATE consultants SET status = 'rejected' WHERE id = '$consultant_id'");
            } else if ($action === 'delete') {
                executeQuery("DELETE FROM consultants WHERE id = '$consultant_id'");
            }
            $response['success'] = true;
            $response['message'] = 'Action completed successfully';
        } catch (Exception $e) {
            $response['message'] = 'Error performing action';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get all consultants
$query = "SELECT * FROM consultants ORDER BY created_at DESC";
$result = executeQuery($query);
?>

<div class="admin-content-header">
    <h1><i class="fas fa-user-tie"></i> Manage Consultants</h1>
    <p>Manage and review consultant registrations</p>
</div>

<div class="admin-controls">
    <div class="admin-search">
        <input type="text" placeholder="Search consultants..." id="consultant-search">
        <button><i class="fas fa-search"></i></button>
    </div>
    
    <div class="admin-filters">
        <select id="statusFilter" onchange="filterConsultants()" class="form-control">
            <option value="all">All Consultants</option>
            <option value="pending">Pending Approval</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table consultant-table">
        <thead>
            <tr>
                <th>Name <i class="fas fa-sort"></i></th>
                <th>Email <i class="fas fa-sort"></i></th>
                <th>RCIC Number <i class="fas fa-sort"></i></th>
                <th>Status <i class="fas fa-sort"></i></th>
                <th>Registration Date <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($consultant = $result->fetch_assoc()): ?>
                    <tr class="consultant-row" data-status="<?php echo $consultant['status']; ?>">
                        <td>
                            <a href="consultant_profile.php?id=<?php echo $consultant['id']; ?>" class="consultant-name-link">
                                <?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($consultant['email']); ?></td>
                        <td><?php echo htmlspecialchars($consultant['rcic_number']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $consultant['status']; ?>">
                                <?php echo ucfirst($consultant['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($consultant['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="consultant_profile.php?id=<?php echo $consultant['id']; ?>" class="action-btn view-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($consultant['status'] === 'pending'): ?>
                                    <button onclick="handleAction('approve', <?php echo $consultant['id']; ?>)" class="action-btn approve-btn" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="handleAction('reject', <?php echo $consultant['id']; ?>)" class="action-btn reject-btn" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <button onclick="handleAction('delete', <?php echo $consultant['id']; ?>)" class="action-btn delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No consultants found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.admin-content-header {
    margin-bottom: 30px;
}

.admin-content-header h1 {
    font-size: 1.8rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.admin-content-header p {
    color: #666;
    margin: 5px 0 0;
}

.admin-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 20px;
}

.admin-search {
    display: flex;
    gap: 10px;
}

.admin-search input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 250px;
}

.admin-table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background-color: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}

.consultant-name-link {
    color: var(--color-burgundy);
    text-decoration: none;
    font-weight: 500;
}

.consultant-name-link:hover {
    text-decoration: underline;
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

.action-buttons {
    display: flex;
    gap: 5px;
}

.action-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9em;
}

.view-btn {
    background-color: #17a2b8;
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

.action-btn:hover {
    opacity: 0.9;
}
</style>

<script>
function filterConsultants() {
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.consultant-row');
    
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Add search functionality
document.getElementById('consultant-search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.consultant-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

function handleAction(action, consultantId) {
    if (!confirm('Are you sure you want to ' + action + ' this consultant?')) {
        return;
    }

    const formData = new FormData();
    formData.append('ajax_action', true);
    formData.append('action', action);
    formData.append('consultant_id', consultantId);

    fetch('consultants.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while performing the action');
    });
}
</script>

<?php include('includes/footer.php'); ?> 