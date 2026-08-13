(function () {
    'use strict';

    function initBlogGallery() {
        var galleryRoot = document.querySelector('[data-blog-gallery]');

        if (!galleryRoot || typeof window.lightGallery !== 'function') {
            return;
        }

        window.lightGallery(galleryRoot, {
            selector: '[data-blog-gallery-item]',
            download: false,
            counter: true,
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogGallery, { once: true });
    } else {
        initBlogGallery();
    }
})();
