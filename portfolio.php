<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceanit - Portfolio</title>
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
                <a href="index.php#home" class="nav-link">HOME</a>
                <a href="index.php#our-story" class="nav-link">OUR STORY</a>
                <a href="index.php#services" class="nav-link">SERVICES</a>
                <a href="index.php#portfolio" class="nav-link">PORTFOLIO</a>
                <a href="index.php#reviews" class="nav-link">REVIEWS</a>
                <a href="index.php#contact-us" class="nav-link">CONTACT US</a>
            </nav>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Portfolio Page Content -->
    <section class="section" style="margin-top: 100px;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Complete Portfolio</h2>
                <div class="title-underline"></div>
                <p class="section-description">A comprehensive showcase of our work and achievements</p>
            </div>
            <div class="portfolio-grid portfolio-page-grid" id="portfolioContainer">
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <p>Loading portfolio items...</p>
                </div>
            </div>
            <div class="view-more-container" style="margin-top: 3rem;">
                <button id="loadMorePortfolio" class="view-more-button" style="display: none;">Load More Projects</button>
                <a href="index.php#portfolio" class="view-more-button" style="margin-left: 1rem;">Back to Home</a>
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

    <script src="assets/script.js"></script>
    <script>
        // Lazy loading for portfolio
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('portfolioContainer');
            const loadMoreBtn = document.getElementById('loadMorePortfolio');
            let currentPage = 1;
            let isLoading = false;
            let hasLoadedFirstPage = false;
            
            function loadPortfolio(page) {
                if (isLoading) return;
                isLoading = true;
                
                if (hasLoadedFirstPage) {
                    loadMoreBtn.textContent = 'Loading...';
                    loadMoreBtn.disabled = true;
                }
                
                fetch(`load_more_portfolio.php?page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear loading message on first load
                            if (!hasLoadedFirstPage) {
                                container.innerHTML = '';
                                hasLoadedFirstPage = true;
                            }
                            
                            if (data.html && data.html.trim() !== '') {
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = data.html;
                                
                                // Append new portfolio items with fade-in animation
                                const newItems = tempDiv.querySelectorAll('.portfolio-item');
                                if (newItems.length > 0) {
                                    newItems.forEach((item, index) => {
                                        item.style.opacity = '0';
                                        item.style.transform = 'translateY(20px)';
                                        container.appendChild(item);
                                        
                                        // Animate in
                                        setTimeout(() => {
                                            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                            item.style.opacity = '1';
                                            item.style.transform = 'translateY(0)';
                                        }, index * 100);
                                    });
                                    
                                    if (data.hasMore) {
                                        loadMoreBtn.style.display = 'inline-block';
                                        loadMoreBtn.textContent = 'Load More Projects';
                                        currentPage++;
                                    } else {
                                        loadMoreBtn.style.display = 'none';
                                    }
                                } else {
                                    // No items returned
                                    if (!hasLoadedFirstPage) {
                                        container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);"><p>No portfolio items available yet.</p></div>';
                                    }
                                    loadMoreBtn.style.display = 'none';
                                }
                            } else {
                                // Empty HTML response
                                if (!hasLoadedFirstPage) {
                                    container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);"><p>No portfolio items available yet.</p></div>';
                                }
                                loadMoreBtn.style.display = 'none';
                            }
                        } else {
                            console.error('Error loading portfolio:', data.error);
                            if (!hasLoadedFirstPage) {
                                container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);"><p>Error loading portfolio. Please try again later.</p></div>';
                            }
                            loadMoreBtn.textContent = 'Error - Click to Retry';
                        }
                        isLoading = false;
                        loadMoreBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (!hasLoadedFirstPage) {
                            container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);"><p>Error loading portfolio. Please check your connection and try again.</p></div>';
                        }
                        loadMoreBtn.textContent = 'Error - Click to Retry';
                        isLoading = false;
                        loadMoreBtn.disabled = false;
                    });
            }
            
            // Load first page
            loadPortfolio(1);
            
            // Load more button click
            loadMoreBtn.addEventListener('click', function() {
                loadPortfolio(currentPage);
            });
        });
    </script>
</body>
</html>

