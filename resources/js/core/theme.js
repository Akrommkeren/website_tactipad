/**
 * TACTIPAD: Core Theme Manager (Light & Dark / High-Contrast Theme)
 */

export function initTheme() {
    const isDark = localStorage.getItem('tactipad_contrast') === 'true';
    applyTheme(isDark);

    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentlyDark = document.body.classList.contains('dark-theme');
            applyTheme(!currentlyDark);
        });
    }
}

export function applyTheme(isDark) {
    localStorage.setItem('tactipad_contrast', isDark ? 'true' : 'false');
    const sunIcon = document.getElementById('themeIconSun');
    const moonIcon = document.getElementById('themeIconMoon');

    if (isDark) {
        document.body.classList.add('dark-theme');
        if (sunIcon) sunIcon.classList.add('hidden');
        if (moonIcon) moonIcon.classList.remove('hidden');
    } else {
        document.body.classList.remove('dark-theme');
        if (sunIcon) sunIcon.classList.remove('hidden');
        if (moonIcon) moonIcon.classList.add('hidden');
    }

    // Update button text on settings page if present
    const toggleContrastBtn = document.getElementById('toggleContrastBtn');
    if (toggleContrastBtn) {
        toggleContrastBtn.textContent = isDark ? 'Nonaktifkan Kontras' : 'Aktifkan Kontras';
    }
}
