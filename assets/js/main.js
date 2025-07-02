// Main JavaScript file for Strawe
document.addEventListener('DOMContentLoaded', function() {
    // Auto resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
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
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e0245e';
                } else {
                    field.style.borderColor = '#e1e8ed';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
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
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Character counter for textareas
    const textareasWithLimit = document.querySelectorAll('textarea[maxlength]');
    textareasWithLimit.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.style.textAlign = 'right';
        counter.style.fontSize = '14px';
        counter.style.color = '#657786';
        counter.style.marginTop = '5px';
        
        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} ký tự còn lại`;
            if (remaining < 20) {
                counter.style.color = '#e0245e';
            } else {
                counter.style.color = '#657786';
            }
        };
        
        textarea.addEventListener('input', updateCounter);
        textarea.parentNode.appendChild(counter);
        updateCounter();
    });
    
    // Mobile menu toggle
    const createMobileMenu = () => {
        const navbar = document.querySelector('.navbar');
        const navMenu = document.querySelector('.nav-menu');
        
        if (window.innerWidth <= 768 && navbar && navMenu) {
            let mobileToggle = document.querySelector('.mobile-toggle');
            
            if (!mobileToggle) {
                mobileToggle = document.createElement('button');
                mobileToggle.className = 'mobile-toggle';
                mobileToggle.innerHTML = '☰';
                mobileToggle.style.cssText = `
                    display: block;
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: #1da1f2;
                `;
                
                navbar.querySelector('.nav-container').appendChild(mobileToggle);
                
                mobileToggle.addEventListener('click', () => {
                    navMenu.style.display = navMenu.style.display === 'none' ? 'flex' : 'none';
                });
            }
        }
    };
    
    // Initialize mobile menu
    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);
    
    // Lazy loading for images
    const images = document.querySelectorAll('img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => {
        if (img.dataset.src) {
            imageObserver.observe(img);
        }
    });
    
    // Infinite scroll (basic implementation)
    let isLoading = false;
    window.addEventListener('scroll', () => {
        if (isLoading) return;
        
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            // Load more content here
            // This is a placeholder for infinite scroll functionality
        }
    });
    
    // Real-time character count for posts
    const postTextarea = document.querySelector('textarea[name="content"]');
    if (postTextarea) {
        const maxChars = 280; // Twitter-like limit
        const charCounter = document.createElement('div');
        charCounter.style.cssText = `
            position: absolute;
            right: 10px;
            bottom: 10px;
            font-size: 12px;
            color: #657786;
        `;
        
        postTextarea.parentNode.style.position = 'relative';
        postTextarea.parentNode.appendChild(charCounter);
        
        const updateCharCount = () => {
            const remaining = maxChars - postTextarea.value.length;
            charCounter.textContent = remaining;
            
            if (remaining < 0) {
                charCounter.style.color = '#e0245e';
            } else if (remaining < 20) {
                charCounter.style.color = '#ffad1f';
            } else {
                charCounter.style.color = '#657786';
            }
        };
        
        postTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }
});

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
        max-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
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

// Add some CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .post:hover {
        background-color: #f7f9fa;
        transition: background-color 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }
    
    .nav-link:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }
`;
document.head.appendChild(style);
