// Depth Engine - Core Depth System
class DepthEngine {
  constructor() {
    this.init();
  }
  
  init() {
    this.setupDepthSystem();
    this.initScrollDepth();
    this.initTouchDepth();
    this.initMouseDepth();
    this.createDepthParticles();
    this.setupGyroDepth(); // For mobile tilt
  }
  
  setupDepthSystem() {
    // Add depth attributes to all elements
    document.querySelectorAll('section').forEach((section, index) => {
      section.setAttribute('data-depth', (index * 0.5).toFixed(1));
    });
    
    // Add depth to cards
    document.querySelectorAll('.service-item, .characteristic-item').forEach((card, index) => {
      card.setAttribute('data-depth', (index * 0.2).toFixed(1));
    });
  }
  
  initScrollDepth() {
    let ticking = false;
    let scrollY = window.scrollY;
    
    const updateDepth = () => {
      const scrolled = scrollY / (document.body.scrollHeight - window.innerHeight);
      
      // Update background depth
      const depthBg = document.querySelector('.depth-bg');
      if (depthBg) {
        const depth = -500 + (scrolled * 100);
        depthBg.style.transform = `translateZ(${depth}px) scale(${1 + scrolled * 0.2})`;
      }
      
      // Update layers with parallax
      document.querySelectorAll('.depth-layer').forEach((layer, index) => {
        const speed = parseFloat(layer.getAttribute('data-speed')) || 0.1;
        const yOffset = scrollY * speed;
        layer.style.transform = `translateZ(-${100 + index * 100}px) translateY(${yOffset}px)`;
      });
      
      // Update section depth
      document.querySelectorAll('section').forEach((section) => {
        const depth = parseFloat(section.getAttribute('data-depth')) || 0;
        const yPos = section.getBoundingClientRect().top;
        const viewportHeight = window.innerHeight;
        
        if (yPos < viewportHeight && yPos > -section.offsetHeight) {
          const progress = 1 - (yPos / viewportHeight);
          const zPos = depth * 100 * progress;
          section.style.transform = `translateZ(${zPos}px)`;
          section.style.opacity = 0.3 + progress * 0.7;
        }
      });
      
      ticking = false;
    };
    
    const onScroll = () => {
      scrollY = window.scrollY;
      
      if (!ticking) {
        window.requestAnimationFrame(updateDepth);
        ticking = true;
      }
    };
    
    // Listen to scroll with passive for better performance
    window.addEventListener('scroll', onScroll, { passive: true });
    
    // Initial depth calculation
    updateDepth();
  }
  
  initTouchDepth() {
    if ('ontouchstart' in window) {
      let lastTouchY = 0;
      let velocity = 0;
      
      document.addEventListener('touchstart', (e) => {
        lastTouchY = e.touches[0].clientY;
        velocity = 0;
      });
      
      document.addEventListener('touchmove', (e) => {
        const currentY = e.touches[0].clientY;
        const deltaY = currentY - lastTouchY;
        lastTouchY = currentY;
        
        // Calculate velocity for depth effect
        velocity = deltaY * 0.5;
        
        // Apply depth effect to elements
        this.applyTouchDepth(deltaY, e.touches[0].clientX, e.touches[0].clientY);
      });
      
      document.addEventListener('touchend', () => {
        // Momentum effect
        const applyMomentum = () => {
          if (Math.abs(velocity) > 0.1) {
            this.applyTouchDepth(velocity, window.innerWidth / 2, window.innerHeight / 2);
            velocity *= 0.95; // Damping
            requestAnimationFrame(applyMomentum);
          }
        };
        applyMomentum();
      });
    }
  }
  
