<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>PayDay</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
            integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+"
            crossorigin="anonymous"></script>
    <style>
        /*overrides*/
        .bg-primary {
            background-color: #019aff !important;
        }

        .text-primary {
            color: #019aff !important;
        }

        .text-info {
            color: #21a6ff !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /*===*/

        *, *::after, *::before {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            /*margin-top: 9.5rem;*/
            margin-top: 8rem;
            overflow-x: hidden;
        }

        nav {
            top: 0;
            padding: 1.75rem 0;
            z-index: 9999;
        }

        nav .logo {
            width: 10rem;
        }

        .custom-py {
            padding: 0.75rem 0;
        }

        .custom-btn {
            padding: 2rem 0;
        }

        a.custom-btn {
            cursor: pointer;
        }

        a.custom-btn:not(.bg-warning):hover {
            background-color: #44bd32 !important;
        }

        a.custom-btn.bg-warning:hover {
            background-color: #ffc927 !important;
        }

        .desktop-navigation {
            transition: 250ms;
        }

        .nav-ul a {
            color: #333;
            transition: 150ms;
            position: relative;
        }

        .nav-ul a::after {
            content: '';
            width: 0;
            height: .125rem;
            border-radius: 1rem;
            background-color: #019aff;
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            transition: 350ms;
            opacity: 50%;
        }

        .nav-ul a:hover::after {
            width: 65%;
        }

        .nav-ul a.custom-btn {
            color: #fff;
        }

        .nav-ul a.custom-btn::after {
            display: none;
        }

        .hero-section {
            padding: 5rem 0;
            position: relative;
        }

        .hero-section .hero-main-text {
            font-weight: 600;
            width: 75%;
            margin: 0 auto;
        }

        .hero-section .hero-sub-text {
            font-weight: 600;
        }

        .hero-section {
            position: relative;
        }

        .hero-section::after {
            content: '';
            height: 3rem;
            position: absolute;
            bottom: -15%;
            left: -10%;
            right: -10%;
            transform: rotate(3deg);
            background: #ffffff;
            z-index: 10;
        }

        .calls-to-action {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .custom-btn {
            border: none;
            color: #f9f9f9;
            padding: 0.5rem 2rem;
            border-radius: 50rem;
        }

        a.custom-btn {
            padding: 0.5rem 1.5rem;
            transition: 250ms;
        }

        a.custom-btn:hover {
            color: #fff;
        }

        .custom-secondary-btn {
            color: #333;
            border: 1px solid #999;
            background: #f9f9f9;
            padding: 0.5rem 1.5rem;
            transition: 250ms;
            border-radius: 50rem;
        }

        .custom-secondary-btn:hover {
            background: #fff !important;
            color: #333 !important;
        }

        .alternate-nav,
        .hamburger-lines .line,
        .hamburger-lines .line,
        .hamburger-lines .line {
            transition: 150ms;
        }

        .alternate-nav {
            background: #f9f9f9;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 90;
        }

        .alternate-nav.hidden {
            margin-left: 100%;
        }

        .alternate-nav-link {
            width: 100%;
            padding: 2rem 0;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .alternate-nav-link:hover {
            color: #f9f9f9;
            background: #333;
        }

        nav .hamburger-lines {
            display: block;
            height: 26px;
            width: 32px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }


        nav .hamburger-lines.close {
            position: relative;
        }

        .hamburger-lines.close .line2 {
            opacity: 0;
        }

        nav .hamburger-lines.close .line1,
        nav .hamburger-lines.close .line3 {
            position: absolute;
            top: 50%;
        }

        nav .hamburger-lines.close .line1 {
            transform: rotate(45deg);

        }

        nav .hamburger-lines.close .line3 {
            transform: rotate(-45deg);
        }

        .feature-list {
            position: relative;
            background: rgb(1, 154, 255);
            background: linear-gradient(135deg, rgba(1, 154, 255, 1) 0%, rgba(33, 166, 255, 1) 100%);
        }

        .feature-list > div {
            min-height: 70vh;
        }

        .feature-list::before {
            content: '';
            position: absolute;
            width: 50rem;
            height: 50rem;
            border: 10rem solid #fff;
            opacity: 5%;
            top: 0;
            pointer-events: none;
        }

        .features-list-ul li {
            padding-left: 1rem;
        }

        .features-list-ul li small b {
            position: relative;
        }

        .features-list-ul li small b::before {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: -1rem;
            width: 0.5rem;
            border-radius: 1rem;
            height: 0.2rem;
            background-color: #fff;
        }

        .feature-img {
            height: 17rem;
            width: 22.5rem;
            display: grid;
            place-items: center;
        }

        .feature-img > iframe {
            width: 100%;
            height: 100%;
        }

        .feature-highlights {
            padding: 5rem 0;
        }

        .feature-highlights > div {
            min-height: 100vh;
        }

        .feature-highlights .feature-desc {
            padding-top: 0.5rem;
        }

        .feature-highlights, #faqs {
            background-color: #f9f9f9;
        }

        .feature {
            gap: 1rem;
        }

        .feature .icon {
            /*width: 3rem;*/
            /*height: 2rem;*/
            align-self: start;
            padding: 0.5rem;
            background: #fafffa;
            display: grid;
            place-items: center;
        }

        .installation-procedure {
            padding: 7rem 0;
            background: rgb(255, 193, 7);
            background: linear-gradient(135deg, rgba(255, 193, 7, 1) 0%, rgba(246, 229, 141, 1) 100%);
            position: relative;
            overflow: hidden;
        }


        .installation-procedure::after {
            content: '';
            position: absolute;
            width: 50rem;
            height: 50rem;
            border-radius: 50%;
            border: 10rem solid #fff;
            opacity: 10%;
            top: 50%;
            left: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
        }

        .install-info-wrapper {
            line-height: 1.5rem;
        }

        .install-info-buy-btn {
            padding: 1.125rem 3rem;
            border-radius: 50px;
            background: #f9f9f9;
            cursor: pointer;
            color: rgb(255, 193, 7);
            transition: 150ms;
        }

        .install-info-buy-btn:hover {
            background: #ffffff;
            color: rgb(255, 193, 7);
        }

        .third-party-lists {
            background: rgb(1, 154, 255);
            background: linear-gradient(135deg, rgba(1, 154, 255, 1) 0%, rgba(33, 166, 255, 1) 100%);
            position: relative;
            padding: 8rem 0;
            /*color: #333;*/
        }

        .third-party-lists::after {
            content: '';
            position: absolute;
            width: 30rem;
            height: 30rem;
            border: 6rem solid #999;
            opacity: 0%;
            bottom: 0;
            right: 0;
            pointer-events: none;
        }

        .tp-list-ul {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .tp-list-ul li {
            z-index: 99;
            background-color: #ffffff;
            position: relative;
            margin-right: 1rem;
            border-radius: 0.5rem;
            width: 10rem;
            height: 5rem;
            margin-bottom: 1.25rem;
        }

        .tp-list-ul li img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }

        .customization-section {
            background-color: #ffffff;
            position: relative;
            overflow: hidden;
            /*border-radius: 1rem;*/
        }

        .customization-section .install-info-wrapper {
            line-height: 2rem;
        }

        nav .hamburger-lines .line {
            display: block;
            height: 4px;
            width: 100%;
            border-radius: 10px;
            background: #0e2431;
        }

        .opacity-85 {
            opacity: 0.85;
        }


        .license-section {
            background-color: #f9f9f9;
            min-height: 90vh;
        }

        .license-section .license-price {
            font-size: 3rem;
            font-weight: 700;
        }

        .license-section .license-container {
            background: #fff;
        }

        .license-section .license-price + small {
            font-size: 1rem;
            font-weight: 800;
        }

        .customer-review {
            min-height: 100vh;
        }

        /*.customer-review-text {*/
        /*    height: 10rem;*/
        /*    overflow-y: scroll;*/
        /*}*/

        .customer-review-text + span.read-more-btn {
            display: none;
        }

        .customer-review-text.hide-review + span.read-more-btn {
            display: inline;
        }

        .customer-review-text.hide-review {
            height: 10rem;
            position: relative;
            overflow-y: hidden;
        }

        .customer-review-text.hide-review::after {
            content: '';
            position: absolute;
            height: 50%;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgb(255, 255, 255);
            background: linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .customer-img-wrapper {
            border: 2px solid #019aff;
            width: 8.5rem;
            height: 8.5rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
        }

        .customer-img-wrapper .customer-image {
            width: 8rem;
            height: 8rem;
            border-radius: inherit;
        }


        .customer-review-card {
            margin: 2rem 0;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            border-radius: 0.65rem;
        }

        .custom-shadow {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }


        #faqs {
            padding: 5rem 0;
        }

        #faqs summary {
            background-color: #ffffff;
            transition: 150ms;
        }

        #faqs summary:hover {
            background: #e0f2fd;
        }

        footer {
            background-color: #ffffff;
            min-height: 30vh;
            position: relative;
        }

        footer .copyright-details {
            background: #f9f9f9;
            height: 5rem;
            display: grid;
            place-items: center;
        }

        @media only screen and (min-width: 576px) {
            .hero-section::after {
                bottom: -16%;
                transform: rotate(2deg);
            }


            .feature-display .feature {
                width: 50% !important;
            }

            .feature-img {
                padding: .5rem;
                border-radius: 0.25rem;
                background: #21a6ff;
            }

            .customer-review-card {
                width: 45%;
                margin-bottom: 4.5rem;
            }

            .feature .feature-desc p {
                width: 90% !important;
            }
        }

        @media only screen and (min-width: 768px) {
            .hero-section::after {
                bottom: -15%;
            }

            .hero-section .hero-main-text {
                font-size: 5rem;
                width: 75%;
                margin: 0 auto;
            }

            .hero-section .hero-sub-text {
                font-size: 4.5rem;
            }

            .hero-section p {
                width: 90%;
            }

            .feature-list-content .feature-img {
                width: 120%;
                height: 18rem;
            }
        }

        @media only screen and (min-width: 992px) {
            .nav-ul {
                gap: 3rem;
                padding: 1rem 0;
            }

            .hero-section::after {
                bottom: -14%;
            }

            body {
                margin-top: 9.5rem;
            }

            .hero-section p {
                width: 80%;
            }

            .license-container {
                width: 45%;
            }

            .review-display {
                gap: 2.5rem;
            }

            .feature-img {
                width: 90%;
                height: 60%;
            }

            .install-info-wrapper {
                line-height: 0.95rem;
            }

            .customer-review-card {
                width: 30%;
                margin-bottom: 0;
            }

            /*footer .footer-content { margin-top: 2rem; }*/
        }

        @media only screen and (min-width: 1200px) {
            .nav-ul {
                gap: 0rem;
                padding: 0;
            }

            .hero-section p {
                width: 65%;
            }

            .license-display {
                margin: 8rem 0;
                gap: 10rem;
            }

            .license-container {
                width: 30% !important;
                transform: scale(1.1);
            }

            .feature-list .feature-list-heading {
                border-bottom: none !important;
            }

            .feature-list .feature-list-heading p {
                width: 75%;
                margin: 0 auto;
            }

            .feature-list .feature-list-content {
                gap: 5rem;
            }

            .feature-list-content .feature-img {
                width: 120%;
                height: 20rem;
            }
        }

        @media only screen and (min-width: 1400px) {
            .hero-section::after {
                bottom: -18%;
                height: 4rem;
                transform: rotate(2deg);
            }

            footer .footer-content {
                margin-top: 2rem !important;
            }
        }

        @media only screen and (min-width: 1500px) {
            .hero-section::after {
                bottom: -20%;
                height: 5rem;
            }

            footer .footer-content {
                margin-top: 2.5rem !important;
            }
        }

    </style>
