<?php
require_once 'db_connect.php';

// Fetch limited portfolio items (maximum 6 items for index page, rest viewable on portfolio page)
$portfolio_stmt = $pdo->query("SELECT * FROM portfolio ORDER BY id DESC LIMIT 6");
$portfolios_index = $portfolio_stmt->fetchAll();

// Fetch limited reviews (4 reviews for index page)
$reviews_stmt = $pdo->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 4");
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
            <h1 class="hero-title">Oceanit Web Developers</h1>
            <p class="hero-subtitle">Crafting Digital Excellence, One Pixel at a Time</p>
            <a href="#services" class="cta-button">Explore Our Services</a>
        </div>
        <div class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section id="our-story" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Story</h2>
                <div class="title-underline"></div>
            </div>
            <div class="story-content">
                <div class="story-text">
                    <p class="lead-text">Welcome to Oceanit Web Developers, where innovation meets craftsmanship.</p>
                    <p>We are a passionate team of web developers dedicated to creating exceptional digital experiences. With years of expertise in cutting-edge technologies, we transform ideas into stunning, high-performance websites that drive results.</p>
                    <p>Our commitment to excellence, attention to detail, and client-focused approach have made us a trusted partner for businesses looking to establish a strong online presence.</p>
                </div>
                <div class="story-stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Projects Delivered</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">200+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Services</h2>
                <div class="title-underline"></div>
                <p class="section-description">Comprehensive web development solutions tailored to your business needs</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🎨</div>
                    <h3 class="service-title">Web Design</h3>
                    <p class="service-description">Stunning, user-centric designs that captivate and convert your audience.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">💻</div>
                    <h3 class="service-title">Web Development</h3>
                    <p class="service-description">Robust, scalable websites built with modern technologies and best practices.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">📱</div>
                    <h3 class="service-title">Responsive Design</h3>
                    <p class="service-description">Perfect experiences across all devices, from mobile to desktop.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">⚡</div>
                    <h3 class="service-title">Performance Optimization</h3>
                    <p class="service-description">Lightning-fast load times and optimized performance for better SEO.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🔒</div>
                    <h3 class="service-title">Security</h3>
                    <p class="service-description">Enterprise-grade security to protect your data and your users.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🔄</div>
                    <h3 class="service-title">Maintenance & Support</h3>
                    <p class="service-description">Ongoing support and updates to keep your website running smoothly.</p>
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
            <div class="portfolio-grid">
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
            <div class="reviews-grid">
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
                        <div class="info-icon">📧</div>
                        <div class="info-text">
                            <h4>Email</h4>
                            <p>info@oceanit.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-text">
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-text">
                            <h4>Address</h4>
                            <p>123 Web Development St, Digital City, DC 12345</p>
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
                        <input type="text" id="subject" name="subject" placeholder="Subject" required>
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
                <div class="footer-logo">
                    <img src="assets/images/logo.png" alt="Oceanit Logo" class="logo">
                    <span class="logo-text">Oceanit</span>
                </div>
                <p class="footer-text">&copy; <?php echo date('Y'); ?> Oceanit Web Developers. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://player.vimeo.com/api/player.js"></script>
    <script src="assets/script.js"></script>
</body>
</html>
