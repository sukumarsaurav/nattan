<?php
$page_title = "Consultant Profile | CANEXT Immigration";
include('../includes/header.php');

// Database connection
$conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if consultant ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consultant.php");
    exit();
}

$consultant_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch consultant details
$query = "SELECT * FROM consultants WHERE id = '$consultant_id' AND status = 'approved'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Consultant not found or not approved
    header("Location: consultant.php");
    exit();
}

$consultant = mysqli_fetch_assoc($result);

// Fetch consultant reviews
$review_query = "SELECT * FROM consultant_reviews WHERE consultant_id = '$consultant_id' ORDER BY created_at DESC LIMIT 5";
$reviews = mysqli_query($conn, $review_query);

// Calculate average rating
$rating_query = "SELECT AVG(rating) as average_rating FROM consultant_reviews WHERE consultant_id = '$consultant_id'";
$rating_result = mysqli_query($conn, $rating_query);
$average_rating = mysqli_fetch_assoc($rating_result)['average_rating'] ?? 0;
$average_rating = round($average_rating, 1);

// Get membership badge styling
$badge_color = "";
$badge_text = "";

switch($consultant['membership_plan']) {
    case 'gold':
        $badge_color = "#FFD700";
        $badge_text = "Gold Member";
        break;
    case 'silver':
        $badge_color = "#C0C0C0";
        $badge_text = "Silver Member";
        break;
    case 'bronze':
        $badge_color = "#CD7F32";
        $badge_text = "Bronze Member";
        break;
    default:
        $badge_color = "#EFEFEF";
        $badge_text = "Member";
}
?>

