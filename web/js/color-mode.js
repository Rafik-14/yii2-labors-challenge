/*!
 * Color mode toggler for Bootstrap 5.3+
 * Based on https://getbootstrap.com/docs/5.3/customize/color-modes/
 *
 * Loaded in <head> so the theme is set before the page is drawn.
 * Light is the default: the required link colour rgb(20, 96, 130) is only readable on a light background;
 * dark mode is available through the toggle and the choice is remembered.
 */
(() => {
    'use strict'

    const getStoredTheme = () => {
        try {
            return localStorage.getItem('theme')
        } catch (e) {
            return null
        }
    }

    const setStoredTheme = theme => {
        try {
            localStorage.setItem('theme', theme)
        } catch (e) {
            // storage unavailable (private mode, blocked cookies): the theme still switches for this page
        }
    }

    const getPreferredTheme = () => {
        const stored = getStoredTheme()
        return stored === 'dark' || stored === 'light' ? stored : 'light'
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    // the translated labels come from the button's data attributes (rendered by the PHP layout)
    const updateToggle = theme => {
        const toggle = document.getElementById('theme-toggle')
        if (!toggle) return
        const label = theme === 'dark' ? toggle.dataset.labelLight : toggle.dataset.labelDark
        toggle.textContent = theme === 'dark' ? '☀' : '🌙'
        toggle.setAttribute('aria-label', label)
        toggle.setAttribute('title', label)
    }

    setTheme(getPreferredTheme())

    document.addEventListener('DOMContentLoaded', () => {
        updateToggle(getPreferredTheme())

        const toggle = document.getElementById('theme-toggle')
        if (toggle) {
            toggle.addEventListener('click', () => {
                const newTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark'
                    ? 'light'
                    : 'dark'
                setStoredTheme(newTheme)
                setTheme(newTheme)
                updateToggle(newTheme)
            })
        }
    })
})()
