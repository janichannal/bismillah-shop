document.addEventListener('DOMContentLoaded', function () {
    // ---------- Build one shared lightbox for the whole page ----------
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox-overlay';
    lightbox.innerHTML = `
        <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
        <button type="button" class="lightbox-prev" aria-label="Previous">&#8249;</button>
        <div class="lightbox-img-wrap">
            <img class="lightbox-img" src="" alt="">
        </div>
        <button type="button" class="lightbox-next" aria-label="Next">&#8250;</button>
        <div class="lightbox-counter"></div>
        <div class="lightbox-hint">Click image to zoom</div>
    `;
    document.body.appendChild(lightbox);

    const lightboxImg = lightbox.querySelector('.lightbox-img');
    const lightboxCounter = lightbox.querySelector('.lightbox-counter');
    const lightboxClose = lightbox.querySelector('.lightbox-close');
    const lightboxPrev = lightbox.querySelector('.lightbox-prev');
    const lightboxNext = lightbox.querySelector('.lightbox-next');

    let lightboxImages = [];
    let lightboxIndex = 0;

    function openLightbox(images, startIndex) {
        lightboxImages = images;
        lightboxIndex = startIndex;
        showLightboxImage();
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        lightboxImg.classList.remove('zoomed');
    }

    function showLightboxImage() {
        lightboxImg.classList.remove('zoomed');
        lightboxImg.src = lightboxImages[lightboxIndex].src;
        lightboxImg.alt = lightboxImages[lightboxIndex].alt;
        lightboxCounter.textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
        const showNav = lightboxImages.length > 1;
        lightboxPrev.style.display = showNav ? 'flex' : 'none';
        lightboxNext.style.display = showNav ? 'flex' : 'none';
    }

    function lightboxGoTo(i) {
        lightboxIndex = (i + lightboxImages.length) % lightboxImages.length;
        showLightboxImage();
    }

    lightboxClose.addEventListener('click', closeLightbox);
    lightboxPrev.addEventListener('click', function (e) { e.stopPropagation(); lightboxGoTo(lightboxIndex - 1); });
    lightboxNext.addEventListener('click', function (e) { e.stopPropagation(); lightboxGoTo(lightboxIndex + 1); });

    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });

    lightboxImg.addEventListener('click', function (e) {
        if (!lightboxImg.classList.contains('zoomed')) {
            const rect = lightboxImg.getBoundingClientRect();
            const originX = ((e.clientX - rect.left) / rect.width) * 100;
            const originY = ((e.clientY - rect.top) / rect.height) * 100;
            lightboxImg.style.transformOrigin = originX + '% ' + originY + '%';
            lightboxImg.classList.add('zoomed');
        } else {
            lightboxImg.classList.remove('zoomed');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lightboxGoTo(lightboxIndex - 1);
        if (e.key === 'ArrowRight') lightboxGoTo(lightboxIndex + 1);
    });

    // ---------- Wire up every slider on the page ----------
    const sliders = document.querySelectorAll('.img-slider');

    sliders.forEach(function (slider) {
        const track = slider.querySelector('.img-slider-track');
        const images = track.querySelectorAll('img');
        const dotsContainer = slider.querySelector('.img-slider-dots');
        const prevBtn = slider.querySelector('.img-slider-prev');
        const nextBtn = slider.querySelector('.img-slider-next');
        let index = 0;
        let autoTimer = null;

        const hint = document.createElement('div');
        hint.className = 'zoom-hint';
        hint.innerHTML = '&#128269;';
        slider.appendChild(hint);

        images.forEach(function (img) {
            img.addEventListener('click', function () {
                const imgList = Array.from(images).map(function (i) {
                    return { src: i.src, alt: i.alt };
                });
                openLightbox(imgList, index);
            });
        });

        if (images.length <= 1) {
            slider.classList.add('single-image');
            return;
        }

        images.forEach(function (_, i) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'img-slider-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', function () { goTo(i); });
            dotsContainer.appendChild(dot);
        });

        function goTo(i) {
            index = (i + images.length) % images.length;
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            dotsContainer.querySelectorAll('.img-slider-dot').forEach(function (d, di) {
                d.classList.toggle('active', di === index);
            });
        }

        prevBtn.addEventListener('click', function () { goTo(index - 1); });
        nextBtn.addEventListener('click', function () { goTo(index + 1); });

        const autoplayDelay = slider.getAttribute('data-autoplay');
        if (autoplayDelay) {
            const delay = parseInt(autoplayDelay, 10) || 4000;
            function startAuto() { autoTimer = setInterval(function () { goTo(index + 1); }, delay); }
            function stopAuto() { clearInterval(autoTimer); }
            startAuto();
            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);
        }
    });
});