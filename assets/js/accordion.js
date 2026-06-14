(function () {
  'use strict';

  function initAccordion(root) {
    if (!root) return;

    // Avoid double-binding
    if (root.dataset.dccFaqInited === '1') return;
    root.dataset.dccFaqInited = '1';

    const items = root.querySelectorAll('.dcc-faq-item');
    if (!items.length) return;

    // Ensure all answers are hidden on init
    items.forEach((item) => {
      const icon = item.querySelector('.dcc-toggle-icon');
      item.classList.remove('active');
      if (icon) icon.textContent = '+';
    });

    root.addEventListener('click', function (e) {
      const header = e.target.closest('.dcc-question-header');
      if (!header || !root.contains(header)) return;

      const item = header.closest('.dcc-faq-item');
      if (!item) return;

      const icon = item.querySelector('.dcc-toggle-icon');
      const isActive = item.classList.contains('active');

      // Close all
      items.forEach((el) => {
        el.classList.remove('active');
        const i = el.querySelector('.dcc-toggle-icon');
        if (i) i.textContent = '+';
      });

      // Toggle current
      if (!isActive) {
        item.classList.add('active');
        if (icon) icon.textContent = '−';
      }
    });
  }

  function initAll() {
    document.querySelectorAll('.dcc-bazar-faq-widget').forEach(initAccordion);
  }

  // Normal frontend
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Elementor frontend hooks (covers editor & dynamic loads)
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction(
      'frontend/element_ready/global',
      function ($scope) {
        // $scope is a jQuery object in Elementor
        const el = $scope && $scope[0] ? $scope[0] : null;
        if (!el) return;
        el.querySelectorAll('.dcc-bazar-faq-widget').forEach(initAccordion);
      }
    );
  }
})();
