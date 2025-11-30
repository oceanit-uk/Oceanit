/* Enhanced script.js with reliable form submission and mobile carousel fix */
(async function() {
  // Initialize carousel
  await initializeCarousel();
  
  // Initialize form handling for both forms
  initializeContactForm('contactForm'); // Desktop form
  initializeContactForm('contactFormMobile'); // Mobile form
  
  // Add smooth scrolling for navigation
  initializeSmoothScrolling();
  
  // Add intersection observer for animations
  initializeAnimations();
})();

async function initializeCarousel() {
  const track = document.querySelector('.carousel-track');
  if (!track) {
    console.error('Carousel track not found!');
    return;
  }

  const base = track.dataset.imgBase || 'images/';
  const ext = track.dataset.imgExt || 'png';
  const maxTry = parseInt(track.dataset.max, 10) || 50;
  const visible = parseInt(track.dataset.visible, 10) || 4;
  const carousel = track.closest('.carousel');

  // Helper: load one image
  function checkImage(url) {
    return new Promise(resolve => {
      const img = new Image();
      img.onload = () => resolve({ ok: true, url });
      img.onerror = () => resolve({ ok: false, url });
      img.src = url;
    });
  }

  // Load images
  const checks = [];
  for (let i = 1; i <= maxTry; i++) {
    checks.push(checkImage(`${base}${i}.${ext}`));
  }
  
  const results = await Promise.all(checks);
  const found = results.filter(r => r.ok).map(r => r.url);

  if (found.length === 0) {
    track.innerHTML = '<div class="text-center py-8"><p class="text-white text-lg opacity-80">Portfolio images coming soon</p></div>';
    const pbtn = document.getElementById('prevBtn');
    const nbtn = document.getElementById('nextBtn');
    if (pbtn) pbtn.style.display = 'none';
    if (nbtn) nbtn.style.display = 'none';
    return;
  }

  // Populate track with images
  track.innerHTML = '';
  found.forEach((url, index) => {
    const img = document.createElement('img');
    img.src = url;
    img.className = 'carousel-img';
    img.alt = `Website design example ${index + 1}`;
    img.loading = 'lazy';
    track.appendChild(img);
  });

  const originalSlides = Array.from(track.children);
  const originalCount = originalSlides.length;

  // Handle small number of images
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  
  if (originalCount <= visible) {
    track.style.justifyContent = 'center';
    if (prevBtn) prevBtn.style.display = 'none';
    if (nextBtn) nextBtn.style.display = 'none';
    
    function sizeSmallSet() {
      const carouselWidth = carousel.clientWidth;
      const gap = 24;
      const slideW = Math.floor((carouselWidth - (visible - 1) * gap) / visible);
      originalSlides.forEach(s => { 
        s.style.width = `${slideW}px`;
        s.style.flex = `0 0 ${slideW}px`;
      });
    }
    sizeSmallSet();
    window.addEventListener('resize', sizeSmallSet);
    return;
  }

  // Clone for infinite loop effect
  for (let i = 0; i < visible; i++) {
    const cloneFirst = originalSlides[i].cloneNode(true);
    const cloneLast = originalSlides[originalCount - 1 - i].cloneNode(true);
    track.appendChild(cloneFirst);
    track.insertBefore(cloneLast, track.firstChild);
  }

  const allSlides = Array.from(track.children);
  let index = visible;
  let slideWidth = 0;
  let isAnimating = false;
  const gap = 24;

  function updateSizes() {
    const carouselWidth = carousel.clientWidth;
    slideWidth = (carouselWidth - (visible - 1) * gap) / visible;
    
    allSlides.forEach(s => {
      s.style.width = `${slideWidth}px`;
      s.style.flex = `0 0 ${slideWidth}px`;
    });
    
    track.style.transition = 'none';
    track.style.transform = `translateX(-${(slideWidth + gap) * index}px)`;
    
    setTimeout(() => {
      track.style.transition = 'transform 0.7s ease-in-out';
    }, 50);
  }

  updateSizes();
  window.addEventListener('resize', updateSizes);

  function goTo(i) {
    if (isAnimating) return;
    isAnimating = true;
    
    track.style.transform = `translateX(-${(slideWidth + gap) * i}px)`;
    index = i;
  }

  function next() {
    if (isAnimating) return;
    goTo(index + 1);
  }

  function prev() {
    if (isAnimating) return;
    goTo(index - 1);
  }

  track.addEventListener('transitionend', () => {
    isAnimating = false;
    
    if (index >= visible + originalCount) {
      track.style.transition = 'none';
      index = visible;
      track.style.transform = `translateX(-${(slideWidth + gap) * index}px)`;
      setTimeout(() => {
        track.style.transition = 'transform 0.7s ease-in-out';
      }, 50);
    }
    
    if (index < visible) {
      track.style.transition = 'none';
      index = visible + originalCount - 1;
      track.style.transform = `translateX(-${(slideWidth + gap) * index}px)`;
      setTimeout(() => {
        track.style.transition = 'transform 0.7s ease-in-out';
      }, 50);
    }
  });

  // Event listeners for arrows - FIXED FOR MOBILE
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      next();
      resetAutoSlide();
    });
    
    // Add touch support for mobile
    nextBtn.addEventListener('touchstart', (e) => {
      e.preventDefault();
      next();
      resetAutoSlide();
    });
  }
  
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      prev();
      resetAutoSlide();
    });
    
    // Add touch support for mobile
    prevBtn.addEventListener('touchstart', (e) => {
      e.preventDefault();
      prev();
      resetAutoSlide();
    });
  }

  // Add touch swipe support for mobile
  let touchStartX = 0;
  let touchEndX = 0;
  
  track.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
  });
  
  track.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  });
  
  function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        // Swipe left - next
        next();
      } else {
        // Swipe right - previous
        prev();
      }
      resetAutoSlide();
    }
  }

  // Auto slide
  let autoSlideInterval;
  
  function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
      if (!isAnimating) {
        next();
      }
    }, 3500);
  }
  
  function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
  }
  
  startAutoSlide();

  // Pause on hover
  carousel.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
  carousel.addEventListener('mouseleave', () => startAutoSlide());
}