  applyTouchDepth(delta, x, y) {
    const intensity = Math.min(Math.abs(delta) * 0.1, 10);
    
    document.querySelectorAll('.service-item, .characteristic-item').forEach((card) => {
      const rect = card.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      
      const distance = Math.sqrt(Math.pow(x - centerX, 2) + Math.pow(y - centerY, 2));
      const influence = Math.max(0, 1 - distance / 500);
      
      if (influence > 0.1) {
        const rotationX = (y - centerY) * 0.01 * influence * intensity;
        const rotationY = (centerX - x) * 0.01 * influence * intensity;
        
        card.style.transform = `
          translateZ(${20 * influence}px)
          rotateX(${rotationX}deg)
          rotateY(${rotationY}deg)
          translateY(${delta * 0.1 * influence}px)
        `;
        
        // Add glow effect
        card.style.boxShadow = `
          0 ${10 + influence * 20}px ${30 + influence * 30}px rgba(0, 0, 0, ${0.1 + influence * 0.2}),
          0 0 ${50 * influence}px rgba(29, 100, 129, ${0.2 * influence})
        `;
      }
    });
  }
  
  initMouseDepth() {
    let mouseX = 0;
    let mouseY = 0;
    
    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      
      this.updateMouseDepth(mouseX, mouseY);
    });
    
    // Update depth on mouse move with throttling
    let lastUpdate = 0;
    const updateMouseDepth = () => {
      const now = Date.now();
      if (now - lastUpdate > 16) { // ~60fps
        this.updateMouseDepth(mouseX, mouseY);
        lastUpdate = now;
      }
      requestAnimationFrame(updateMouseDepth);
    };
    updateMouseDepth();
  }
  
  updateMouseDepth(x, y) {
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    // Calculate normalized position (-1 to 1)
    const normalizedX = (x / viewportWidth) * 2 - 1;
    const normalizedY = (y / viewportHeight) * 2 - 1;
    
    // Update depth light position
    const depthLight = document.querySelector('.depth-light');
    if (depthLight) {
      depthLight.style.transform = `
        translateZ(100px)
        translateX(${normalizedX * 50}px)
        translateY(${normalizedY * 50}px)
      `;
    }
    
    // Update background depth based on mouse position
    const depthBg = document.querySelector('.depth-bg');
    if (depthBg) {
      depthBg.style.transform = `
        translateZ(-500px)
        translateX(${normalizedX * 20}px)
        translateY(${normalizedY * 20}px)
      `;
    }
    
    // Apply subtle parallax to cards
    document.querySelectorAll('.service-item, .characteristic-item').forEach((card) => {
      const rect = card.getBoundingClientRect();
      const cardCenterX = rect.left + rect.width / 2;
      const cardCenterY = rect.top + rect.height / 2;
      
      const distanceX = (x - cardCenterX) / viewportWidth;
      const distanceY = (y - cardCenterY) / viewportHeight;
      
      const parallaxX = distanceX * 10;
      const parallaxY = distanceY * 10;
      const depthZ = 10 - (Math.abs(distanceX) + Math.abs(distanceY)) * 5;
      
      card.style.transform = `
        translateX(${parallaxX}px)
        translateY(${parallaxY}px)
        translateZ(${depthZ}px)
      `;
    });
  }
  
  createDepthParticles() {
    const canvas = document.getElementById('depth-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const particles = [];
    const particleCount = Math.min(100, Math.floor(window.innerWidth / 20));
    
    // Create particles at different depths
    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        size: Math.random() * 3 + 1,
        speed: Math.random() * 0.5 + 0.2,
        depth: Math.random() * 300 + 100, // Z position
        color: `rgba(255, 255, 255, ${Math.random() * 0.1 + 0.05})`
      });
    }
    
    function animateParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      particles.forEach(particle => {
        // Move particle based on depth (slower when farther)
        particle.y -= particle.speed * (300 / particle.depth);
        
        // Wrap around
        if (particle.y < -10) {
          particle.y = canvas.height + 10;
          particle.x = Math.random() * canvas.width;
        }
        
        // Draw with perspective
        const scale = 300 / particle.depth;
        const x = particle.x;
        const y = particle.y;
        
        ctx.beginPath();
        ctx.arc(x, y, particle.size * scale, 0, Math.PI * 2);
        ctx.fillStyle = particle.color;
        ctx.fill();
        
        // Add glow
        ctx.beginPath();
        ctx.arc(x, y, particle.size * scale * 2, 0, Math.PI * 2);
        ctx.fillStyle = particle.color.replace('0.15', '0.05');
        ctx.fill();
      });
      
      requestAnimationFrame(animateParticles);
    }
    
    // Handle resize
    window.addEventListener('resize', () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    });
    
    animateParticles();
  }
  
  setupGyroDepth() {
    if (window.DeviceOrientationEvent) {
      let lastBeta = 0;
      let lastGamma = 0;
      
      window.addEventListener('deviceorientation', (e) => {
        const beta = e.beta || 0; // Front-to-back tilt
        const gamma = e.gamma || 0; // Left-to-right tilt
        
        // Filter small movements
        if (Math.abs(beta - lastBeta) > 1 || Math.abs(gamma - lastGamma) > 1) {
          this.applyGyroDepth(beta, gamma);
          lastBeta = beta;
          lastGamma = gamma;
        }
      }, true);
    }
  }
  
  applyGyroDepth(beta, gamma) {
    // Normalize angles
    const tiltX = gamma / 45; // -1 to 1
    const tiltY = (beta - 90) / 45; // -1 to 1
    
    // Apply to depth layers
    document.querySelectorAll('.depth-layer').forEach((layer, index) => {
      const speed = parseFloat(layer.getAttribute('data-speed')) || 0.1;
      const offsetX = tiltX * 100 * speed * (index + 1);
      const offsetY = tiltY * 100 * speed * (index + 1);
      
      layer.style.transform = `
        translateZ(-${100 + index * 100}px)
        translateX(${offsetX}px)
        translateY(${offsetY}px)
      `;
    });
    
    // Apply to cards
    document.querySelectorAll('.service-item, .characteristic-item').forEach(card => {
      const rotationX = tiltY * 5;
      const rotationY = tiltX * 5;
      const translateZ = 10 + Math.abs(tiltX + tiltY) * 10;
      
      card.style.transform = `
        rotateX(${rotationX}deg)
        rotateY(${rotationY}deg)
        translateZ(${translateZ}px)
      `;
    });
  }
}