</head>
<body>
<header>
    <nav class="desktop-navigation w-100 bg-white position-fixed">
        <div class="container nav-content container-sm-xl d-flex align-items-center justify-content-between flex-lg-column flex-xl-row">
            <img class="logo" src="{{ asset('images/logo.png')  }}" alt=""/>

            <ul class="nav-ul d-none d-lg-flex align-items-center justify-content-between list-unstyled mb-0">
                <li class="px-xl-3"><a href="#home" class="special-scroll text-decoration-none">Home</a></li>
                <li class="px-xl-3"><a href="#feature-list" class="special-scroll text-decoration-none">Feature list</a>
                </li>
                <li class="px-xl-3"><a href="#highlights" class="special-scroll text-decoration-none">Highlights</a>
                </li>
                <li class="px-xl-3"><a href="#license-and-pricing" class="special-scroll text-decoration-none">License &
                        Pricing</a></li>
                <li class="px-xl-3"><a href="#customer-review" class="special-scroll text-decoration-none">Customer
                        Review</a></li>
                <li class="px-xl-3"><a href="#faqs" class="special-scroll text-decoration-none">FAQs</a></li>
                <li class="px-xl-3"><a href="https://payday.gainhq.com" target="_blank"
                                       class="text-decoration-none custom-btn bg-primary">View demo</a></li>
            </ul>
            <div id="hamburger-menu" class="hamburger-lines d-lg-none">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </div>
        </div>
    </nav>

    <div class="alternate-nav hidden">
        <ul class="d-flex flex-column justify-content-center align-items-center list-unstyled h-100 w-100">
            <li class="alternate-nav-link text-uppercase"><a href="#home"
                                                             class="special-scroll alternate-nav-link text-decoration-none">Home</a>
            </li>
            <li class="alternate-nav-link text-uppercase"><a href="#feature-list"
                                                             class="special-scroll alternate-nav-link text-decoration-none">Feature
                    list</a></li>
            <li class="alternate-nav-link text-uppercase"><a href="#highlights"
                                                             class="special-scroll alternate-nav-link text-decoration-none">Highlights</a>
            </li>
            <li class="alternate-nav-link text-uppercase"><a href="#license-and-pricing"
                                                             class="special-scroll alternate-nav-link text-decoration-none">License
                    & Pricing</a></li>
            <li class="alternate-nav-link text-uppercase"><a href="#customer-review"
                                                             class="special-scroll alternate-nav-link text-decoration-none">Customer
                    Review</a></li>
            <li class="alternate-nav-link text-uppercase"><a href="#faqs"
                                                             class="special-scroll alternate-nav-link text-decoration-none">FAQs</a>
            </li>
            <li class="alternate-nav-link text-uppercase"><a href="https://mailer.gainhq.com/" target="_blank"
                                                             class="alternate-nav-link text-decoration-none custom-btn bg-primary text-white">View
                    demo</a></li>

        </ul>
    </div>
