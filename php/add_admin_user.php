<?php
require_once __DIR__ . '/../databases/connection1.php';

$email = 'keithyvheaiv@gmail.com';
$password = 'Admin123!';
$first = 'Kim';
$last = 'Fernando';
$gender = 'male';
$mobile = '09650523732';
$baranggay = 'Dadiangas Nort';
$city = 'General Santos City';
$province = 'South Cotabato';
$zipcode = 9500;
$birthDate = '1990-01-01';
$role = 'admin';

try {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "User already exists: {$email}\n";
        exit(0);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO users (first_name, last_name, gender, birth_date, mobile, email, baranggay, city, province, zipcode, password, role, created_at, updated_At) '
         . 'VALUES (:first, :last, :gender, :birth_date, :mobile, :email, :baranggay, :city, :province, :zipcode, :password, :role, NOW(), NOW())';
    $ins = $conn->prepare($sql);
    $ins->execute([
        ':first' => $first,
        ':last' => $last,
        ':gender' => $gender,
        ':birth_date' => $birthDate,
        ':mobile' => $mobile,
        ':email' => $email,
        ':baranggay' => $baranggay,
        ':city' => $city,
        ':province' => $province,
        ':zipcode' => $zipcode,
        ':password' => $hash,
        ':role' => $role,
    ]);

    echo "Admin user inserted: {$email} with password: {$password}\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
