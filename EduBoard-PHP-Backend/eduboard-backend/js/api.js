// =====================================================
// EduBoard — Shared API Helper (js/api.js)
// Include this in every HTML page
// =====================================================

// Change this if your PHP files are in a different folder
const API_BASE = '../api';  // relative path from pages/ folder

const api = {
    async call(endpoint, method = 'GET', body = null) {
        const opts = {
            method,
            credentials: 'include',           // send session cookie
            headers: { 'Content-Type': 'application/json' }
        };
        if (body) opts.body = JSON.stringify(body);

        try {
            const res = await fetch(`${API_BASE}/${endpoint}`, opts);
            const data = await res.json();

            if (data.redirect) {
                window.location.href = '../index.html';
                return null;
            }
            return data;
        } catch (err) {
            console.error('API error:', err);
            return { success: false, message: 'Network error. Is the server running?' };
        }
    },

    // Auth
    login: (email, password)    => api.call('login.php', 'POST', { email, password }),
    logout: ()                  => api.call('logout.php', 'POST'),
    me: ()                      => api.call('me.php'),

    // Notices
    getNotices: (params = {}) => {
        const qs = new URLSearchParams(params).toString();
        return api.call(`notices.php${qs ? '?' + qs : ''}`);
    },
    getNotice: (id)             => api.call(`notices.php?id=${id}`),
    createNotice: (data)        => api.call('notices.php', 'POST', data),
    updateNotice: (data)        => api.call('notices.php', 'PUT', data),
    deleteNotice: (id)          => api.call(`notices.php?id=${id}`, 'DELETE'),

    // Users (admin only)
    getUsers: ()                => api.call('users.php'),
    createUser: (data)          => api.call('users.php', 'POST', data),
    updateUser: (data)          => api.call('users.php', 'PUT', data),
    deleteUser: (id)            => api.call(`users.php?id=${id}`, 'DELETE'),
};

// Toast notification helper
function toast(message, type = 'success') {
    const existing = document.getElementById('toast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.id = 'toast';
    t.textContent = message;
    t.style.cssText = `
        position:fixed; bottom:28px; right:28px; z-index:9999;
        padding:14px 22px; border-radius:12px; font-size:0.9rem; font-weight:500;
        background:${type === 'success' ? '#4caf8a' : '#e8486a'}; color:#fff;
        box-shadow:0 8px 30px rgba(0,0,0,0.3);
        animation:slideIn 0.3s ease;
        font-family:'DM Sans',sans-serif;
    `;
    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}`;
    document.head.appendChild(style);
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
