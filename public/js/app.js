/* app.js — Global UI: Toast, Modal, Sidebar, Delete confirmation */

/* ============================================================
   TOAST SYSTEM
   ============================================================ */
const Toast = {
  container: null,

  init() {
    this.container = document.getElementById('toast-container');
    // Show any server-side flash messages as toasts
    const flashSuccess = document.getElementById('flash-success');
    const flashError   = document.getElementById('flash-error');
    if (flashSuccess) this.show('success', flashSuccess.dataset.message);
    if (flashError)   this.show('error',   flashError.dataset.message);
  },

  show(type, message, title) {
    if (!this.container) return;

    const icons = {
      success: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
      error:   `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
      info:    `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
    };

    const defaults = { success: 'Success', error: 'Error', info: 'Info' };
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `
      <div class="toast-icon">${icons[type] || icons.info}</div>
      <div class="toast-body">
        <div class="toast-title">${title || defaults[type]}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close" onclick="Toast.dismiss(this.parentElement)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      <div class="toast-progress"></div>
    `;

    this.container.appendChild(t);

    // Auto dismiss after 4s
    const timer = setTimeout(() => this.dismiss(t), 4000);
    t.addEventListener('mouseenter', () => clearTimeout(timer));
    t.addEventListener('mouseleave', () => setTimeout(() => this.dismiss(t), 1500));
  },

  dismiss(el) {
    if (!el || el.classList.contains('hide')) return;
    el.classList.add('hide');
    setTimeout(() => el.remove(), 300);
  }
};

/* ============================================================
   CONFIRM MODAL
   ============================================================ */
const ConfirmModal = {
  resolve: null,

  show({ title, description, confirmText = 'Delete', confirmClass = 'btn-danger', icon = 'danger' } = {}) {
    return new Promise((resolve) => {
      this.resolve = resolve;
      const icons = {
        danger: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>`,
        indigo: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
      };
      const overlay = document.createElement('div');
      overlay.className = 'modal-overlay';
      overlay.id = 'confirm-overlay';
      overlay.innerHTML = `
        <div class="modal" id="confirm-modal">
          <div class="modal-header">
            <div class="modal-icon ${icon}">${icons[icon] || icons.danger}</div>
            <div>
              <div class="modal-title">${title || 'Are you sure?'}</div>
              <div class="modal-desc">${description || 'This action cannot be undone.'}</div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-ghost" id="confirm-cancel">Cancel</button>
            <button class="btn ${confirmClass}" id="confirm-ok">${confirmText}</button>
          </div>
        </div>
      `;
      document.body.appendChild(overlay);

      overlay.querySelector('#confirm-cancel').addEventListener('click', () => this._close(false));
      overlay.querySelector('#confirm-ok').addEventListener('click', () => this._close(true));
      overlay.addEventListener('click', (e) => { if (e.target === overlay) this._close(false); });

      // ESC to close
      const escHandler = (e) => { if (e.key === 'Escape') { this._close(false); document.removeEventListener('keydown', escHandler); } };
      document.addEventListener('keydown', escHandler);
    });
  },

  _close(result) {
    const overlay = document.getElementById('confirm-overlay');
    const modal   = document.getElementById('confirm-modal');
    if (!overlay) return;
    overlay.classList.add('hide');
    modal && modal.classList.add('hide');
    setTimeout(() => overlay.remove(), 250);
    if (this.resolve) { this.resolve(result); this.resolve = null; }
  }
};

/* ============================================================
   SIDEBAR TOGGLE
   ============================================================ */
function initSidebar() {
  const hamburger = document.getElementById('hamburger-btn');
  const sidebar   = document.getElementById('sidebar');
  const overlay   = document.getElementById('sidebar-overlay');

  if (!hamburger || !sidebar) return;

  hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  });

  overlay && overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
  });
}

/* ============================================================
   DELETE FORM CONFIRMATION (intercept all delete forms)
   ============================================================ */
function initDeleteForms() {
  document.querySelectorAll('[data-confirm-delete]').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const title = form.dataset.confirmTitle || 'Delete this item?';
      const desc  = form.dataset.confirmDesc  || 'This action cannot be undone. The changes will be reversed.';
      const ok    = await ConfirmModal.show({ title, description: desc, confirmText: 'Yes, Delete', confirmClass: 'btn-danger', icon: 'danger' });
      if (ok) form.submit();
    });
  });
}

/* ============================================================
   COUNTER ANIMATION
   ============================================================ */
function animateCounters() {
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseFloat(el.dataset.count);
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    const decimals = el.dataset.decimals || 0;
    const duration = 800;
    let start = null;

    function step(ts) {
      if (!start) start = ts;
      const progress = Math.min((ts - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = target * eased;
      el.textContent = prefix + current.toLocaleString('en-US', { minimumFractionDigits: +decimals, maximumFractionDigits: +decimals }) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  });
}

/* ============================================================
   BOOTSTRAP
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  Toast.init();
  initSidebar();
  initDeleteForms();
  animateCounters();
});
