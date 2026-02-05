/**
 * Flavor Trip — Lightbox Gallery
 * 바닐라 JS, 키보드 접근성 지원
 */

(function () {
    'use strict';

    var galleryItems = document.querySelectorAll('.gallery-item');
    if (!galleryItems.length) return;

    var currentIndex = 0;
    var lightbox = null;
    var imgEl = null;
    var captionEl = null;
    var counterEl = null;
    var previousFocus = null;

    function createLightbox() {
        lightbox = document.createElement('div');
        lightbox.className = 'ft-lightbox';
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-modal', 'true');
        lightbox.setAttribute('aria-label', '포토 갤러리');

        lightbox.innerHTML =
            '<button class="ft-lightbox-close" aria-label="닫기">&times;</button>' +
            '<button class="ft-lightbox-prev" aria-label="이전 이미지">&#8249;</button>' +
            '<button class="ft-lightbox-next" aria-label="다음 이미지">&#8250;</button>' +
            '<img class="ft-lightbox-img" src="" alt="">' +
            '<div class="ft-lightbox-caption"></div>' +
            '<div class="ft-lightbox-counter"></div>';

        document.body.appendChild(lightbox);

        imgEl = lightbox.querySelector('.ft-lightbox-img');
        captionEl = lightbox.querySelector('.ft-lightbox-caption');
        counterEl = lightbox.querySelector('.ft-lightbox-counter');

        // 이벤트
        lightbox.querySelector('.ft-lightbox-close').addEventListener('click', close);
        lightbox.querySelector('.ft-lightbox-prev').addEventListener('click', prev);
        lightbox.querySelector('.ft-lightbox-next').addEventListener('click', next);

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) close();
        });
    }

    function open(index) {
        if (!lightbox) createLightbox();

        previousFocus = document.activeElement;
        currentIndex = index;
        updateImage();

        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';

        // 포커스 트랩
        lightbox.querySelector('.ft-lightbox-close').focus();
        document.addEventListener('keydown', onKeydown);
    }

    function close() {
        if (!lightbox) return;
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKeydown);

        if (previousFocus) {
            previousFocus.focus();
        }
    }

    function prev() {
        currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        updateImage();
    }

    function next() {
        currentIndex = (currentIndex + 1) % galleryItems.length;
        updateImage();
    }

    function updateImage() {
        var item = galleryItems[currentIndex];
        var src = item.getAttribute('href');
        var caption = item.getAttribute('data-caption') || '';
        var alt = item.querySelector('img') ? item.querySelector('img').alt : '';

        imgEl.src = src;
        imgEl.alt = alt;
        captionEl.textContent = caption;
        captionEl.style.display = caption ? 'block' : 'none';
        counterEl.textContent = (currentIndex + 1) + ' / ' + galleryItems.length;
    }

    function onKeydown(e) {
        switch (e.key) {
            case 'Escape':
                close();
                break;
            case 'ArrowLeft':
                prev();
                break;
            case 'ArrowRight':
                next();
                break;
            case 'Tab':
                // 포커스 트랩
                var focusable = lightbox.querySelectorAll('button');
                var first = focusable[0];
                var last = focusable[focusable.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
                break;
        }
    }

    // 갤러리 아이템 클릭 이벤트
    galleryItems.forEach(function (item, index) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            open(index);
        });
    });
})();
