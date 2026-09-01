```javascript
/* =====================================================
   STRAY PAW - MAIN JAVASCRIPT
===================================================== */

document.addEventListener("DOMContentLoaded", function () {


    /* =================================================
       MOBILE SIDEBAR
    ================================================= */

    const menuButton =
        document.querySelector(".mobile-menu");

    const sidebar =
        document.querySelector(".dashboard-sidebar");


    if (menuButton && sidebar) {

        /* Create overlay */

        let overlay =
            document.querySelector(".sidebar-overlay");


        if (!overlay) {

            overlay =
                document.createElement("div");

            overlay.className =
                "sidebar-overlay";

            document.body.appendChild(overlay);

        }


        /* Open sidebar */

        menuButton.addEventListener("click", function () {

            sidebar.classList.add("sidebar-open");

            overlay.classList.add("active");

        });


        /* Close sidebar */

        overlay.addEventListener("click", function () {

            sidebar.classList.remove("sidebar-open");

            overlay.classList.remove("active");

        });


        /* Close after clicking navigation */

        const sidebarLinks =
            sidebar.querySelectorAll("a");

        sidebarLinks.forEach(function (link) {

            link.addEventListener("click", function () {

                sidebar.classList.remove(
                    "sidebar-open"
                );

                overlay.classList.remove(
                    "active"
                );

            });

        });

    }



    /* =================================================
       LOGIN FORM
    ================================================= */

    const loginForm =
        document.querySelector(
            'form[action="login.php"]'
        );


    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            function (event) {

                const email =
                    loginForm.querySelector(
                        'input[name="email"]'
                    );

                const password =
                    loginForm.querySelector(
                        'input[name="password"]'
                    );


                let valid = true;


                /* Email validation */

                if (
                    email &&
                    !email.value.trim()
                ) {

                    email.classList.add(
                        "input-error"
                    );

                    valid = false;

                }

                else if (email) {

                    email.classList.remove(
                        "input-error"
                    );

                }


                /* Password validation */

                if (
                    password &&
                    !password.value
                ) {

                    password.classList.add(
                        "input-error"
                    );

                    valid = false;

                }

                else if (password) {

                    password.classList.remove(
                        "input-error"
                    );

                }


                /* Stop form if invalid */

                if (!valid) {

                    event.preventDefault();

                }

            }
        );

    }



    /* =================================================
       REMOVE INPUT ERROR WHEN USER TYPES
    ================================================= */

    const inputs =
        document.querySelectorAll(
            ".form-group input"
        );


    inputs.forEach(function (input) {

        input.addEventListener(
            "input",
            function () {

                input.classList.remove(
                    "input-error"
                );

            }
        );

    });



    /* =================================================
       PASSWORD SHOW / HIDE
    ================================================= */

    const passwordInput =
        document.querySelector(
            'input[name="password"]'
        );


    if (passwordInput) {

        const passwordGroup =
            passwordInput.parentElement;


        /* Create wrapper */

        passwordGroup.style.position =
            "relative";


        /* Create toggle */

        const toggle =
            document.createElement("button");


        toggle.type = "button";

        toggle.innerHTML =
            '<i class="fa-regular fa-eye"></i>';

        toggle.style.position =
            "absolute";

        toggle.style.right =
            "12px";

        toggle.style.bottom =
            "10px";

        toggle.style.border =
            "none";

        toggle.style.background =
            "transparent";

        toggle.style.color =
            "#998d89";

        toggle.style.cursor =
            "pointer";

        toggle.style.fontSize =
            "14px";


        passwordGroup.appendChild(toggle);


        toggle.addEventListener(
            "click",
            function () {


                if (
                    passwordInput.type ===
                    "password"
                ) {

                    passwordInput.type =
                        "text";

                    toggle.innerHTML =
                        '<i class="fa-regular fa-eye-slash"></i>';

                }

                else {

                    passwordInput.type =
                        "password";

                    toggle.innerHTML =
                        '<i class="fa-regular fa-eye"></i>';

                }

            }
        );

    }



    /* =================================================
       ACTIVE NAVIGATION
    ================================================= */

    const currentPage =
        window.location.pathname
            .split("/")
            .pop();


    const navigationLinks =
        document.querySelectorAll(
            ".sidebar-nav a"
        );


    navigationLinks.forEach(function (link) {

        const linkPage =
            link.getAttribute("href");


        if (
            linkPage === currentPage
        ) {

            navigationLinks.forEach(
                function (item) {

                    item.classList.remove(
                        "active"
                    );

                }
            );


            link.classList.add("active");

        }

    });


});
```
