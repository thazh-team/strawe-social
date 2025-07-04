// Main JavaScript file for Strawe
document.addEventListener('DOMContentLoaded', function() {
    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    // Add mobile class to body
    if (isMobile || window.innerWidth <= 768) {
        document.body.classList.add('mobile-device');
    }
    
    // Auto resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        // Set initial height
        textarea.style.minHeight = '120px';
        
        const autoResize = () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 300) + 'px';
        };
        
        textarea.addEventListener('input', autoResize);
        textarea.addEventListener('paste', () => setTimeout(autoResize, 10));
        
        // Initial resize
        autoResize();
    });
    
    // Enhanced search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        // Add clear button for mobile
        const clearButton = document.createElement('button');
        clearButton.innerHTML = '×';
        clearButton.className = 'search-clear';
        clearButton.style.cssText = `
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 20px;
            color: #657786;
            cursor: pointer;
            display: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: background-color 0.2s;
        `;
        
        if (searchInput.parentNode.style.position !== 'relative') {
            searchInput.parentNode.style.position = 'relative';
        }
        searchInput.parentNode.appendChild(clearButton);
        
        searchInput.addEventListener('input', function() {
            clearButton.style.display = this.value ? 'block' : 'none';
        });
        
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            this.style.display = 'none';
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    }
    
    // Enhanced form validation with mobile-friendly messages
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e0245e';
                    field.classList.add('field-error');
                    
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.style.borderColor = '#e1e8ed';
                    field.classList.remove('field-error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Show mobile-friendly error
                showMobileNotification('Vui lòng điền đầy đủ thông tin bắt buộc', 'error');
                
                // Focus first invalid field and scroll to it
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
    
    // Auto-hide alerts with better mobile timing
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Add close button to alerts
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            right: 10px;
            top: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        `;
        
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);
        
        const hideAlert = () => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        };
        
        closeBtn.addEventListener('click', hideAlert);
        
        // Auto-hide after longer time on mobile
        const hideTime = isMobile ? 7000 : 5000;
        setTimeout(hideAlert, hideTime);
    });
    
    // Character counter for textareas with better mobile display
    const textareasWithLimit = document.querySelectorAll('textarea[maxlength]');
    textareasWithLimit.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = `
            text-align: right;
            font-size: 12px;
            color: #657786;
            margin-top: 5px;
            padding: 0 5px;
        `;
        
        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} ký tự còn lại`;
            
            if (remaining < 0) {
                counter.style.color = '#e0245e';
                counter.style.fontWeight = 'bold';
            } else if (remaining < 20) {
                counter.style.color = '#ffad1f';
                counter.style.fontWeight = 'bold';
            } else {
                counter.style.color = '#657786';
                counter.style.fontWeight = 'normal';
            }
        };
        
        textarea.addEventListener('input', updateCounter);
        textarea.parentNode.appendChild(counter);
        updateCounter();
    });
    
    // Improved mobile navigation with swipe detection
    let touchStartX = 0;
    let touchStartY = 0;
    const navbar = document.querySelector('.navbar');
    
    if (isTouch && navbar) {
        document.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        
        document.addEventListener('touchend', function(e) {
            if (!e.changedTouches[0]) return;
            
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Detect horizontal swipe
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0) {
                    // Swipe right - could trigger back navigation
                    console.log('Swipe right detected');
                } else {
                    // Swipe left - could trigger forward navigation
                    console.log('Swipe left detected');
                }
            }
        }, { passive: true });
    }
    
    // Lazy loading for images with better mobile performance
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.dataset.src || img.src;
                
                // Create a new image to preload
                const newImg = new Image();
                newImg.onload = () => {
                    img.src = src;
                    img.classList.add('loaded');
                    if (img.dataset.src) {
                        img.removeAttribute('data-src');
                    }
                };
                newImg.src = src;
                
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px',
        threshold: 0.1
    });
    
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        imageObserver.observe(img);
        
        // Add loading placeholder
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        img.addEventListener('error', function() {
            this.style.opacity = '0.5';
            this.title = 'Không thể tải hình ảnh';
        });
    });
    
    // Enhanced infinite scroll with mobile optimizations
    let isLoading = false;
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', throttle(() => {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
        // Hide/show navbar on mobile when scrolling
        if (isMobile && navbar) {
            if (currentScroll > lastScrollTop && currentScroll > 100) {
                // Scrolling down
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                navbar.style.transform = 'translateY(0)';
            }
        }
        
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        
        // Infinite scroll trigger
        if (!isLoading && (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000)) {
            // Placeholder for infinite scroll functionality
            console.log('Load more content trigger');
        }
    }, 100), { passive: true });
    
    // Enhanced post interactions for mobile
    const postActions = document.querySelectorAll('.post-action');
    postActions.forEach(action => {
        // Add touch feedback
        action.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        }, { passive: true });
        
        action.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        }, { passive: true });
        
        // Prevent double-tap zoom on buttons
        action.addEventListener('touchend', function(e) {
            e.preventDefault();
        });
    });
    
    // Real-time character count for posts with mobile optimization
    const postTextarea = document.querySelector('textarea[name="content"]');
    if (postTextarea) {
        const maxChars = 280;
        const charCounter = document.createElement('div');
        charCounter.className = 'post-char-counter';
        charCounter.style.cssText = `
            position: absolute;
            right: 15px;
            bottom: 15px;
            font-size: 12px;
            color: #657786;
            background: white;
            padding: 2px 6px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        `;
        
        postTextarea.parentNode.style.position = 'relative';
        postTextarea.parentNode.appendChild(charCounter);
        
        const updateCharCount = () => {
            const remaining = maxChars - postTextarea.value.length;
            charCounter.textContent = remaining;
            
            if (remaining < 0) {
                charCounter.style.color = '#e0245e';
                charCounter.style.fontWeight = 'bold';
            } else if (remaining < 20) {
                charCounter.style.color = '#ffad1f';
                charCounter.style.fontWeight = 'bold';
            } else {
                charCounter.style.color = '#657786';
                charCounter.style.fontWeight = 'normal';
            }
        };
        
        postTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }
    
    // Mobile-specific file input enhancements
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        const label = document.createElement('label');
        label.className = 'file-input-label';
        label.style.cssText = `
            display: inline-block;
            padding: 8px 16px;
            background: #f7f9fa;
            border: 2px dashed #e1e8ed;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
            margin-top: 5px;
        `;
        
        const isImage = input.accept && input.accept.includes('image');
        label.textContent = isImage ? '📷 Chọn hình ảnh' : '📎 Chọn file';
        
        input.style.display = 'none';
        input.parentNode.insertBefore(label, input.nextSibling);
        
        label.addEventListener('click', () => input.click());
        
        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                label.textContent = `✓ ${fileName.length > 20 ? fileName.substring(0, 20) + '...' : fileName}`;
                label.style.background = '#e8f5e8';
                label.style.borderColor = '#17bf63';
            }
        });
    });
    
    // Performance optimization for mobile
    if (isMobile) {
        // Reduce animation duration for better performance
        const style = document.createElement('style');
        style.textContent = `
            * {
                transition-duration: 0.15s !important;
                animation-duration: 0.15s !important;
            }
        `;
        document.head.appendChild(style);
    }
});

