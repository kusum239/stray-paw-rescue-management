<?php

$host = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$conn->query("
    CREATE DATABASE IF NOT EXISTS stray_paw_management
");

$conn->select_db("stray_paw_management");


/* USERS */

$conn->query("
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    address VARCHAR(255),
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");


/* ADMIN */

$conn->query("
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");


/* VOLUNTEER APPLICATION */

$conn->query("
CREATE TABLE IF NOT EXISTS volunteer_application (
    application_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    skills TEXT,
    availability VARCHAR(100),
    experience TEXT,

    status ENUM(
        'Pending',
        'Approved',
        'Rejected'
    ) DEFAULT 'Pending',

    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
) ENGINE=InnoDB
");


/* VOLUNTEERS */

$conn->query("
CREATE TABLE IF NOT EXISTS volunteers (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL UNIQUE,

    application_id INT NOT NULL UNIQUE,

    status ENUM(
        'Active',
        'Inactive'
    ) DEFAULT 'Active',

    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (application_id)
        REFERENCES volunteer_application(application_id)
        ON DELETE CASCADE
) ENGINE=InnoDB
");


/* RESCUE REQUESTS */

$conn->query("
CREATE TABLE IF NOT EXISTS rescue_requests (

    request_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    assigned_admin_id INT NULL,

    assigned_volunteer_id INT NULL,

    animal_type ENUM(
        'Dog',
        'Cat'
    ) NOT NULL,

    condition_type ENUM(
        'Injured',
        'Stray',
        'Abandoned'
    ) NOT NULL,

    photo VARCHAR(255),

    video VARCHAR(255),

    description TEXT NOT NULL,

    location VARCHAR(255) NOT NULL,

    emergency ENUM(
        'Yes',
        'No'
    ) DEFAULT 'No',

    status ENUM(
        'Pending',
        'Under Review',
        'Assigned',
        'In Progress',
        'Rescued',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (assigned_admin_id)
        REFERENCES admin(admin_id)
        ON DELETE SET NULL,

    FOREIGN KEY (assigned_volunteer_id)
        REFERENCES volunteers(volunteer_id)
        ON DELETE SET NULL

) ENGINE=InnoDB
");


/* RESCUED ANIMAL */

$conn->query("
CREATE TABLE IF NOT EXISTS rescued_animal (

    animal_id INT AUTO_INCREMENT PRIMARY KEY,

    request_id INT NOT NULL UNIQUE,

    animal_type ENUM(
        'Dog',
        'Cat'
    ) NOT NULL,

    name VARCHAR(100),

    breed VARCHAR(100),

    age VARCHAR(50),

    gender VARCHAR(20),

    color VARCHAR(50),

    photo VARCHAR(255),

    health_condition TEXT,

    rescued_date DATE,

    current_status ENUM(
        'Rescued',
        'Under Treatment',
        'In Shelter',
        'Recovered'
    ) DEFAULT 'Rescued',

    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (request_id)
        REFERENCES rescue_requests(request_id)
        ON DELETE CASCADE

) ENGINE=InnoDB
");


/* TREATMENT HISTORY */

$conn->query("
CREATE TABLE IF NOT EXISTS treatment_history (

    treatment_id INT AUTO_INCREMENT PRIMARY KEY,

    animal_id INT NOT NULL,

    admin_id INT NULL,

    treatment_date DATE NOT NULL,

    treatment_type VARCHAR(150),

    description TEXT,

    medication VARCHAR(255),

    vet_name VARCHAR(100),

    next_visit_date DATE,

    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (animal_id)
        REFERENCES rescued_animal(animal_id)
        ON DELETE CASCADE,

    FOREIGN KEY (admin_id)
        REFERENCES admin(admin_id)
        ON DELETE SET NULL

) ENGINE=InnoDB
");


/* SHELTERS */

$conn->query("
CREATE TABLE IF NOT EXISTS shelters (

    shelter_id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    address VARCHAR(255) NOT NULL,

    contact_number VARCHAR(30),

    capacity INT DEFAULT 0,

    current_occupancy INT DEFAULT 0,

    description TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
");


/* ANIMAL SHELTER */

$conn->query("
CREATE TABLE IF NOT EXISTS animal_shelter (

    assignment_id INT AUTO_INCREMENT PRIMARY KEY,

    animal_id INT NOT NULL,

    shelter_id INT NOT NULL,

    check_in_date DATE NOT NULL,

    check_out_date DATE NULL,

    status ENUM(
        'Active',
        'Released'
    ) DEFAULT 'Active',

    FOREIGN KEY (animal_id)
        REFERENCES rescued_animal(animal_id)
        ON DELETE CASCADE,

    FOREIGN KEY (shelter_id)
        REFERENCES shelters(shelter_id)
        ON DELETE CASCADE

) ENGINE=InnoDB
");


/* DEFAULT ADMIN */

$email = "admin@straypaw.com";

$check = $conn->prepare(
    "SELECT admin_id FROM admin WHERE email = ?"
);

$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();

if ($result->num_rows == 0) {

    $name = "Admin";

    $password_hash = password_hash(
        "admin123",
        PASSWORD_DEFAULT
    );

    $stmt = $conn->prepare("
        INSERT INTO admin
        (name, email, password)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "sss",
        $name,
        $email,
        $password_hash
    );

    $stmt->execute();

    $stmt->close();
}

$check->close();

?>

<!DOCTYPE html>

<html>

<head>

    <title>Stray Paw Setup</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #fff8f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .box {
            background: white;
            padding: 40px;
            width: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0,0,0,.08);
        }

        h1 {
            margin-top: 0;
        }

        .success {
            color: #6b8e65;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>Stray Paw</h1>

    <p class="success">
        Database migration completed successfully.
    </p>

    <p>
        Database:
        <strong>stray_paw_management</strong>
    </p>

    <hr>

    <p>
        <strong>Admin Login</strong>
    </p>

    <p>
        Email: admin@straypaw.com
    </p>

    <p>
        Password: admin123
    </p>

</div>

</body>

</html>