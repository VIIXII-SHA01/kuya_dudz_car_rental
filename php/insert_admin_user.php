<?php
// insert_admin_user.php
// Run this script once to insert a sample admin user into the users table.

require_once __DIR__ . '/../databases/connection1.php';

try {
    $email = 'admin@example.com';
    $passwordPlain = 'Admin@123';

    // Use password_hash to store a secure password hash.
    $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

    // Avoid duplicate users by checking the email first.
    $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $checkStmt->execute([':email' => $email]);

    if ($checkStmt->fetch()) {
        echo "User with email '$email' already exists. No new row inserted.";
        exit;
    }

    $stmt = $conn->prepare(
        'INSERT INTO users (
            first_name,
            last_name,
            gender,
            birth_date,
            mobile,
            email,
            baranggay,
            city,
            province,
            zipcode,
            password,
            profile_pic,
            role
        ) VALUES (
            :first_name,
            :last_name,
            :gender,
            :birth_date,
            :mobile,
            :email,
            :baranggay,
            :city,
            :province,
            :zipcode,
            :password,
            :profile_pic,
            :role
        )'
    );

    $stmt->execute([
        ':first_name'  => 'Admin',
        ':last_name'   => 'User',
        ':gender'      => 'Male',
        ':birth_date'  => '1985-01-01',
        ':mobile'      => '09171234567',
        ':email'       => $email,
        ':baranggay'   => 'Barangay 1',
        ':city'        => 'Manila',
        ':province'    => 'Metro Manila',
        ':zipcode'     => 1000,
        ':password'    => $passwordHash,
        ':profile_pic' => 'admin-profile.jpg',
        ':role'        => 'admin',
    ]);

    echo "Admin user inserted successfully with email '$email'.";
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
