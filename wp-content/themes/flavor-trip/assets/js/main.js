/**
 * TripTalk — Main JS
 * 모바일 메뉴, 스티키 헤더, 부드러운 스크롤
 */

(function () {
    'use strict';

    // 모바일 메뉴 토글
    var menuToggle = document.querySelector('.menu-toggle');
    var navigation = document.getElementById('primary-nav');

    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', function () {
            var isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
            navigation.classList.toggle('is-open');
            document.body.classList.toggle('menu-open');
        });

        // ESC로 메뉴 닫기
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && navigation.classList.contains('is-open')) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navigation.classList.remove('is-open');
                document.body.classList.remove('menu-open');
                menuToggle.focus();
            }
        });
    }

    // 스티키 헤더 스크롤 감지
    var header = document.getElementById('site-header');
    if (header) {
        var lastScrollY = 0;
        var ticking = false;

        function onScroll() {
            lastScrollY = window.scrollY;
            if (!ticking) {
                requestAnimationFrame(function () {
                    if (lastScrollY > 10) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // 부드러운 스크롤 (앵커 링크)
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href^="#"]');
        if (!link) return;

        var targetId = link.getAttribute('href');
        if (targetId === '#') return;

        var target = document.querySelector(targetId);
        if (target) {
            e.preventDefault();
            var headerHeight = header ? header.offsetHeight : 0;
            var targetPos = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
            window.scrollTo({ top: targetPos, behavior: 'smooth' });
        }
    });

    // 네이티브 레이지 로딩 폴백
    if ('loading' in HTMLImageElement.prototype) {
        // 브라우저 기본 지원
    } else {
        // IntersectionObserver 폴백
        var lazyImages = document.querySelectorAll('img[loading="lazy"]');
        if (lazyImages.length && 'IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });

            lazyImages.forEach(function (img) {
                observer.observe(img);
            });
        }
    }
})();