</header>

<section class="hero-section text-center" id="home">
    <h1 class="hero-main-text w-sm-100">Lorem ipsum dolor sit amet.</h1>
    <h2 class="hero-sub-text text-info">Lorem ipsum dolor.</h2>
    <div class="calls-to-action mt-5">
        <a href="#license-and-pricing" class="special-scroll shadow-sm custom-btn bg-primary text-decoration-none">Pricing</a>
        <a href="https://payday.gainhq.com/documentation/" target="_blank"
           class="shadow-sm custom-secondary-btn text-decoration-none">Documentation</a>
    </div>
</section>

<section class="feature-list py-4 py-md-5 mt-5" id="feature-list">
    <div class="container container-sm-xl text-white px-lg-5">
        <div class="border-bottom text-xl-center feature-list-heading py-xl-5">
            <h1 class="display-5">Feature list</h1>
            <p class="lead opacity-85">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci architecto
                blanditiis
                dolores voluptate. Atque eos fuga id laborum natus ratione. Commodi explicabo inventore labore, nemo
                quaerat
                sunt temporibus unde!</p>
        </div>
        <div class="feature-list-content d-flex mt-3 flex-column flex-md-row mt-md-5 align-items-md-start">
            <div class="feature-img m-0 mt-1 order-1">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/jIeX6R7KU40"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>

            <div class="m-0 text-white w-100 mt-lg-0">
                <h4><strong>We are providing you with modules such as - </strong></h4>
                <ul class="features-list-ul mt-4 list-unstyled">
                    <li class="mb-2">
                        <small> <b>Dashboard</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Job Desk</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Employee</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Import employee</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Leave</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Attendance</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Geolocation & IP</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Import and Export Attendances</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Payroll</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>Administration</b></small>
                    </li>
                    <li class="mb-2">
                        <small> <b>App Settings</b></small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="installation-procedure shadow-sm bg-warning">
    <div class="container container-sm-xl text-white text-center">
        <h1 class="display-5">We provide free installation</h1>
        <div class="install-info-wrapper py-5">
            <p class="lead">Lorem ipsum dolor sit amet, qui minim labore adipisicing minim sint cillum sint consectetur
                cupidatat.</p>
            <p class="lead">Qui minim labore adipisicing minim sint cillum sint consectetur cupidatat.</p>
        </div>
        <a href="https://codecanyon.net/item/payday-hrm-solutions/33681719#" target="_blank"
           class="install-info-buy-btn shadow-sm text-decoration-none">Buy now</a>
    </div>
