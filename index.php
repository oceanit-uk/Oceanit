<?php
require_once 'db_connect.php';

// Fetch limited portfolio items (maximum 6 items for index page, rest viewable on portfolio page)
$portfolio_stmt = $pdo->query("SELECT * FROM portfolio ORDER BY id DESC LIMIT 6");
$portfolios_index = $portfolio_stmt->fetchAll();

// Fetch limited reviews (4 reviews for index page)
$reviews_stmt = $pdo->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 3");
$reviews_index = $reviews_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceanit</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" type="image/png" href="assets/images/logo.png">
    <link rel="apple-touch-icon" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Fixed Header -->
    <header class="main-header" id="mainHeader">
        <div class="header-container">
            <div class="logo-container">
                <img src="assets/images/logo.png" alt="Oceanit Logo" class="logo">
                <span class="logo-text">Oceanit</span>
            </div>
            <nav class="main-nav">
                <a href="#home" class="nav-link">HOME</a>
                <a href="#our-story" class="nav-link">OUR STORY</a>
                <a href="#services" class="nav-link">SERVICES</a>
                <a href="#portfolio" class="nav-link">PORTFOLIO</a>
                <a href="#reviews" class="nav-link">REVIEWS</a>
                <a href="#contact-us" class="nav-link">CONTACT US</a>
            </nav>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="video-background">
            <div class="video-fallback"></div>
            <iframe id="vimeoVideo" src="https://player.vimeo.com/video/1148744938?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479&amp;autoplay=1&amp;loop=1&amp;muted=1&amp;background=1&amp;controls=0&amp;title=0&amp;byline=0&amp;portrait=0&amp;dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" title="oceanit_landing_video" data-vimeo-id="1148744938"></iframe>
            <div class="video-overlay"></div>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">Oceanit Web Solutions</h1>
            <p class="hero-subtitle">Creative, affordable web solutions that work as hard as your business does.</p>
            <a href="#portfolio" class="cta-button">Explore Our Portfolio</a>
        </div>
        <div class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section id="our-story" class="section story-section-split">
        <div class="story-split-container">
            <div class="story-left">
                <div class="story-header">
                    <h2 class="section-title">Our Story</h2>
                    <div class="title-underline"></div>
                </div>
                <div class="story-text">
                    <p>We are a husband-and-wife team of Computer Science graduates dedicated to bringing high-end technology to ambitious businesses. We founded Oceanit to bridge the gap between technical engineering and creative storytelling, offering premium, "next-level" digital experiences without the corporate overhead. As a family business, we are personally invested in your growth, ensuring your success drives everything we do.</p>
                    <p>Our approach combines graduate-level logic with immersive 3D aesthetics and cinematic motion to build websites that are as fast as they are visually stunning. We specialize in making the future of the web affordable, transforming your vision into a cutting-edge reality that elevates your brand. Let's collaborate to create something amazing designed with heart and powered by science.</p>
                </div>
            </div>
            <div class="story-image">
                <img src="assets/images/our_story.jpg" alt="Our Story">
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Services</h2>
                <div class="title-underline"></div>
                <p class="section-description">Comprehensive Digital Solutions, Engineered for the Modern Brand.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <h3 class="service-title">Next-Gen Website Development</h3>
                    <p class="service-description">Beyond standard templates. We architect high-performance, immersive websites using 3D aesthetics and cinematic motion. It’s graduate-level engineering meets modern art fast, secure, and visually stunning.</p>
                </div>
                <div class="service-card">
                    <h3 class="service-title">Software Solutions</h3>
                    <p class="service-description">We build the engine behind your business. From automation tools to complex backend systems, we engineer bespoke software that simplifies your workflow and solves your unique challenges with precision.</p>
                </div>
                <div class="service-card">
                    <h3 class="service-title">Mobile App Development</h3>
                    <p class="service-description">our brand in their pocket. We design and develop sleek, intuitive mobile applications for iOS and Android that focus on seamless user experience and high speed performance.</p>
                </div>
                <div class="service-card">
                    <h3 class="service-title">Strategic Social Media Management</h3>
                    <p class="service-description">Digital storytelling with an edge. We don't just post, we strategize. We combine data-driven logic with high-end visuals to build your community and turn engagement into real world growth.</p>
                </div>
                <div class="service-card">
                    <h3 class="service-title">High-End Graphic Design</h3>
                    <p class="service-description">Visual identities that demand attention. From modern UI/UX to premium marketing materials, we create a cohesive aesthetic that makes your business look like a global leader from day one.</p>
                </div>
                <div class="service-card">
                    <h3 class="service-title">Maintenance & Expert Support</h3>
                    <p class="service-description">Technology moves fast, we keep you ahead. Our dedicated support ensures your digital assets are always updated, secure, and performing at their peak, so you can focus on running your business.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="section portfolio-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Portfolio</h2>
                <div class="title-underline"></div>
                <p class="section-description">Showcasing our finest work and client success stories</p>
            </div>
            <div class="portfolio-grid portfolio-page-grid">
                <?php
                if (empty($portfolios_index)) {
                    echo '<p style="text-align: center; color: var(--text-secondary); grid-column: 1 / -1;">No portfolio items available yet.</p>';
                } else {
                    foreach ($portfolios_index as $portfolio) {
                        $linkAttr = !empty($portfolio['link']) ? 'onclick="window.open(\'' . htmlspecialchars($portfolio['link'], ENT_QUOTES) . '\', \'_blank\')" style="cursor: pointer;"' : '';
                        echo '<div class="portfolio-item" ' . $linkAttr . '>';
                        echo '<div class="portfolio-image">';
                        if (!empty($portfolio['image_path'])) {
                            echo '<img src="' . htmlspecialchars($portfolio['image_path']) . '" alt="' . htmlspecialchars($portfolio['project_name']) . '">';
                        } else {
                            echo '<img src="assets/images/logo.png" alt="' . htmlspecialchars($portfolio['project_name']) . '">';
                        }
                        echo '<div class="portfolio-overlay">';
                        echo '<h4>' . htmlspecialchars($portfolio['project_name']) . '</h4>';
                        echo '<p>' . htmlspecialchars(substr($portfolio['description'], 0, 50)) . '...</p>';
                        if (!empty($portfolio['link'])) {
                            echo '<a href="' . htmlspecialchars($portfolio['link']) . '" target="_blank" class="portfolio-link" style="margin-top: 1rem; color: white; text-decoration: underline;">View Project</a>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="view-more-container">
                <a href="portfolio.php" class="view-more-button">View More</a>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="section reviews-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Client Reviews</h2>
                <div class="title-underline"></div>
                <p class="section-description">What our clients say about working with us</p>
            </div>
            <div class="reviews-grid reviews-page-grid">
                <?php
                if (empty($reviews_index)) {
                    echo '<p style="text-align: center; color: var(--text-secondary); grid-column: 1 / -1;">No reviews available yet.</p>';
                } else {
                    foreach ($reviews_index as $review) {
                        echo '<div class="review-card">';
                        echo '<div class="review-rating">★★★★★</div>';
                        echo '<p class="review-text">"' . htmlspecialchars($review['comment']) . '"</p>';
                        echo '<div class="review-author">';
                        echo '<div class="author-name">' . htmlspecialchars($review['client_name']) . '</div>';
                        if (!empty($review['business_name'])) {
                            echo '<div class="author-title">' . htmlspecialchars($review['business_name']) . '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="view-more-container">
                <a href="reviews.php" class="view-more-button">View More</a>
            </div>
        </div>
    </section>

        <!-- Beyond the Code Section -->
        <section id="beyond-code" class="section story-section-split -sectiostoryn-reversed">
        <div class="story-split-container">
            <div class="story-image">
                <img src="assets/images/uk.jpg" alt="Beyond the Code">
            </div>
            <div class="story-left">
                <div class="story-header">
                    <h2 class="section-title">More than just websites</h2>
                    <div class="title-underline"></div>
                </div>
                <div class="story-text">
                    <p>In 2026, the success of your business depends on more than just "being online" it requires a digital experience that commands attention and converts visitors into loyal customers. As a fresh, graduate-led startup, Oceanit is moving away from the outdated agency models of the past. We don't rely on a portfolio of old work. instead, we are driven by the ambition to engineer the most innovative, high-performance websites of today. We believe that your digital presence should be a masterpiece of logic and art, designed to boost your success and, in turn, establish our reputation as the new standard in creative web engineering.</p>
                    <p>Our philosophy is built on a transparent partnership where we grow only when you do. To jumpstart this vision, we are launching an exclusive initiative for 2026 where we offer comprehensive web development solutions at zero upfront cost for selected businesses that possess a truly creative vision. We handle the entire design and build process for free, allowing you to scale your brand without the heavy financial burden of traditional development. In return, we only require a very low monthly payment to cover our ongoing costs for ultra-fast hosting, domain security, and the expert maintenance required to keep your site at the cutting edge. This stress-free, modern approach ensures that our interests are perfectly aligned. we give you our best work to ensure your success, and your success becomes our greatest achievement.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Contact Us Section -->
    <section id="contact-us" class="section contact-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Contact Us</h2>
                <div class="title-underline"></div>
                <p class="section-description">Let's discuss how we can help bring your vision to life</p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Instagram</h4>
                            <p>@oceanit</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.35.23 2.35.23v2.59h-1.324c-1.304 0-1.711.81-1.711 1.64v1.973h2.922l-.467 3.47h-2.455v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Facebook</h4>
                            <p>@Oceanituk</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Email</h4>
                            <p>oceanit.uk@gmail.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Phone</h4>
                            <p>+44 (770) 658-6203</p>
                        </div>
                    </div>
                </div>
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" rows="6" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="submit-button">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> Oceanit Web Solutions. All rights reserved.</p>
        </div>
    </div>
</footer>

    <script src="https://player.vimeo.com/api/player.js"></script>
    <script src="assets/script.js"></script>
</body>
</html>
