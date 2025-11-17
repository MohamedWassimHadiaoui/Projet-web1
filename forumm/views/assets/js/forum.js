document.addEventListener('DOMContentLoaded', function() {
    initCharCounters();
    initFilters();
});

function initCharCounters() {
    const contenu = document.getElementById('contenu');
    const charCount = document.getElementById('charCount');
    
    if (contenu && charCount) {
        contenu.addEventListener('input', function() {
            const remaining = 5000 - this.value.length;
            charCount.textContent = remaining;
        });
    }
}

function validatePublicationForm() {
    let isValid = true;
    
    if (!validateField('titre')) isValid = false;
    if (!validateField('categorie')) isValid = false;
    if (!validateField('contenu')) isValid = false;
    if (document.getElementById('auteur') && !validateField('auteur')) isValid = false;
    
    return isValid;
}

function validateCommentForm() {
    let isValid = true;
    
    if (!validateField('auteur')) isValid = false;
    if (!validateField('contenu')) isValid = false;
    
    return isValid;
}

function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId + 'Error');
    
    if (!field) return true;
    
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire';
    }
    
    if (fieldId === 'titre') {
        if (value && value.length < 5) {
            isValid = false;
            errorMessage = 'Le titre doit contenir au moins 5 caractères';
        }
        if (value && value.length > 255) {
            isValid = false;
            errorMessage = 'Le titre ne peut pas dépasser 255 caractères';
        }
    }
    
    if (fieldId === 'contenu') {
        if (value && value.length < 10) {
            isValid = false;
            errorMessage = 'Le contenu doit contenir au moins 10 caractères';
        }
        if (value && value.length > 5000) {
            isValid = false;
            errorMessage = 'Le contenu ne peut pas dépasser 5000 caractères';
        }
    }
    
    if (fieldId === 'categorie') {
        if (!value) {
            isValid = false;
            errorMessage = 'Veuillez sélectionner une catégorie';
        }
    }
    
    if (fieldId === 'auteur') {
        if (value && value.length < 2) {
            isValid = false;
            errorMessage = 'Le nom doit contenir au moins 2 caractères';
        }
        if (value && value.length > 100) {
            isValid = false;
            errorMessage = 'Le nom ne peut pas dépasser 100 caractères';
        }
    }
    
    if (errorElement) {
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.textContent = errorMessage;
        }
    }
    
    return isValid;
}

function filterPosts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const posts = document.querySelectorAll('.forum-post');
    
    posts.forEach(function(post) {
        const postCategory = post.getAttribute('data-category');
        const postText = post.textContent.toLowerCase();
        
        let matches = true;
        
        if (searchTerm && !postText.includes(searchTerm)) {
            matches = false;
        }
        
        if (category && postCategory !== category) {
            matches = false;
        }
        
        if (matches) {
            post.style.display = '';
        } else {
            post.style.display = 'none';
        }
    });
}

function initFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterPosts, 300));
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterPosts);
    }
}