</section>

<section class="feature-highlights" id="highlights">
    <div class="container container-sm-xl">
        <div class="section-heading px-lg-5">
            <h1 class="text-center pt-5 mb-3">Highlights</h1>
            <p class="lead text-center">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci architecto
                dignissimos dolorum minima minus nam rem sequi sint soluta tempore. Ad aperiam commodi dolor quae.
                Aliquid
                asperiores nemo provident sint?</p>
        </div>

        <div class="feature-display d-flex flex-wrap mt-5 border rounded p-5 bg-white">
            <div class="feature w-100 d-flex mb-3">
                <div class="icon rounded text-info shadow-sm">
                    <i data-feather="pie-chart"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Dashboard</h5>
                    <p class="text-muted w-100">See all statuses of the application from dashboard and stay up to
                        date. </p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="user"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Job desk</h5>
                    <p class="text-muted w-100">Find and manage all of your employees and their history, statuses,
                        salary, leave, attendance in one place.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="download"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Import Employee</h5>
                    <p class="text-muted w-100">Import employees to the app.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="clock"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Leave</h5>
                    <p class="text-muted w-100">Track your employees every leave, assign, approve or reject their leave
                        in this option. And also you can find the whole summery of leave here.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="calendar"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Attendance</h5>
                    <p class="text-muted w-100">Track an employee every day attendance through the app's Punch In and
                        Punch Out option. Manage attendance by assign or approve or reject application and view the
                        whole attendance summery.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="map-pin"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Geolocation & IP</h5>
                    <p class="text-muted w-100">Track your employee's geolocation and ip through attendance.</p>
                </div>
            </div>


            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm text-info shadow-sm rounded">
                    <i data-feather="upload"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Import and Export Attendances</h5>
                    <p class="text-muted w-100">Manually Import Attendances. Also app admin and Manager can export
                        attendances.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="credit-card"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Payroll</h5>
                    <p class="text-muted w-100">Calculation and generation the employees monthly or any customized
                        date's payslips are fully automated.</p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="briefcase"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Administration</h5>
                    <p class="text-muted w-100">
                        Add different users to different role based on permissions.
                        Create Departments and manage your employees according to your need.
                        Create and assign Work Shifts to employee or departments to manage your workflow.
                        Create holidays and be relax on those days.
                        Find the full organization structure here.
                    </p>
                </div>
            </div>

            <div class="feature w-100 d-flex mb-3">
                <div class="icon text-info shadow-sm rounded">
                    <i data-feather="settings"></i>
                </div>
                <div class="feature-desc">
                    <h5 class="text-info">Settings</h5>
                    <p class="text-muted w-100">
                        Customize and set the full application according to your demand.
                    </p>
                </div>
            </div>

        </div>
        <div class="customization-section p-5 mx-auto mt-5 d-flex align-items-center justify-content-between flex-wrap shadow-sm rounded">
            <div class="section-content">
                <h4>Need customization?</h4>
                <div class="install-info-wrapper">
                    <p class="lead">Utilize the expertise of our developers to bring your custom requirements to
                        life.</p>
                </div>
            </div>
            <a href="#license-and-pricing" class="custom-btn bg-primary text-decoration-none d-inline-block bg-warning"
               target="_blank">Let's talk!</a>
        </div>
    </div>
