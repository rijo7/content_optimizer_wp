// js/admin_darkmode.js
(() => {
    const STORAGE_KEY = 'contentOptimizerDark';
    const body        = document.body;
  
    // 1. Apply saved preference
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      body.classList.add('dark-mode');
    }
  
    // 2. Wait for DOM, then insert a single toggle button
    document.addEventListener('DOMContentLoaded', () => {
      if (document.getElementById('modeToggle')) return; // prevent duplicates
  
      const btn = document.createElement('button');
      btn.className = 'mode-toggle';
      btn.id = 'modeToggle';
      btn.type = 'button';
      btn.title = 'Toggle dark mode';
      btn.textContent = body.classList.contains('dark-mode') ? '☀️' : '🌙';
  
      btn.addEventListener('click', () => {
        const isDark = body.classList.toggle('dark-mode');
        btn.textContent = isDark ? '☀️' : '🌙';
        localStorage.setItem(STORAGE_KEY, isDark ? '1' : '0');
      });
  
      document.body.appendChild(btn);
    });
  })();
  