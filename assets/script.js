document.addEventListener('DOMContentLoaded', function () {

  var sidebar  = document.getElementById('sidebar');
  var mainContent = document.getElementById('main-content');
  var overlay  = document.getElementById('sidebar-overlay');
  var btnToggle = document.getElementById('btn-toggle');

  function isMobile() { return window.innerWidth <= 768; }

  if (btnToggle) {
    btnToggle.addEventListener('click', function () {
      if (isMobile()) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
      } else {
        var isCollapsed = sidebar.classList.toggle('collapsed');
        if (isCollapsed) {
          mainContent.style.marginLeft = '0';
          mainContent.style.width = '100%';
        } else {
          mainContent.style.marginLeft = 'var(--sidebar-w)';
          mainContent.style.width = 'calc(100% - var(--sidebar-w))';
        }
      }
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  document.querySelectorAll('.sidebar-nav a').forEach(function(a) {
    a.addEventListener('click', function() {
      if (isMobile()) {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
      }
    });
  });

  var page = window.location.pathname.split('/').pop();
  document.querySelectorAll('.sidebar-nav a').forEach(function(a) {
    if (a.getAttribute('href') && a.getAttribute('href').includes(page)) {
      a.classList.add('active');
    }
  });

  var berat  = document.getElementById('berat');
  var hargaK = document.getElementById('harga_perkg');
  var total  = document.getElementById('total_harga');

  function hitungTotal() {
    if (!berat || !hargaK || !total) return;
    var b = parseFloat(berat.value) || 0;
    var h = parseFloat(hargaK.value) || 0;
    total.value = Math.round(b * h);
  }

  if (berat)  berat.addEventListener('input', hitungTotal);
  if (hargaK) hargaK.addEventListener('input', hitungTotal);

  document.querySelectorAll('[data-confirm]').forEach(function(el) {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm || 'Yakin?')) e.preventDefault();
    });
  });

  document.querySelectorAll('.alert[data-auto]').forEach(function(el) {
    setTimeout(function() { el.style.display = 'none'; }, 4000);
  });

  window.togglePw = function(inputId, spanId) {
    var inp  = document.getElementById(inputId);
    var span = document.getElementById(spanId);
    if (!inp) return;
    if (inp.type === 'password') {
      inp.type = 'text';
      if (span) span.textContent = 'Sembunyikan';
    } else {
      inp.type = 'password';
      if (span) span.textContent = 'Lihat';
    }
  };

});