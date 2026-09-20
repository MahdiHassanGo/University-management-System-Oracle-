(function () {
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (event) {
      if (!confirm(el.getAttribute('data-confirm') || 'Are you sure?')) {
        event.preventDefault();
      }
    });
  });
})();