</section>


<section class="third-party-lists shadow text-center">
    <div class="container container-sm-xl text-white px-lg-5">
        <h1 class="display-5">Send email with MailTrap or other email providers</h1>
        <ul class="list-unstyled my-5 tp-list-ul">
            <li class="shadow-sm">
                <img src="{{ asset('images/landing-page/gmail.png')  }}" alt="">
            </li>
            <li class="shadow-sm">
                <img src="{{ asset('images/landing-page/webmail.png')  }}" alt="">
            </li>
            <li class="shadow-sm">
                <img src="{{ asset('images/landing-page/mailgun.png')  }}" alt="">
            </li>
            <li class="shadow-sm">
                <img src="{{ asset('images/landing-page/aws.png')  }}" alt="">
            </li>
            {{--            <li class="lead">list item 9</li>--}}
        </ul>
        <p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci architecto blanditiis
            dolores voluptate. Atque eos fuga id laborum natus ratione. Commodi explicabo inventore labore, nemo quaerat
            sunt temporibus unde!</p>
        <a class="text-decoration-none custom-btn bg-warning shadow-sm text-white d-inline-block">View integrations</a>
    </div>
</section>

<section id="license-and-pricing" class="license-section py-5">
    <div class="container container-sm-xl">
        <div class="section-header text-center mb-5">
            <h1 class="text-center pt-5 mb-3">Choose the license of your preference</h1>
            <p class="opacity-85 lead w-75 mx-auto">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque est porro ut! Assumenda dolorem ducimus
                eligendi eos hic impedit iste modi, quo sint voluptas? Amet doloremque molestias quos reprehenderit
                voluptatum.
            </p>
        </div>

        <div class="license-display d-block d-flex flex-column align-items-center flex-xl-row justify-content-xl-center">
            {{-- regular license --}}
            <div class="license-container rounded shadow-sm mb-4 w-75">
                <div class="license-header border-bottom w-100 px-4 pt-4">
                    <h4><strong>Regular license</strong></h4>
                    <p class="opacity-85 text-muted">
                        Suitable for personal use.
                    </p>
                    <p>
                        <strong class="license-price">$23</strong>
                        <small class="text-primary"> / pay once</small>
                    </p>
                </div>

                <div class="mt-3 px-4 pb-4 d-flex flex-column">
                    <small class="lead mb-3 text-bold"><strong>What we offer</strong></small>
                    <ul class="list-unstyled">
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> 1 Installation
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> One time fee
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> 6 months free
                                support </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Unlimited
                                everything </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Personal use
                                only </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Full source
                                code </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Lifetime
                                license </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Email support
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Host on own
                                server </p></li>
                    </ul>
                    <a class="custom-btn bg-primary d-block w-75 text-center align-self-center text-decoration-none"
                       href="https://codecanyon.net/item/payday-hrm-solutions/33681719"
                       target="_blank">Purchase now</a>
                </div>
            </div>

            <div class="license-container rounded shadow-sm mb-4 w-75">
                <div class="license-header border-bottom w-100 px-4 pt-4">
                    <h4><strong>Extended license</strong></h4>
                    <p class="opacity-85 text-muted">
                        Suitable for personal use.
                    </p>
                    <p>
                        <strong class="license-price">$590</strong>
                        <small class="text-primary"> / pay once</small>
                    </p>
                </div>

                <div class="mt-3 px-4 pb-4 d-flex flex-column">
                    <small class="lead mb-3 text-bold"><strong>What we offer</strong></small>
                    <ul class="list-unstyled">
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> 1 Installation
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> One time fee
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> 6 months free
                                support </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Unlimited
                                everything </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> End user can be
                                billed </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Full source
                                code </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Lifetime
                                license </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Email support
                            </p></li>
                        <li><p class="text-muted"><i data-feather="check" class="pr-2 text-primary"></i> Host on own
                                server </p></li>
                    </ul>
                    <a class="custom-btn bg-primary d-block w-75 text-center align-self-center text-decoration-none"
                       href="https://codecanyon.net/item/payday-hrm-solutions/33681719"
                       target="_blank">Purchase now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="customer-review" class="customer-review border">
    <div class="container container-sm-xl px-lg-5 mb-5">
        <div class="section-header text-center mb-5">
            <h1 class="text-center pt-5 mb-3">Customer review</h1>
            <p class="lead text-center">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci architecto
                dignissimos dolorum minima minus nam rem sequi sint soluta tempore. Ad aperiam commodi dolor quae.
                Aliquid
                asperiores nemo provident sint?</p>
        </div>

        <div class="review-display d-block d-sm-flex justify-content-center align-items-center flex-wrap">
            <div class="customer-review-card custom-shadow overflow-hidden border">
                <div class="customer-img-wrapper">
                    <img
                            class="customer-image"
                            src="data:image/jpeg;base64,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"
                            alt="Customer Image"
                    />
                </div>
                <div class="text-content p-4">
                    <div class="customer-details mb-3 text-center">
                        <p class="customer-name lead"> Lorem ipsum. </p>
                        <small class="opacity-85">Lorem ipsum dolor sit amet, consectetur sit.</small>
                    </div>

                    <p class="customer-review-text hide-review">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem eligendi est exercitationem
                        harum
                        magnam modi natus necessitatibus nostrum optio porro quam quidem quis ratione sapiente, totam
                        unde
                        ut velit voluptatum?
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias aliquam, aperiam at culpa
                        delectus
                        dolore doloremque eos esse excepturi expedita explicabo hic impedit laborum magni maiores nisi
                        obcaecati quibusdam. Maiores!

                        <span class="read-less-btn text-danger d-block mt-3 cursor-pointer"> Read less</span>
                    </p>
                    <span class="read-more-btn text-info cursor-pointer">Read more</span>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="shadow-md" id="faqs">
    <div class="container container-sm-xl px-lg-5 text-center">
        <h1 class="display-5 mb-5">FAQs</h1>
        <div class="row mb-2 text-left">
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
            <div class="q-and-a col-12 col-md-6 mb-3">
                <details>
                    <summary class="p-2 pl-3 rounded mb-2">Lorem ipsum</summary>
                    <p class="pl-3">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem debitis deleniti est, explicabo
                        ipsam labore minima odit perspiciatis provident rerum sit sunt, voluptate. Perspiciatis, rem,
                        voluptate. Dolores placeat sed unde.
                    </p>
                </details>
            </div>
        </div>
    </div>
