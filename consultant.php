<?php
$page_title = "Our Immigration Consultants | CANEXT Immigration";
include('includes/header.php');

// Database connection
$conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all approved consultants
$query = "SELECT * FROM consultants WHERE status = 'approved' ORDER BY membership_plan DESC, id ASC";
$result = mysqli_query($conn, $query);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Our Immigration Consultants</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 700px; margin: 20px auto 0;">Find and book a consultation with one of our licensed immigration experts</p>
    </div>
</section>

<!-- Consultant Listings -->
<section class="section" style="padding: 60px 0;">
    <div class="container">
        <h2 class="section-title">Meet Our Experts</h2>
        <p class="section-subtitle">Our team of regulated Canadian immigration consultants (RCICs) are here to help with your immigration needs</p>
        
        <!-- Filters -->
        <div class="consultant-filters" style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 40px; padding: 20px; background-color: var(--color-gold); border-radius: 10px;">
            <div class="filter-group">
                <label for="specialization" style="display: block; margin-bottom: 5px; font-weight: 500; color: var(--color-dark);">Specialization:</label>
                <select id="specialization" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; background-color: white;">
                    <option value="">All Specializations</option>
                    <option value="Express Entry">Express Entry</option>
                    <option value="Family Sponsorship">Family Sponsorship</option>
                    <option value="Study Permits">Study Permits</option>
                    <option value="Work Permits">Work Permits</option>
                    <option value="Business Immigration">Business Immigration</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="language" style="display: block; margin-bottom: 5px; font-weight: 500; color: var(--color-dark);">Languages:</label>
                <select id="language" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; background-color: white;">
                    <option value="">All Languages</option>
                    <option value="English">English</option>
                    <option value="French">French</option>
                    <option value="Spanish">Spanish</option>
                    <option value="Mandarin">Mandarin</option>
                    <option value="Hindi">Hindi</option>
                    <option value="Punjabi">Punjabi</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="membership" style="display: block; margin-bottom: 5px; font-weight: 500; color: var(--color-dark);">Membership Level:</label>
                <select id="membership" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; background-color: white;">
                    <option value="">All Levels</option>
                    <option value="gold">Gold</option>
                    <option value="silver">Silver</option>
                    <option value="bronze">Bronze</option>
                </select>
            </div>
        </div>
        
        <!-- Consultants Grid -->
        <div class="consultants-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($consultant = mysqli_fetch_assoc($result)): 
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
                    <!-- Consultant Card -->
                    <div class="consultant-card" data-specialization="<?php echo htmlspecialchars($consultant['specialization']); ?>" 
                         data-languages="<?php echo htmlspecialchars($consultant['languages']); ?>"
                         data-membership="<?php echo htmlspecialchars($consultant['membership_plan']); ?>"
                         style="background-color: var(--color-light); border-radius: 10px; overflow: hidden; position: relative;">
                        
                        <!-- Membership Badge -->
                        <?php if ($consultant['membership_plan']): ?>
                        <div style="position: absolute; top: 10px; right: 10px; background-color: <?php echo $badge_color; ?>; color: #333; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; z-index: 2;">
                            <?php echo $badge_text; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Consultant Image -->
                        <div style="height: 220px; background-image: url('<?php echo $consultant['profile_image'] ? 'images/consultants/' . $consultant['profile_image'] : 'images/consultant-placeholder.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                        
                        <!-- Consultant Info -->
                        <div style="padding: 20px;">
                            <h3 style="font-size: 1.3rem; margin-bottom: 5px; color: var(--color-burgundy);"><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h3>
                            
                            <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">RCIC #<?php echo htmlspecialchars($consultant['rcic_number']); ?></p>
                            
                            <div style="display: flex; margin-bottom: 15px; flex-wrap: wrap; gap: 5px;">
                                <?php 
                                    $specializations = explode(',', $consultant['specialization']);
                                    foreach($specializations as $spec): 
                                        $spec = trim($spec);
                                        if(!empty($spec)):
                                ?>
                                    <span style="background-color: var(--color-gold); font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; color: var(--color-dark);"><?php echo htmlspecialchars($spec); ?></span>
                                <?php 
                                        endif;
                                    endforeach; 
                                ?>
                            </div>
                            
                            <p style="margin-bottom: 20px; font-size: 0.9rem; color: #555;">
                                <?php echo substr(htmlspecialchars($consultant['bio']), 0, 100) . '...'; ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="font-weight: 600; color: var(--color-burgundy);">Starting at $<?php echo htmlspecialchars($consultant['consultation_fee']); ?></span>
                                </div>
                                <a href="<?php echo $base; ?>/consultant/consultant-profile.php?id=<?php echo $consultant['id']; ?>" class="btn btn-primary" style="padding: 8px 15px; font-size: 0.9rem;">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #666;">
                    <h3>No consultants available at the moment</h3>
                    <p>Please check back later or contact our support team for assistance.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Become a Consultant -->
