function formatDate(date, format = 'dd/mm/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year)
        .replace('hh', hours)
        .replace('mm', minutes);
}

function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function containsSpecialCharacters(text) {
    if (!text || text.trim() === '') {
        return false;
    }
    const regex = /^[a-zA-Z0-9\sàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ\-']+$/;
    return !regex.test(text);
}

function validateNoSpecialChars(text) {
    if (!text || text.trim() === '') {
        return { valid: true, message: '' };
    }
    
    if (containsSpecialCharacters(text)) {
        return {
            valid: false,
            message: 'pas de caractère spécial'
        };
    }
    
    return { valid: true, message: '' };
}

function containsNonLetters(text) {
    if (!text || text.trim() === '') {
        return false;
    }
    const regex = /^[a-zA-Z\sàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ\-']+$/;
    return !regex.test(text);
}

function validateOnlyLetters(text) {
    if (!text || text.trim() === '') {
        return { valid: true, message: '' };
    }
    
    if (containsNonLetters(text)) {
        return {
            valid: false,
            message: 'seulement des lettres'
        };
    }
    
    return { valid: true, message: '' };
}

function validatePassword(password, minLength = 8) {
    const errors = [];
    
    if (password.length < minLength) {
        errors.push(`Le mot de passe doit contenir au moins ${minLength} caractères`);
    }
    
    if (!/[A-Z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une majuscule');
    }
    
    if (!/[a-z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une minuscule');
    }
    
    if (!/[0-9]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un chiffre');
    }
    
    if (!/[^A-Za-z0-9]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un caractère spécial');
    }
    
    return {
        valid: errors.length === 0,
        errors: errors
    };
}

function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit = 300) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (err) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(textArea);
            return true;
        } catch (err) {
            document.body.removeChild(textArea);
            return false;
        }
    }
}

function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '3000';
    toast.style.minWidth = '300px';
    toast.style.animation = 'slideIn 0.3s ease-out';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, duration);
}

function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
}

function getUrlParameter(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function setUrlParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, '', url);
}

function removeUrlParameter(param) {
    const url = new URL(window.location);
    url.searchParams.delete(param);
    window.history.pushState({}, '', url);
}

function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function smoothScrollTo(target, offset = 0) {
    const element = typeof target === 'string' ? document.querySelector(target) : target;
    if (element) {
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

async function generateAISuggestion(input) {
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    const suggestions = {
        'violence': 'Je recommande de contacter immédiatement les services d\'urgence (17) et de signaler l\'incident sur la plateforme.',
        'harcèlement': 'Pour le harcèlement, documentez tous les incidents avec dates et preuves. Contactez un conseiller juridique si nécessaire.',
        'discrimination': 'La discrimination est illégale. Rassemblez des preuves et contactez le Défenseur des droits.',
        'aide': 'Plusieurs ressources sont disponibles : assistance juridique, soutien psychologique, et médiation communautaire.',
        'conflit': 'Pour résoudre un conflit, je suggère de commencer par une médiation avec un tiers neutre.'
    };
    
    const lowerInput = input.toLowerCase();
    for (const [keyword, suggestion] of Object.entries(suggestions)) {
        if (lowerInput.includes(keyword)) {
            return suggestion;
        }
    }
    
    return 'Je vous recommande de remplir le formulaire avec le maximum de détails. Un conseiller vous contactera dans les plus brefs délais pour vous accompagner.';
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatDate,
        isValidEmail,
        validatePassword,
        containsSpecialCharacters,
        validateNoSpecialChars,
        containsNonLetters,
        validateOnlyLetters,
        debounce,
        throttle,
        generateId,
        copyToClipboard,
        showToast,
        formatNumber,
        truncateText,
        getUrlParameter,
        setUrlParameter,
        removeUrlParameter,
        isInViewport,
        smoothScrollTo,
        generateAISuggestion
    };
}
