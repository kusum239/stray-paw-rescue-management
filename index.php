<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Stray Paw Rescue Management System
    </title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<section class="hero">

    <img
        src="assets/images/hero-dog.jpg"
        alt="Rescued dog"
        class="hero-background"
    >

    <div class="hero-overlay"></div>

    <div class="hero-card">

        <h1>
            Every Paw Deserves a
            <span>Second Chance.</span>
        </h1>

        <p>
            Join us in our mission to rescue, rehabilitate,
            and rehome stray and injured animals. Your
            vigilance in reporting animals in need makes
            all the difference.
        </p>

        <div class="hero-buttons">

            <a href="report.php" class="hero-report-btn">
                ◉ &nbsp; Report an Animal
            </a>

            <a href="volunteer.php" class="hero-volunteer-btn">
                ♧ &nbsp; Become a Volunteer
            </a>

        </div>

    </div>

</section>

<section class="process-section">

    <div class="process-heading">

        <h2>
            How Our Rescue Operations Work
        </h2>

        <p>
            A simple process from reporting an animal
            to rescue, treatment and shelter.
        </p>

    </div>

    <div class="process-grid">

        <div class="process-card">

            <div class="process-icon">
                1
            </div>

            <h3>
                Report
            </h3>

            <p>
                Community members report stray or injured
                animals with photo, video, description and
                location.
            </p>

        </div>

        <div class="process-card">

            <div class="process-icon">
                2
            </div>

            <h3>
                Review & Rescue
            </h3>

            <p>
                The admin reviews the request and assigns
                an available volunteer.
            </p>

        </div>

        <div class="process-card">

            <div class="process-icon">
                3
            </div>

            <h3>
                Treatment
            </h3>

            <p>
                Rescued animals receive appropriate
                treatment and care.
            </p>

        </div>

        <div class="process-card">

            <div class="process-icon">
                4
            </div>

            <h3>
                Shelter
            </h3>

            <p>
                Animals are assigned to suitable shelters
                while they recover.
            </p>

        </div>

    </div>

</section>

<section class="stats-section">

    <div class="stats-grid">

        <div class="stat-item">

            <h2>
                1200+
            </h2>

            <p>
                Reports Received
            </p>

        </div>

        <div class="stat-item">

            <h2>
                950+
            </h2>

            <p>
                Animals Rescued
            </p>

        </div>

        <div class="stat-item">

            <h2>
                800+
            </h2>

            <p>
                Treatments Completed
            </p>

        </div>

        <div class="stat-item">

            <h2>
                250+
            </h2>

            <p>
                Active Volunteers
            </p>

        </div>

    </div>

</section>

<section class="stories-section">

    <div class="stories-header">

        <div>

            <h2>
                Recent Rescue Stories
            </h2>

            <p>
                Meet the animals we recently rescued
                and cared for.
            </p>

        </div>

        <a href="rescue.php">
            View All Stories →
        </a>

    </div>

    <div class="stories-grid">

        <div class="animal-card">

            <img src="assets/images/dog-1.jpg"
                 alt="Buddy">

            <div class="animal-info">

                <h3>
                    Buddy
                </h3>

                <span class="animal-status">
                    Dog • Rescued
                </span>

                <span class="animal-location">
                    Kathmandu, Nepal
                </span>

                <a href="rescue.php"
                   class="animal-btn">

                    View Details

                </a>

            </div>

        </div>

        <div class="animal-card">

            <img src="assets/images/cat-1.jpg"
                 alt="Luna">

            <div class="animal-info">

                <h3>
                    Luna
                </h3>

                <span class="animal-status">
                    Cat • Treatment
                </span>

                <span class="animal-location">
                    Lalitpur, Nepal
                </span>

                <a href="rescue.php"
                   class="animal-btn">

                    View Details

                </a>

            </div>

        </div>

        <div class="animal-card">

            <img src="assets/images/dog-2.jpg"
                 alt="Max">

            <div class="animal-info">

                <h3>
                    Max
                </h3>

                <span class="animal-status">
                    Dog • Rescued
                </span>

                <span class="animal-location">
                    Bhaktapur, Nepal
                </span>

                <a href="rescue.php"
                   class="animal-btn">

                    View Details

                </a>

            </div>

        </div>

    </div>

</section>

<section class="action-section">

    <h1>
        Your small action can save a life.
    </h1>

    <p>
        Whether you spot a stray in need or have time to lend a hand,
        our community<br>
        relies on compassionate individuals like you.
    </p>

    <div class="action-buttons">

        <a href="report.php" class="btn-primary">
            ◉ &nbsp; Report an Animal
        </a>

        <a href="volunteer.php" class="btn-secondary">
            ♧ &nbsp; Become a Volunteer
        </a>

    </div>

</section>

<?php include "includes/footer.php"; ?>

</body>

</html>