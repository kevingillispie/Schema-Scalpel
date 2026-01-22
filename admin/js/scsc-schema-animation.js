document.addEventListener('DOMContentLoaded', () => {
    // Split into non-letter characters and schema words
    const jsonChars = ['{', '}', '[', ']', ':', ',', '"'];
    const schemaWords = [
        '@context', '@id', '@type', 'article', 'author', 'address', 'BreadCrumbs', 'BlogPosting', 'contactPoint', 'dateModified', 'datePublished', 'description', 'geo', 'headline', 'image', 'item', 'itemListElement', 'keywords', 'latitude', 'longitude', 'LocalBusiness', 'mainEntity', 'name', 'position', 'potentialAction', 'publisher', 'query-input', 'review', 'reviewRating', 'sameAs', 'schema', 'speakable', 'telephone', 'thumbnailUrl', 'url', 'WebPage', 'WebSite', 'xPath'
    ];

    // Get the container
    let container = document.querySelector('#scsc_schema_preview .inside');
    if (!container) {
        container = document.getElementById('wpbody-content');
    }

    if (!container) {
        console.error('Animation container not found.');
        return;
    }

    // const containerRect = container.getBoundingClientRect();
    const containerRect = document.getElementsByTagName('BODY')[0].getBoundingClientRect();
    console.log(containerRect)
    // Determine number of particles based on screen width
    const isMobile = window.innerWidth <= 768; // Common mobile breakpoint
    const numParticles = isMobile ? 100 : 300; // 100 for mobile, 300 for desktop
    const maxOpacity = 0.1;

    // Weighted random selection (75% chance for jsonChars)
    const getRandomElement = () => {
        const rand = Math.random();
        if (rand < 0.75) {
            return jsonChars[Math.floor(Math.random() * jsonChars.length)];
        }
        return schemaWords[Math.floor(Math.random() * schemaWords.length)];
    };

    // Create particles
    for (let i = 0; i < numParticles; i++) {
        const span = document.createElement('span');
        span.textContent = getRandomElement();
        span.style.position = 'absolute';
        span.style.zIndex = '0';
        span.style.color = '#ffffff';
        span.style.fontSize = `${Math.random() * 12 + 12}px`; // 12-24px
        span.style.fontFamily = 'monospace';
        span.style.pointerEvents = 'none';
        span.style.whiteSpace = 'nowrap';
        span.style.opacity = '0'; // Start hidden

        // Append to get dimensions
        container.appendChild(span);
        const spanWidth = span.offsetWidth;
        const padding = 8;                     // small safety margin (px)
        const maxAllowedX = containerRect.width - spanWidth - padding;
        const x = Math.random() * Math.max(0, maxAllowedX);  // never negative
        const maxAllowedY = containerRect.height * 0.88; // or 0.85–0.90
        const y = Math.random() * maxAllowedY;
        span.style.left = `${x}px`;
        span.style.top = `${y}px`;

        // Anime.js animation
        anime({
            targets: span,
            opacity: {
                value: maxOpacity * (1 - y / containerRect.height), // Scale 0.1 to 0
                duration: 1000,
                easing: 'linear'
            },
            rotate: {
                value: anime.random(-20, 20),
                duration: anime.random(5000, 10000),
                easing: 'easeInOutSine',
                direction: 'alternate',
                loop: true
            },
            translateX: {
                value: () => {
                    // Constrain translation to stay within bounds
                    const maxRight = containerRect.width - spanWidth - x;
                    const maxLeft = -x;
                    return anime.random(Math.max(maxLeft, -10), Math.min(maxRight, 10));
                },
                duration: anime.random(3000, 6000),
                easing: 'easeInOutQuad',
                direction: 'alternate',
                loop: true
            },
            translateY: {
                value: anime.random(-10, 10),
                duration: anime.random(3000, 6000),
                easing: 'easeInOutQuad',
                direction: 'alternate',
                loop: true
            },
            update: (anim) => {
                const currentY = parseFloat(span.style.top) + anim.animations[3].currentValue; // translateY
                span.style.opacity = Math.max(0, maxOpacity * (1 - currentY / containerRect.height));
            }
        });
    }
});