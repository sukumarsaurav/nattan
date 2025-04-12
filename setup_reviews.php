<?php
// Database connection
$conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add missing columns to consultants table
$alter_consultants_sql = "
ALTER TABLE consultants 
ADD COLUMN IF NOT EXISTS video_consultation_available TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS phone_consultation_available TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS in_person_consultation_available TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS video_consultation_fee DECIMAL(10,2) DEFAULT 150.00,
ADD COLUMN IF NOT EXISTS phone_consultation_fee DECIMAL(10,2) DEFAULT 100.00,
ADD COLUMN IF NOT EXISTS in_person_consultation_fee DECIMAL(10,2) DEFAULT 200.00,
ADD COLUMN IF NOT EXISTS years_experience INT DEFAULT 5,
ADD COLUMN IF NOT EXISTS successful_cases INT DEFAULT 100,
ADD COLUMN IF NOT EXISTS office_address TEXT,
ADD COLUMN IF NOT EXISTS office_hours TEXT,
ADD COLUMN IF NOT EXISTS email VARCHAR(255),
ADD COLUMN IF NOT EXISTS phone VARCHAR(50)
";

if (mysqli_multi_query($conn, $alter_consultants_sql)) {
    echo "Added consultation columns to consultants table<br>";
    // Clear results
    while (mysqli_next_result($conn)) {;}
} else {
    echo "Error adding columns: " . mysqli_error($conn) . "<br>";
}

// Update sample data with consultation information
$update_consultants_sql = "
UPDATE consultants SET 
    video_consultation_available = 1,
    phone_consultation_available = 1,
    in_person_consultation_available = 1,
    video_consultation_fee = 150.00,
    phone_consultation_fee = 100.00,
    in_person_consultation_fee = 200.00,
    years_experience = 8,
    successful_cases = 250,
    office_address = '123 Immigration Street\\nToronto, ON M5V 2H1\\nCanada',
    office_hours = 'Monday - Friday: 9:00 AM - 5:00 PM\\nSaturday: By appointment\\nSunday: Closed',
    email = CONCAT(LOWER(first_name), '.', LOWER(last_name), '@canext.ca'),
    phone = '+1 (416) 555-0123'
";

if (mysqli_query($conn, $update_consultants_sql)) {
    echo "Updated consultants with consultation information<br>";
} else {
    echo "Error updating consultants: " . mysqli_error($conn) . "<br>";
}

// Create consultant_reviews table
$create_table_sql = "CREATE TABLE IF NOT EXISTS consultant_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultant_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_table_sql)) {
    echo "Table consultant_reviews created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Sample review data
$sample_reviews = [
    [1, 'John Smith', 5, 'Sarah was extremely helpful throughout my Express Entry process. Her knowledge and guidance made the journey smooth.', 'approved'],
    [1, 'Mary Johnson', 5, 'Excellent service! Very professional and knowledgeable about the immigration process.', 'approved'],
    [2, 'Wei Chen', 5, 'Michael\'s expertise in business immigration was invaluable. Highly recommended!', 'approved'],
    [2, 'Linda Zhang', 4, 'Very professional service. Good communication throughout the process.', 'approved'],
    [3, 'Amit Patel', 5, 'Priya helped me with my study permit. Her guidance was crucial to my success.', 'approved'],
    [4, 'Sophie Martin', 5, 'Jean\'s bilingual expertise made the process much easier for me.', 'approved'],
    [5, 'James Wilson', 4, 'David provided clear and practical advice for my work permit application.', 'approved'],
    [6, 'Carlos Rodriguez', 5, 'Maria was fantastic! She helped my family through the sponsorship process.', 'approved'],
    [7, 'Deepak Singh', 5, 'Raj\'s knowledge of the student visa process is exceptional.', 'approved'],
    [8, 'Tom Lee', 5, 'Lisa\'s expertise in business immigration helped me establish my business in Canada.', 'approved']
];

// Clear existing reviews
mysqli_query($conn, "TRUNCATE TABLE consultant_reviews");

// Insert sample reviews
$insert_sql = "INSERT INTO consultant_reviews (consultant_id, client_name, rating, review_text, status) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $insert_sql);

foreach ($sample_reviews as $review) {
    mysqli_stmt_bind_param($stmt, "isiss", $review[0], $review[1], $review[2], $review[3], $review[4]);
    if (mysqli_stmt_execute($stmt)) {
        echo "Added review for consultant ID " . $review[0] . "<br>";
    } else {
        echo "Error adding review: " . mysqli_stmt_error($stmt) . "<br>";
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
echo "Setup complete!";
?> 