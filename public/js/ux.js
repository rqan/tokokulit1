document.addEventListener('DOMContentLoaded', () => {
    // 1. Page Transitions
    requestAnimationFrame(() => {
        document.body.classList.add('page-loaded');
    });

    document.querySelectorAll('a').forEach(link => {
        if (link.hostname === window.location.hostname && 
            !link.hash && 
            link.target !== '_blank' && 
            link.getAttribute('href') !== '#' && 
            !link.hasAttribute('data-no-transition') &&
            !link.href.includes('javascript:')) {
            
            link.addEventListener('click', (e) => {
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
                e.preventDefault();
                const href = link.href;
                document.body.classList.remove('page-loaded');
                document.body.classList.add('page-exit');
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        }
    });

    // 2. Scroll Reveal Animations
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.05
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Find all elements currently intersecting to stagger them
                const activeEntries = entries.filter(e => e.isIntersecting && !e.target.classList.contains('is-visible'));
                const index = activeEntries.indexOf(entry);
                
                setTimeout(() => {
                    entry.target.classList.add('is-visible');
                }, Math.max(0, index) * 75); // 75ms stagger
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Apply reveal class
    const elementsToReveal = document.querySelectorAll('h1, h2, h3, .card, section p, .grid > div, table tbody tr, .bg-white.rounded-xl, .bg-darkBorder.rounded-xl, .group.flex.flex-col.cursor-pointer');
    
    elementsToReveal.forEach((el) => {
        if (!el.closest('.modal') && !el.closest('.hidden')) {
            el.classList.add('reveal-item');
            revealObserver.observe(el);
        }
    });

    // 3. Apply micro-interaction classes dynamically to specific elements
    document.querySelectorAll('.group.flex.flex-col.cursor-pointer').forEach(card => {
        card.classList.add('hover-scale-card');
    });
    
    document.querySelectorAll('button:not(.fixed), .btn').forEach(btn => {
        btn.classList.add('hover-scale-btn');
    });

    // 4. Smooth UI Component Transitions Helper (Modals/Dropdowns)
    window.toggleElement = function(elementId, backdropId = null) {
        const el = document.getElementById(elementId);
        if (!el) return;
        
        const backdrop = backdropId ? document.getElementById(backdropId) : null;
        
        if (el.classList.contains('hidden')) {
            // Show
            if(backdrop) {
                backdrop.classList.remove('hidden');
                backdrop.classList.add('backdrop-enter');
                requestAnimationFrame(() => {
                    backdrop.classList.add('backdrop-enter-active');
                    backdrop.classList.remove('backdrop-enter');
                });
            }
            
            el.classList.remove('hidden');
            el.classList.add('transition-enter');
            
            requestAnimationFrame(() => {
                el.classList.add('transition-enter-active');
                el.classList.remove('transition-enter');
            });
        } else {
            // Hide
            if(backdrop) {
                backdrop.classList.add('backdrop-exit-active');
                backdrop.classList.remove('backdrop-enter-active');
            }
            
            el.classList.add('transition-exit-active');
            el.classList.remove('transition-enter-active', 'transition-enter');
            
            setTimeout(() => {
                el.classList.add('hidden');
                el.classList.remove('transition-exit-active');
                
                if(backdrop) {
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('backdrop-exit-active');
                }
            }, 200);
        }
    };
});