function initializeContactForm(formId) {
  const contactForm = document.getElementById(formId);
  if (!contactForm) return;

  contactForm.addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent page refresh
    
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;
    contactForm.classList.add('loading');

    try {
      // Create FormData from the form
      const formData = new FormData(contactForm);
      
      // Add timestamp to prevent caching
      formData.append('_timestamp', Date.now());
      
      // Send to FormSubmit AJAX endpoint
      const response = await fetch(contactForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        // Success - show message and reset form
        showMessage('✅ Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.', 'success', formId);
        contactForm.reset();
        
        // Scroll to show success message
        setTimeout(() => {
          const message = document.querySelector(`#${formId}-message`);
          if (message) {
            message.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 100);
      } else {
        throw new Error(result.message || 'Form submission failed');
      }
      
    } catch (error) {
      console.error('Form submission error:', error);
      showMessage('❌ Sorry, there was an error sending your message. Please try again or contact us directly at oceanit.uk@gmail.com', 'error', formId);
    } finally {
      // Reset button state
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
      contactForm.classList.remove('loading');
    }
  });
}

function showMessage(text, type, formId) {
  // Remove existing messages for this form
  const existingMsg = document.querySelector(`#${formId}-message`);
  if (existingMsg) existingMsg.remove();
  
  // Create new message
  const messageDiv = document.createElement('div');
  messageDiv.id = `${formId}-message`;
  messageDiv.className = `form-message ${type === 'success' ? 'success-message' : 'error-message'}`;
  messageDiv.innerHTML = `
    <div style="display: flex; align-items: center; gap: 10px; justify-content: center;">
      <span style="font-size: 1.2em;">${type === 'success' ? '✅' : '❌'}</span>
      <span>${text}</span>
    </div>
  `;
  
  // Add styles for better visibility
  messageDiv.style.cssText = `
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    font-weight: 500;
    text-align: center;
    animation: slideIn 0.3s ease-out;
    border: 1px solid ${type === 'success' ? '#059669' : '#dc2626'};
  `;
  
  // Add to form container
  const form = document.getElementById(formId);
  if (form) {
    form.parentNode.insertBefore(messageDiv, form);
    
    // Auto remove after 8 seconds for success, 12 seconds for errors
    setTimeout(() => {
      if (messageDiv.parentNode) {
        messageDiv.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
          if (messageDiv.parentNode) {
            messageDiv.remove();
          }
        }, 300);
      }
    }, type === 'success' ? 8000 : 12000);
  }
}

function initializeSmoothScrolling() {
  // Add smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
}

function initializeAnimations() {
  // Simple intersection observer for fade-in animations
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: 0.1 });

  // Observe elements for animation
  document.querySelectorAll('.service-item, .characteristic-item, .package-card, .notice-card, .feature-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
}

// Function to scroll to contact form (works for both desktop and mobile)
function scrollToContactForm() {
  // Try to find desktop form first
  const desktopForm = document.getElementById('contactForm');
  // Try to find mobile form
  const mobileForm = document.getElementById('contactFormMobile');
  
  if (desktopForm) {
    desktopForm.scrollIntoView({ 
      behavior: 'smooth',
      block: 'start'
    });
  } else if (mobileForm) {
    mobileForm.scrollIntoView({ 
      behavior: 'smooth',
      block: 'start'
    });
  }
}

// Performance monitoring
function initializePerformanceTracking() {
  // Track Core Web Vitals
  if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
      list.getEntries().forEach((entry) => {
        console.log(`${entry.name}: ${entry.value}`);
      });
    });
    
    observer.observe({entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift']});
  }
}

// Enhanced image loading
function optimizeImageLoading() {
  const images = document.querySelectorAll('img[data-src]');
  images.forEach(img => {
    img.src = img.getAttribute('data-src');
    img.removeAttribute('data-src');
  });
}

// Update your main initialization function
(async function() {
  // Your existing code...
  
  // Add these lines:
  initializePerformanceTracking();
  optimizeImageLoading();
})();