<!-- Profile Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 80px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 30px; max-width: 1000px; margin: 0 auto;">
            <!-- Consultant Photo -->
            <div style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 4px solid white; position: relative;">
                <img src="<?php echo $consultant['profile_image'] ? '../images/consultants/' . $consultant['profile_image'] : '../images/consultant-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                
                <?php if ($consultant['membership_plan']): ?>
                <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); background-color: <?php echo $badge_color; ?>; color: #333; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; white-space: nowrap;">
                    <?php echo $badge_text; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Consultant Info -->
            <div style="text-align: left; flex: 1; min-width: 250px;">
                <h1 style="margin-bottom: 5px;"><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 10px;">Licensed Immigration Consultant (RCIC #<?php echo htmlspecialchars($consultant['rcic_number']); ?>)</p>
                
                <!-- Rating Stars -->
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="display: flex; gap: 2px; margin-right: 10px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $average_rating): ?>
                                <i class="fas fa-star" style="color: gold;"></i>
                            <?php elseif ($i <= $average_rating + 0.5): ?>
                                <i class="fas fa-star-half-alt" style="color: gold;"></i>
                            <?php else: ?>
                                <i class="far fa-star" style="color: gold;"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span>(<?php echo $average_rating; ?>/5)</span>
                </div>
                
                <!-- Languages -->
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                    <?php 
                        $languages = explode(',', $consultant['languages']);
                        foreach($languages as $language): 
                            $language = trim($language);
                            if(!empty($language)):
                    ?>
                        <span style="background-color: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;">
                            <i class="fas fa-language" style="margin-right: 5px;"></i><?php echo htmlspecialchars($language); ?>
                        </span>
                    <?php 
                            endif;
                        endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="section" style="padding: 60px 0;">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <!-- Left Column (About & Reviews) -->
            <div style="flex: 2; min-width: 300px;">
                <!-- About Section -->
                <div style="background-color: var(--color-light); border-radius: 10px; padding: 30px; margin-bottom: 30px;">
                    <h2 style="margin-bottom: 20px; color: var(--color-burgundy); font-size: 1.5rem;">About</h2>
                    <div style="line-height: 1.7; color: #444;">
                        <?php echo nl2br(htmlspecialchars($consultant['bio'])); ?>
                    </div>
                    
                    <!-- Specializations -->
                    <div style="margin-top: 30px;">
                        <h3 style="margin-bottom: 15px; color: var(--color-burgundy); font-size: 1.2rem;">Specializations</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php 
                                $specializations = explode(',', $consultant['specialization']);
                                foreach($specializations as $spec): 
                                    $spec = trim($spec);
                                    if(!empty($spec)):
                            ?>
                                <span style="background-color: var(--color-gold); padding: 5px 12px; border-radius: 5px; font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($spec); ?>
                                </span>
                            <?php 
                                    endif;
                                endforeach; 
                            ?>
                        </div>
                    </div>
                    
                    <!-- Experience -->
                    <div style="margin-top: 30px;">
                        <h3 style="margin-bottom: 15px; color: var(--color-burgundy); font-size: 1.2rem;">Experience</h3>
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px;">
                            <div style="font-weight: 600; color: var(--color-dark);">Years in Practice:</div>
                            <div><?php echo htmlspecialchars($consultant['years_experience']); ?> years</div>
                            
                            <div style="font-weight: 600; color: var(--color-dark);">Successful Cases:</div>
                            <div><?php echo htmlspecialchars($consultant['successful_cases']); ?>+</div>
                            
                            <div style="font-weight: 600; color: var(--color-dark);">Languages:</div>
                            <div><?php echo htmlspecialchars($consultant['languages']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews Section -->
                <div style="background-color: var(--color-light); border-radius: 10px; padding: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.5rem;">Client Reviews</h2>
                        <span style="font-size: 1.1rem; font-weight: 600;"><?php echo $average_rating; ?>/5 
                            <span style="color: gold; margin-left: 5px;">
                                <i class="fas fa-star"></i>
                            </span>
                        </span>
                    </div>
                    
                    <?php if (mysqli_num_rows($reviews) > 0): ?>
                        <?php while($review = mysqli_fetch_assoc($reviews)): ?>
                            <div style="border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 5px; color: var(--color-dark);">
                                            <?php echo htmlspecialchars($review['client_name']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 2px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['rating']): ?>
                                                <i class="fas fa-star" style="color: gold;"></i>
                                            <?php else: ?>
                                                <i class="far fa-star" style="color: gold;"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="margin: 0; color: #444; line-height: 1.6;">
                                    <?php echo htmlspecialchars($review['review_text']); ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: #666;">
                            <p>No reviews yet. Be the first to leave a review!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column (Booking) -->
            <div style="flex: 1; min-width: 300px;">
                <!-- Consultation Options -->
                <div style="background-color: var(--color-light); border-radius: 10px; padding: 30px; margin-bottom: 30px; position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 25px; color: var(--color-burgundy); font-size: 1.4rem; text-align: center;">Book a Consultation</h2>
                    
                    <form id="consultationForm" action="process_booking.php" method="post">
                        <input type="hidden" name="consultant_id" value="<?php echo $consultant_id; ?>">
                        
                        <!-- Consultation Type Selection -->
                        <div class="form-group">
                            <label>Select Consultation Type:</label>
                            
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <?php if ($consultant['video_consultation_available'] == 1): ?>
                                <div class="consultation-option">
                                    <input type="radio" name="consultation_type" id="videoConsultation" value="video" style="display: none;">
                                    <label for="videoConsultation" style="cursor: pointer; display: flex; justify-content: space-between; width: 100%;">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fas fa-video" style="color: var(--color-burgundy); font-size: 1.2rem; margin-right: 10px;"></i>
                                            <span>Video Consultation</span>
                                        </div>
                                        <span>$<?php echo htmlspecialchars($consultant['video_consultation_fee']); ?></span>
                                    </label>
                                    <div style="margin-top: 10px; font-size: 0.9rem; color: #666;">60 minutes via video call</div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($consultant['phone_consultation_available'] == 1): ?>
                                <div class="consultation-option">
                                    <input type="radio" name="consultation_type" id="phoneConsultation" value="phone" style="display: none;">
                                    <label for="phoneConsultation" style="cursor: pointer; display: flex; justify-content: space-between; width: 100%;">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fas fa-phone-alt" style="color: var(--color-burgundy); font-size: 1.2rem; margin-right: 10px;"></i>
                                            <span>Phone Consultation</span>
                                        </div>
                                        <span>$<?php echo htmlspecialchars($consultant['phone_consultation_fee']); ?></span>
                                    </label>
                                    <div style="margin-top: 10px; font-size: 0.9rem; color: #666;">45 minutes via phone call</div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($consultant['in_person_consultation_available'] == 1): ?>
                                <div class="consultation-option">
                                    <input type="radio" name="consultation_type" id="inPersonConsultation" value="in_person" style="display: none;">
                                    <label for="inPersonConsultation" style="cursor: pointer; display: flex; justify-content: space-between; width: 100%;">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fas fa-user" style="color: var(--color-burgundy); font-size: 1.2rem; margin-right: 10px;"></i>
                                            <span>In-Person Consultation</span>
                                        </div>
                                        <span>$<?php echo htmlspecialchars($consultant['in_person_consultation_fee']); ?></span>
                                    </label>
                                    <div style="margin-top: 10px; font-size: 0.9rem; color: #666;">60 minutes at consultant's office</div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Date Selection -->
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="consultationDate" style="display: block; margin-bottom: 10px; font-weight: 500; color: var(--color-dark);">Select Date:</label>
                            <input type="date" id="consultationDate" name="consultation_date" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+60 days')); ?>">
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 10px; font-weight: 500; color: var(--color-dark);">Select Time:</label>
                            <div class="time-slots" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
                                <div class="time-slot" data-time="09:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">9:00 AM</div>
                                <div class="time-slot" data-time="10:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">10:00 AM</div>
                                <div class="time-slot" data-time="11:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">11:00 AM</div>
                                <div class="time-slot" data-time="12:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">12:00 PM</div>
                                <div class="time-slot" data-time="13:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">1:00 PM</div>
                                <div class="time-slot" data-time="14:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">2:00 PM</div>
                                <div class="time-slot" data-time="15:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">3:00 PM</div>
                                <div class="time-slot" data-time="16:00:00" style="text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">4:00 PM</div>
                            </div>
                            <input type="hidden" id="consultationTime" name="consultation_time" required>
                        </div>
                        
                        <!-- Error Message -->
                        <div id="bookingError" style="display: none; color: #dc3545; text-align: center; margin: 15px 0; padding: 8px; border-radius: 5px; background-color: #ffe6e6; font-size: 0.9rem;"></div>
                        
                        <!-- Book Button -->
                        <button type="submit" id="bookBtn" class="btn btn-primary" style="width: 100%; padding: 12px;" disabled>Book Consultation</button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div style="background-color: var(--color-light); border-radius: 10px; padding: 30px;">
                    <h3 style="margin-bottom: 20px; color: var(--color-burgundy); font-size: 1.2rem;">Contact Information</h3>
                    
                    <?php if (!empty($consultant['office_address'])): ?>
                    <div style="display: flex; margin-bottom: 15px;">
                        <i class="fas fa-map-marker-alt" style="color: var(--color-burgundy); margin-right: 12px; min-width: 20px; text-align: center;"></i>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 3px; color: var(--color-dark);">Office Address</div>
                            <div style="color: #666;"><?php echo nl2br(htmlspecialchars($consultant['office_address'])); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consultant['office_hours'])): ?>
                    <div style="display: flex; margin-bottom: 15px;">
                        <i class="fas fa-clock" style="color: var(--color-burgundy); margin-right: 12px; min-width: 20px; text-align: center;"></i>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 3px; color: var(--color-dark);">Office Hours</div>
                            <div style="color: #666;"><?php echo nl2br(htmlspecialchars($consultant['office_hours'])); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consultant['email'])): ?>
                    <div style="display: flex; margin-bottom: 15px;">
                        <i class="fas fa-envelope" style="color: var(--color-burgundy); margin-right: 12px; min-width: 20px; text-align: center;"></i>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 3px; color: var(--color-dark);">Email</div>
                            <div style="color: #666;"><?php echo htmlspecialchars($consultant['email']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consultant['phone'])): ?>
                    <div style="display: flex;">
                        <i class="fas fa-phone" style="color: var(--color-burgundy); margin-right: 12px; min-width: 20px; text-align: center;"></i>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 3px; color: var(--color-dark);">Phone</div>
                            <div style="color: #666;"><?php echo htmlspecialchars($consultant['phone']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consultationOptions = document.querySelectorAll('.consultation-option');
    const timeSlots = document.querySelectorAll('.time-slot');
    const consultationTime = document.getElementById('consultationTime');
    const consultationDate = document.getElementById('consultationDate');
    const bookBtn = document.getElementById('bookBtn');
    const bookingError = document.getElementById('bookingError');
    
    // Select consultation type
    consultationOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Highlight selected option
            consultationOptions.forEach(opt => {
                opt.style.borderColor = '#ddd';
                opt.style.backgroundColor = 'white';
                const radio = opt.querySelector('input[type="radio"]');
                radio.checked = false;
            });
            
            this.style.borderColor = 'var(--color-burgundy)';
            this.style.backgroundColor = 'var(--color-gold)';
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            updateBookButton();
        });
    });
    
    // Date input change
    consultationDate.addEventListener('change', function() {
        // Reset time slot selection
        timeSlots.forEach(slot => {
            slot.style.backgroundColor = '';
            slot.style.color = '';
            slot.style.borderColor = '#ddd';
        });
        
        consultationTime.value = '';
        updateBookButton();
        
        // In a real app, you would fetch available time slots for this date
        // For this demo, we'll just simulate all slots being available
    });
    
    // Select time slot
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            if (consultationDate.value === '') {
                showError('Please select a date first');
                return;
            }
            
            // Reset all slots
            timeSlots.forEach(s => {
                s.style.backgroundColor = '';
                s.style.color = '';
                s.style.borderColor = '#ddd';
            });
            
            // Highlight selected slot
            this.style.backgroundColor = 'var(--color-burgundy)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--color-burgundy)';
            
            consultationTime.value = this.getAttribute('data-time');
            updateBookButton();
        });
    });
    
    // Form submission
    document.getElementById('consultationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if all required fields are filled
        const consultationType = document.querySelector('input[name="consultation_type"]:checked');
        
        if (!consultationType) {
            showError('Please select a consultation type');
            return;
        }
        
        if (consultationDate.value === '') {
            showError('Please select a date');
            return;
        }
        
        if (consultationTime.value === '') {
            showError('Please select a time slot');
            return;
        }
        
        // If all validations pass, redirect to details page
        window.location.href = `consultant_details.php?consultant_id=${<?php echo $consultant_id; ?>}&type=${encodeURIComponent(consultationType.value)}&date=${encodeURIComponent(consultationDate.value)}&time=${encodeURIComponent(consultationTime.value)}`;
    });
    
    function updateBookButton() {
        const consultationType = document.querySelector('input[name="consultation_type"]:checked');
        
        if (consultationType && consultationDate.value && consultationTime.value) {
            bookBtn.disabled = false;
        } else {
            bookBtn.disabled = true;
        }
    }
    
    function showError(message) {
        bookingError.textContent = message;
        bookingError.style.display = 'block';
        
        // Hide error message after 3 seconds
        setTimeout(() => {
            bookingError.style.display = 'none';
        }, 3000);
    }
});
</script>

<?php 
mysqli_close($conn);
include('../includes/footer.php'); 
?> 