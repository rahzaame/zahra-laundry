document.addEventListener('DOMContentLoaded', function () {

  const page = window.location.pathname.split('/').pop();
  document.querySelectorAll('.sidebar-nav a').forEach(a => {
    if (a.getAttribute('href') && a.getAttribute('href').includes(page)) {
      a.classList.add('active');
    }
  });

  const berat  = document.getElementById('berat');
  const hargaK = document.getElementById('harga_perkg');
  const total  = document.getElementById('total_harga');

  function hitungTotal() {
    if (!berat || !hargaK || !total) return;
    const b = parseFloat(berat.value) || 0;
    const h = parseFloat(hargaK.value) || 0;
    total.value = (b * h).toFixed(0);
  }

  if (berat)  berat.addEventListener('input',  hitungTotal);
  if (hargaK) hargaK.addEventListener('input', hitungTotal);

  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Yakin?')) e.preventDefault();
    });
  });

  document.querySelectorAll('.alert[data-auto]').forEach(el => {
    setTimeout(() => el.style.display = 'none', 4000);
  });

  window.togglePw = function(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    if (!inp) return;
    if (inp.type === 'password') { inp.type = 'text'; if(ico) ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; if(ico) ico.className = 'bi bi-eye'; }
  };

});
