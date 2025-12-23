// ========================================
// SMOOTH SCROLLING & TRANSITIONS
// Oceanit Web Developers
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // VIDEO LOAD DETECTION & PLAYBACK SPEED CONTROL (50% speed)
    // ========================================
    const vimeoVideo = document.getElementById('vimeoVideo');
    const videoBackground = document.querySelector('.video-background');
    
    if (vimeoVideo && videoBackground) {
        let player = null;
        let playbackRateSet = false;
        
        // Function to set playback speed to 50%
        function setSlowPlayback() {
            if (player && !playbackRateSet) {
                player.getPlaybackRate().then(rate => {
                    if (rate !== 0.5) {
                        player.setPlaybackRate(0.5).then(() => {
                            playbackRateSet = true;
                            console.log('Video playback speed set to 50%');
                        }).catch(error => {
                            console.log('Error setting playback rate:', error);
                            // Retry after a short delay
                            setTimeout(setSlowPlayback, 500);
                        });
                    } else {
                        playbackRateSet = true;
                    }
                }).catch(error => {
                    console.log('Error getting playback rate:', error);
                });
            }
        }
        
        // Function to initialize Vimeo Player and set playback speed
        function initVimeoPlayer() {
            if (typeof Vimeo !== 'undefined' && Vimeo.Player) {
                try {
                    if (!player) {
                        player = new Vimeo.Player(vimeoVideo);
                    }
                    
                    // Set playback speed to 50% (0.5x) when video is ready
                    player.ready().then(() => {
                        setSlowPlayback();
                    }).catch(error => {
                        console.log('Error waiting for video ready:', error);
                        // Try to set playback rate anyway after a delay
                        setTimeout(setSlowPlayback, 1000);
                    });
                    
                    // Handle video play events
                    player.on('play', () => {
                        videoBackground.classList.add('video-loaded');
                        videoBackground.classList.add('video-playing');
                        setSlowPlayback(); // Ensure playback rate is set when playing
                    });
                    
                    // Also set playback rate when video starts playing (backup)
                    player.on('playing', () => {
                        setSlowPlayback();
                    });
                    
                    // Set playback rate on timeupdate as well (additional backup)
                    player.on('timeupdate', () => {
                        if (!playbackRateSet) {
                            setSlowPlayback();
                        }
                    });
                    
                } catch (error) {
                    console.log('Error initializing Vimeo Player:', error);
                    // Retry initialization
                    setTimeout(initVimeoPlayer, 1000);
                }
            } else {
                // Retry if Vimeo API is not loaded yet
                setTimeout(initVimeoPlayer, 200);
            }
        }
        
        // Wait for window load to ensure Vimeo API script is loaded
        if (document.readyState === 'complete') {
            // Page already loaded
            setTimeout(initVimeoPlayer, 500);
        } else {
            window.addEventListener('load', function() {
                setTimeout(initVimeoPlayer, 500);
            });
        }
        
        // Also try immediately if API is already available
        if (typeof Vimeo !== 'undefined' && Vimeo.Player) {
            setTimeout(initVimeoPlayer, 100);
        }
        
        // Mark video as loaded (fallback method)
        vimeoVideo.addEventListener('load', function() {
            videoBackground.classList.add('video-loaded');
            initVimeoPlayer(); // Try to initialize player when iframe loads
            
            // Try to detect if video is playing (mobile detection)
            setTimeout(() => {
                videoBackground.classList.add('video-playing');
            }, 2000); // Give video 2 seconds to start playing
        });
        
        // Also check if iframe is already loaded
        if (vimeoVideo.contentWindow) {
            videoBackground.classList.add('video-loaded');
            setTimeout(() => {
                videoBackground.classList.add('video-playing');
            }, 2000);
        }
        
        // Fallback: if video doesn't play after 3 seconds on mobile, keep fallback visible
        if (window.innerWidth <= 768) {
            setTimeout(() => {
                if (!videoBackground.classList.contains('video-playing')) {
                    // Video likely failed to play, keep fallback visible
                    videoBackground.classList.remove('video-loaded');
                }
            }, 3000);
        }
    }
    
    // ========================================
    // HEADER SCROLL EFFECT (Transparent to Solid)
    // ========================================
    const header = document.getElementById('mainHeader');
    let lastScroll = 0;

    function handleHeaderScroll() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    }

    window.addEventListener('scroll', handleHeaderScroll, { passive: true });
    handleHeaderScroll(); // Initial check

    // ========================================
    // SMOOTH SCROLLING FOR NAVIGATION LINKS
    // ========================================
    const navLinks = document.querySelectorAll('.nav-link, .cta-button');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only handle anchor links
            if (href.startsWith('#')) {
                e.preventDefault();
                
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
        
                if (targetElement) {
                    const headerHeight = header.offsetHeight;
                    const targetPosition = targetElement.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
    
                    // Close mobile menu if open
                    const nav = document.querySelector('.main-nav');
                    nav.classList.remove('active');
  }
            }
        });
    });

    // ========================================
    // MOBILE MENU TOGGLE
    // ========================================
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
      });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
      }
    });
  }
  
    // ========================================
    // SCROLL ANIMATIONS (Fade in on scroll)
    // ========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all sections and cards
    const elementsToAnimate = document.querySelectorAll(
        '.section, .service-card, .portfolio-item, .review-card, .stat-item'
    );
    
    elementsToAnimate.forEach(el => {
        observer.observe(el);
    });

    // ========================================
    // FORM SUBMISSION HANDLING
    // ========================================
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const submitButton = this.querySelector('.submit-button');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;
    
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                // Success message
                submitButton.textContent = 'Message Sent!';
                submitButton.style.backgroundColor = '#10b981';
                
                // Reset form
                this.reset();
    
                // Reset button after 3 seconds
                setTimeout(() => {
                    submitButton.textContent = originalText;
                    submitButton.style.backgroundColor = '';
                    submitButton.disabled = false;
                }, 3000);
            }, 1500);
        });
    }
    
    // ========================================
    // PORTFOLIO ITEM HOVER EFFECTS
    // ========================================
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    portfolioItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // ========================================
    // PARALLAX EFFECT FOR HERO SECTION
    // ========================================
    const heroSection = document.querySelector('.hero-section');
    
    if (heroSection) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroContent = document.querySelector('.hero-content');
            
            if (scrolled < window.innerHeight && heroContent) {
                const parallaxSpeed = 0.5;
                heroContent.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
                heroContent.style.opacity = 1 - (scrolled / window.innerHeight);
            }
        }, { passive: true });
    }

    // ========================================
    // STAT COUNTER ANIMATION
    // ========================================
    const statNumbers = document.querySelectorAll('.stat-number');
    const statObserverOptions = {
        threshold: 0.5
    };

    const statObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalNumber = parseInt(target.textContent.replace(/\D/g, ''));
                const suffix = target.textContent.replace(/\d/g, '');
                let currentNumber = 0;
                const increment = finalNumber / 50;
                const duration = 2000;
                const stepTime = duration / 50;

                const counter = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        target.textContent = finalNumber + suffix;
                        clearInterval(counter);
                    } else {
                        target.textContent = Math.floor(currentNumber) + suffix;
                    }
                }, stepTime);
      
                statObserver.unobserve(target);
    }
        });
    }, statObserverOptions);

    statNumbers.forEach(stat => {
        statObserver.observe(stat);
    });

    // ========================================
    // SCROLL TO TOP FUNCTIONALITY (Optional)
    // ========================================
    let scrollTopButton = document.createElement('button');
    scrollTopButton.innerHTML = '↑';
    scrollTopButton.className = 'scroll-to-top';
    scrollTopButton.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: #64ffda;
        color: #0a192f;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
        box-shadow: 0 4px 16px rgba(100, 255, 218, 0.3);
    `;
    
    document.body.appendChild(scrollTopButton);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopButton.style.opacity = '1';
            scrollTopButton.style.visibility = 'visible';
        } else {
            scrollTopButton.style.opacity = '0';
            scrollTopButton.style.visibility = 'hidden';
        }
    }, { passive: true });

    scrollTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // ========================================
    // ACTIVE NAV LINK HIGHLIGHTING
    // ========================================
    const sections = document.querySelectorAll('.section, .hero-section');
    
    function highlightActiveNav() {
        const scrollPosition = window.pageYOffset + 150;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }

    window.addEventListener('scroll', highlightActiveNav, { passive: true });
    
    // Add CSS for active nav link
    const style = document.createElement('style');
    style.textContent = `
        .nav-link.active {
            color: #64ffda !important;
        }
        .nav-link.active::after {
            width: 100% !important;
        }
        .scroll-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(100, 255, 218, 0.4);
        }
    `;
    document.head.appendChild(style);

});