</section>

<footer class="d-flex flex-column justify-content-between">
    <div class="container container-sm-xl px-lg-5">
        <div class="footer-content d-flex justify-content-between align-items-center flex-wrap flex-lg-nowrap py-3 py-lg-0 mt-xl-4">
            <div class="content">
                <h4>Contact us</h4>
                <p class="lead">
                    <i data-feather="mail"></i>
                    <span>support@gain.solutions</span>
                </p>
                <p class="text-muted">
                    We will reply to you within 24hrs of receiving your email.
                </p>
            </div>
            <a href="https://payday.gainhq.com" target="_blank"
               class="custom-btn bg-primary shadow-sm text-decoration-none d-block">View our demo</a>
        </div>
    </div>
    <div class="copyright-details text-center">
        <p class="mb-0">© <span id="current-year"></span> PayDay - Self-hosted HRM software. All Rights
            Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
    // MOBILE NAVIGATION TOGGLE LOGICC
    const hamburgerMenu = document.querySelector('#hamburger-menu');
    const mobileNav = document.querySelector('.alternate-nav');

    const toggleAlternateNav = () => {
        hamburgerMenu.classList.toggle('close');
        mobileNav.classList.toggle('hidden');
    }

    const handleNavLinkClick = (e) => {
        const {classList} = e.target;
        const classListArrayed = Array.from(classList);
        if (classListArrayed.includes('alternate-nav-link')) setTimeout(toggleAlternateNav, 150);
    }

    mobileNav.addEventListener('click', handleNavLinkClick);
    hamburgerMenu.addEventListener('click', toggleAlternateNav);

    // DESKTOP NAVIGATION SCROLL BEHAVIOUR
    const addSpecialClassesToNavOnScroll = () => {
        const pixelsFromTheTop = +window.scrollY;
        const threshold = 100;
        const specialClasses = ["shadow", "custom-py"]; // classes to add the the navigation to make it scroll with the page
        const navEl = document.querySelector(".desktop-navigation");

        if (+pixelsFromTheTop >= +threshold) {
            for (const specialClass of specialClasses) navEl.classList.add(specialClass);
            return;
        }
        for (const specialClass of specialClasses) navEl.classList.remove(specialClass);
    }

    document.addEventListener('scroll', addSpecialClassesToNavOnScroll);

    document.querySelectorAll('.special-scroll').forEach(link =>
        link.addEventListener('click', () =>
            setTimeout(() =>
                    window.scrollTo({top: window.scrollY - (70)}),
                800
            )));


    const date = new Date();
    const currentYear = date.getFullYear();
    document.getElementById('current-year').textContent = currentYear;

    const readMoreBtn = document.querySelector('.read-more-btn');
    const readLessBtn = document.querySelector('.read-less-btn');
    const customerReviewText = document.querySelector('.customer-review-text');

    const toggleCustomerReviewHideClass = () => customerReviewText.classList.toggle('hide-review');
    readMoreBtn.addEventListener('click', toggleCustomerReviewHideClass);
    readLessBtn.addEventListener('click', toggleCustomerReviewHideClass);

    ////////////////////// bg effect logic

    // animated background
    const deg = (a) => Math.PI / 180 * a
    const rand = (v1, v2) => Math.floor(v1 + Math.random() * (v2 - v1))
    const opt = {
        particles: window.width / 500 ? 1000 : 500,
        noiseScale: 0.009,
        angle: Math.PI / 180 * 220,
        h1: rand(0, 360),
        h2: rand(0, 360),
        s1: rand(20, 90),
        s2: rand(20, 90),
        l1: rand(30, 80),
        l2: rand(30, 80),
        strokeWeight: 1.2,
        tail: 82,
    }
    const Particles = []
    let time = 0
    document.body.addEventListener('click', () => {
        opt.h1 = rand(0, 360)
        opt.h2 = rand(0, 360)
        opt.s1 = rand(20, 90)
        opt.s2 = rand(20, 90)
        opt.l1 = rand(30, 80)
        opt.l2 = rand(30, 80)
        // opt.angle += deg(random(60, 60)) * (Math.random() > .5 ? 1 : -1)

        for (let p of Particles) {
            p.randomize()
        }
    })

    class Particle {
        constructor(x, y) {
            this.x = x
            this.y = y
            this.lx = x
            this.ly = y
            this.vx = 0
            this.vy = 0
            this.ax = 0
            this.ay = 0
            this.hueSemen = Math.random()
            this.hue = this.hueSemen > .5 ? 20 + opt.h1 : 20 + opt.h2
            this.sat = this.hueSemen > .5 ? opt.s1 : opt.s2
            this.light = this.hueSemen > .5 ? opt.l1 : opt.l2
            this.maxSpeed = this.hueSemen > .5 ? .5 : 1
        }

        randomize() {
            this.hueSemen = Math.random()
            this.hue = this.hueSemen > .5 ? 20 + opt.h1 : 20 + opt.h2
            this.sat = this.hueSemen > .5 ? opt.s1 : opt.s2
            this.light = this.hueSemen > .5 ? opt.l1 : opt.l2
            this.maxSpeed = this.hueSemen > .5 ? .5 : 1
        }

        update() {
            this.follow()

            this.vx += this.ax
            this.vy += this.ay

            var p = Math.sqrt(this.vx * this.vx + this.vy * this.vy)
            var a = Math.atan2(this.vy, this.vx)
            var m = Math.min(this.maxSpeed, p)
            this.vx = Math.cos(a) * m
            this.vy = Math.sin(a) * m

            this.x += this.vx
            this.y += this.vy
            this.ax = 0
            this.ay = 0

            this.edges()
        }

        follow() {
            let angle = (noise(this.x * opt.noiseScale, this.y * opt.noiseScale, time * opt.noiseScale)) * Math.PI * 0.5 + opt.angle

            this.ax += Math.cos(angle)
            this.ay += Math.sin(angle)

        }

        updatePrev() {
            this.lx = this.x
            this.ly = this.y
        }

        edges() {
            if (this.x < 0) {
                this.x = width
                this.updatePrev()
            }
            if (this.x > width) {
                this.x = 0
                this.updatePrev()
            }
            if (this.y < 0) {
                this.y = height
                this.updatePrev()
            }
            if (this.y > height) {
                this.y = 0
                this.updatePrev()
            }
        }

        render() {
            stroke(`#359bfc`)
            // stroke(`hsla(${this.hue}, ${this.sat}%, ${this.light}%, .5)`)
            line(this.x, this.y, this.lx, this.ly)
            this.updatePrev()
        }
    }

    function setup() {
        createCanvas(windowWidth, windowHeight)
        for (let i = 0; i < opt.particles; i++) {
            Particles.push(new Particle(Math.random() * width, Math.random() * height))
        }
        strokeWeight(opt.strokeWeight)
    }

    function draw() {
        time++
        // background(255, 100 - opt.tail)
        background('#fff')
        for (let p of Particles) {
            p.update()
            p.render()
        }
    }

    function windowResized() {
        resizeCanvas(windowWidth, windowHeight)
    }
</script>
</body>
</html>