// Initialize Depth Engine when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  const depthEngine = new DepthEngine();
  
  // Add depth class to body for CSS hooks
  document.body.classList.add('depth-enabled');
  
  // Add depth interaction to all interactive elements
  const depthElements = document.querySelectorAll(
    '.service-item, .characteristic-item, .news-card, .logo-wrapper, .form-input, .form-submit-btn'
  );
  
  depthElements.forEach(el => {
    el.addEventListener('mouseenter', () => {
      el.classList.add('depth-active');
    });
    
    el.addEventListener('mouseleave', () => {
      el.classList.remove('depth-active');
    });
    
    // Touch interaction for mobile
    el.addEventListener('touchstart', () => {
      el.classList.add('depth-active');
      setTimeout(() => el.classList.remove('depth-active'), 300);
    });
  });
  
  // Add scroll depth indicators
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const currentScroll = window.scrollY;
    const scrollDirection = currentScroll > lastScroll ? 'down' : 'up';
    lastScroll = currentScroll;
    
    // Add scroll depth class to body
    const scrollPercent = (currentScroll / (document.body.scrollHeight - window.innerHeight)) * 100;
    document.body.style.setProperty('--scroll-depth', `${scrollPercent}%`);
    
    // Update depth intensity based on scroll speed
    const scrollSpeed = Math.abs(currentScroll - lastScroll);
    document.body.style.setProperty('--scroll-intensity', Math.min(scrollSpeed * 0.1, 10));
  });
});