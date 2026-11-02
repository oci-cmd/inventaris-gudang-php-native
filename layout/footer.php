  </main>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-slate-200 mt-auto py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
      <div class="flex items-center gap-2">
        <span class="font-bold text-slate-800"><?= APP_NAME ?></span>
        <span>&bull;</span>
        <span><?= APP_TITLE ?></span>
      </div>
      <div class="text-slate-400">
        &copy; <?= date('Y') ?> <?= CAMPUS_NAME ?> &bull; Pegangan Dosen Pemrograman Web Dasar
      </div>
    </div>
  </footer>

  <!-- VANILLA JS HELPER (P13/P14) -->
  <script>
    // Live Search Filter untuk Tabel Data
    function setupTableFilter(inputId, tableId) {
      const searchInput = document.getElementById(inputId);
      const table = document.getElementById(tableId);
      if (!searchInput || !table) return;

      searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
          const text = row.innerText.toLowerCase();
          row.style.display = text.includes(filter) ? '' : 'none';
        });
      });
    }

    // Auto dismiss alert info
    document.addEventListener('DOMContentLoaded', () => {
      const alerts = document.querySelectorAll('.auto-dismiss');
      alerts.forEach(el => {
        setTimeout(() => {
          el.style.transition = 'opacity 0.5s ease';
          el.style.opacity = '0';
          setTimeout(() => el.remove(), 500);
        }, 3500);
      });
    });
  </script>

</body>
</html>