<section class="section" style="background-color: var(--color-gold); padding: 60px 0;">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <h2 class="section-title">Are You a Licensed Immigration Consultant?</h2>
            <p style="margin-bottom: 30px; font-size: 1.1rem;">Join our platform to connect with clients from around the world and grow your practice</p>
            
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; margin-bottom: 40px;">
                <!-- Benefits -->
                <div style="flex: 1; min-width: 250px; text-align: left; background: white; padding: 20px; border-radius: 10px;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <i class="fas fa-check-circle" style="color: var(--color-burgundy); font-size: 1.5rem; margin-right: 10px;"></i>
                        <h3 style="margin: 0; font-size: 1.1rem;">Expand Your Reach</h3>
                    </div>
                    <p style="color: #666; font-size: 0.9rem;">Connect with clients from around the world looking for immigration assistance</p>
                </div>
                
                <div style="flex: 1; min-width: 250px; text-align: left; background: white; padding: 20px; border-radius: 10px;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <i class="fas fa-calendar-check" style="color: var(--color-burgundy); font-size: 1.5rem; margin-right: 10px;"></i>
                        <h3 style="margin: 0; font-size: 1.1rem;">Easy Booking Management</h3>
                    </div>
                    <p style="color: #666; font-size: 0.9rem;">Manage your appointments and client communications all in one place</p>
                </div>
                
                <div style="flex: 1; min-width: 250px; text-align: left; background: white; padding: 20px; border-radius: 10px;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <i class="fas fa-star" style="color: var(--color-burgundy); font-size: 1.5rem; margin-right: 10px;"></i>
                        <h3 style="margin: 0; font-size: 1.1rem;">Build Your Reputation</h3>
                    </div>
                    <p style="color: #666; font-size: 0.9rem;">Showcase your expertise and collect client testimonials to grow your practice</p>
                </div>
            </div>
            
            <a href="consultant-register.php" class="btn btn-primary" style="padding: 12px 30px;">Join as a Consultant</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const specializationFilter = document.getElementById('specialization');
    const languageFilter = document.getElementById('language');
    const membershipFilter = document.getElementById('membership');
    const consultantCards = document.querySelectorAll('.consultant-card');
    
    // Apply filters function
    function applyFilters() {
        const specializationValue = specializationFilter.value.toLowerCase();
        const languageValue = languageFilter.value.toLowerCase();
        const membershipValue = membershipFilter.value.toLowerCase();
        
        consultantCards.forEach(card => {
            const cardSpecializations = card.getAttribute('data-specialization').toLowerCase();
            const cardLanguages = card.getAttribute('data-languages').toLowerCase();
            const cardMembership = card.getAttribute('data-membership').toLowerCase();
            
            // Check if card matches all selected filters
            const matchesSpecialization = !specializationValue || cardSpecializations.includes(specializationValue);
            const matchesLanguage = !languageValue || cardLanguages.includes(languageValue);
            const matchesMembership = !membershipValue || cardMembership === membershipValue;
            
            // Show or hide based on filter matches
            if (matchesSpecialization && matchesLanguage && matchesMembership) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Add event listeners to filters
    specializationFilter.addEventListener('change', applyFilters);
    languageFilter.addEventListener('change', applyFilters);
    membershipFilter.addEventListener('change', applyFilters);
});
</script>

<?php 
mysqli_close($conn);
include('includes/footer.php'); 
?>

