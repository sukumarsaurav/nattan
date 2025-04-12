<?php
// Database connection
$conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add missing columns to consultants table
$alter_table_sql = "
ALTER TABLE consultants 
ADD COLUMN IF NOT EXISTS password VARCHAR(255) NOT NULL,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS bio TEXT,
ADD COLUMN IF NOT EXISTS specialization VARCHAR(255),
ADD COLUMN IF NOT EXISTS languages VARCHAR(255),
ADD COLUMN IF NOT EXISTS phone VARCHAR(50)
";

if (mysqli_multi_query($conn, $alter_table_sql)) {
    echo "Successfully added missing columns to consultants table";
} else {
    echo "Error adding columns: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 