// Utility functions
function showMobileNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `mobile-notification ${type}`;
    notification.textContent = message;
    
    const isMobile = window.innerWidth <= 768;
    notification.style.cssText = `
        position: fixed;
        top: ${isMobile ? '70px' : '80px'};
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        max-width: ${isMobile ? '90%' : '400px'};
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInTop 0.3s ease;
        background: ${type === 'error' ? '#e0245e' : type === 'success' ? '#17bf63' : '#1da1f2'};
        color: white;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutTop 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add enhanced CSS animations and mobile optimizations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInTop {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutTop {
        from {
            transform: translate(-50%, 0);
            opacity: 1;
        }
        to {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
    }
    
    .navbar {
        transition: transform 0.3s ease;
    }
    
    .post:hover {
        background-color: #f7f9fa;
        transition: background-color 0.2s ease;
    }
    
    .btn:active {
        transform: scale(0.98);
    }
    
    .nav-link:active {
        transform: scale(0.95);
    }
    
    .field-error {
        animation: shake 0.5s ease;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    img {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    img.loaded {
        opacity: 1;
    }
    
    /* Mobile-specific enhancements */
    @media (max-width: 768px) {
        .mobile-device .card {
            margin-bottom: 8px;
        }
        
        .mobile-device .post-action {
            min-width: 44px;
            min-height: 44px;
        }
        
        .mobile-device input, 
        .mobile-device textarea, 
        .mobile-device button {
            font-size: 16px !important; /* Prevent zoom on iOS */
        }
        
        .mobile-device .search-clear:hover {
            background-color: rgba(0,0,0,0.1);
        }
    }
    
    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
        .post:hover {
            background-color: transparent;
        }
        
        .btn:hover {
            transform: none;
        }
        
        .nav-link:hover {
            transform: none;
            background-color: transparent;
        }
    }
`;
document.head.appendChild(style);
