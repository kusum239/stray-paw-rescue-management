
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Stray Paw</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .about-hero {
            background: #fff7ed;
            padding: 45px 20px;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 40px;
        }

        .about-hero h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 12px;
        }

        .about-hero p {
            color: #666;
            font-size: 16px;
        }

        /* Image left, text right */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 40px;
            align-items: center;
            max-width: 1050px;
            margin: 0 auto 55px;
            padding: 20px;
        }

        .about-image {
            width: 100%;
        }

        .about-image img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.12);
        }

        .about-text h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 18px;
        }

        .about-text p {
            color: #555;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            max-width: 1000px;
            margin: 0 auto 55px;
            padding: 0 20px;
        }

        .info-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.07);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card h2 {
            color: #333;
            margin-bottom: 12px;
        }

        .info-card p {
            color: #666;
            line-height: 1.7;
        }

        .what-we-do {
            max-width: 1050px;
            margin: 0 auto 55px;
            padding: 20px;
            text-align: center;
        }

        .what-we-do h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 25px;
        }

        .work-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .work-card {
            background: #fff7ed;
            padding: 22px;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .work-card:hover {
            transform: translateY(-5px);
        }

        .work-card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .work-card p {
            color: #666;
            line-height: 1.6;
            font-size: 14px;
        }

        /* Safe Place section */
        .about-ending {
            max-width: 1050px;
            margin: 0 auto 45px;
            padding: 35px 20px;
            text-align: center;
            background: #fff7ed;
            border-radius: 12px;
        }

        .about-ending h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .about-ending > p {
            color: #666;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .about-ending .work-card {
            background: #fff;
            box-shadow: 0 3px 15px rgba(0,0,0,0.07);
            padding: 15px;
        }

        .about-ending .work-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            margin-bottom: 15px;
        }

        /* Final message */
        .hope-section {
            max-width: 1050px;
            margin: 0 auto 45px;
            padding: 40px 20px;
            text-align: center;
            background: #fff7ed;
            border-radius: 12px;
        }

        .hope-section h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 18px;
        }

        .hope-section p {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .hope-section h3 {
            color: #333;
            font-size: 20px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .about-content {
                grid-template-columns: 1fr;
                gap: 22px;
            }

            .about-image img {
                height: 250px;
            }

            .mission-vision {
                grid-template-columns: 1fr;
            }

            .work-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .work-grid {
                grid-template-columns: 1fr;
            }

            .about-hero h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

    <!-- Hero Section -->
    <section class="about-hero">
        <h1>About Stray Paw</h1>
        <p>Creating a safer and kinder world for every stray animal.</p>
    </section>

    <!-- Image LEFT and Who We Are RIGHT -->
    <section class="about-content">

        <div class="about-image">
            <img
                src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=900"
                alt="Two dogs together">
        </div>

        <div class="about-text">
            <h2>Who We Are</h2>

            <p>
                Stray Paw Rescue Management System is a web-based platform
                created to make animal rescue activities more organized
                and accessible.
            </p>

            <p>
                Many stray animals suffer from injuries, illness, hunger,
                and homelessness. Finding help can be difficult when rescue
                information is scattered across social media and phone calls.
            </p>

            <p>
                Stray Paw brings important rescue activities together in one
                place. Users can report animals in need, while administrators
                can manage rescue requests, animal records, treatments,
                shelters, adoptions, and volunteers.
            </p>
        </div>

    </section>

    <!-- Mission and Vision -->
    <section class="mission-vision">

        <div class="info-card">
            <h2>Our Mission</h2>
            <p>
                Our mission is to make reporting, rescuing, and caring for
                stray animals easier through an organized online system.
                We aim to connect people who want to help animals with
                the rescue activities that need attention.
            </p>
        </div>

        <div class="info-card">
            <h2>Our Vision</h2>
            <p>
                Our vision is a community where every stray animal has
                access to care, protection, and the opportunity to find
                a safe and loving home.
            </p>
        </div>

    </section>

    <!-- What We Do -->
    <section class="what-we-do">
        <h2>What We Do</h2>

        <div class="work-grid">

            <div class="work-card">
                <h3>Report & Rescue</h3>
                <p>
                    Users can report stray or injured animals by sharing
                    photos, descriptions, and location details.
                </p>
            </div>

            <div class="work-card">
                <h3>Animal Care</h3>
                <p>
                    Administrators can maintain animal records and
                    treatment histories.
                </p>
            </div>

            <div class="work-card">
                <h3>Adoption</h3>
                <p>
                    People can browse animals available for adoption
                    and submit adoption applications.
                </p>
            </div>

            <div class="work-card">
                <h3>Volunteer</h3>
                <p>
                    People can register as volunteers and become part
                    of animal rescue activities.
                </p>
            </div>

            <div class="work-card">
                <h3>Rescue Management</h3>
                <p>
                    Administrators can organize rescue requests,
                    shelters, animal records, and volunteer information.
                </p>
            </div>

        </div>
    </section>

    <!-- A Safe Place for Every Paw -->
    <section class="about-ending">

        <h2>A Safe Place for Every Paw 🐾</h2>

        <p>
            Every animal deserves kindness, care, and a chance at a better life.
        </p>

        <div class="work-grid">

            <div class="work-card">
                <img
                    src="https://images.unsplash.com/photo-1601758125946-6ec2ef64daf8?w=900"
                    alt="Dogs receiving care">

                <h3>Care & Protection</h3>

                <p>
                    Every rescued animal deserves a safe and caring environment.
                </p>
            </div>

            <div class="work-card">
                <img
                    src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=900"
                    alt="Person caring for a dog">

                <h3>Love & Kindness</h3>

                <p>
                    Compassion and attention help animals feel safe and loved.
                </p>
            </div>

            <div class="work-card">
                <img
                    src="https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=900"
                    alt="Cat">

                <h3>A Loving Home</h3>

                <p>
                    Every rescued animal deserves the chance to find a loving family.
                </p>
            </div>

        </div>

    </section>

    <!-- Be Their Voice Section -->
    <section class="hope-section">

        <h2>Be Their Voice. Be Their Hope. </h2>

        <p>
            Animals cannot always ask for help, but we can choose to help them.
            Whether you report an injured animal, volunteer, or adopt,
            every small action matters.
        </p>

        <h3>Stray Paw – Because Every Life Matters. 🐾</h3>

    </section>

</main>

<?php include "includes/footer.php"; ?>

</body>
</html>
