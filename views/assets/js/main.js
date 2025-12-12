document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initModals();
    initForms();
    initTabs();
    initTables();
    initFilters();
    initTooltips();
});

function initNavigation() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
            const icon = navbarToggle.querySelector('i') || navbarToggle;
            icon.textContent = navbarMenu.classList.contains('active') ? '✕' : '☰';
        });
        
        const menuLinks = navbarMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navbarMenu.classList.remove('active');
                    const icon = navbarToggle.querySelector('i') || navbarToggle;
                    icon.textContent = '☰';
                }
            });
        });
        
        document.addEventListener('click', function(e) {
            if (!navbarMenu.contains(e.target) && !navbarToggle.contains(e.target)) {
                navbarMenu.classList.remove('active');
                const icon = navbarToggle.querySelector('i') || navbarToggle;
                icon.textContent = '☰';
            }
        });
    }
    
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', throttle(function() {
            if (window.scrollY > 10) {
                navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            }
        }, 100));
    }
}

function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    const modalCloses = document.querySelectorAll('.modal-close, [data-close-modal]');
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
    
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        const firstInput = modal.querySelector('input, textarea, select, button');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', debounce(function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            }, 300));
        });
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });
    });
    
    const multiStepForms = document.querySelectorAll('.multi-step-form');
    multiStepForms.forEach(form => {
        initMultiStepForm(form);
    });
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    const errorElement = field.parentElement.querySelector('.form-error');
    
    let isValid = true;
    let errorMessage = '';
    
    if (required && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire';
    }
    
    if (type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Veuillez entrer une adresse email valide';
    }
    
    if (type === 'password' && value) {
        const validation = validatePassword(value);
        if (!validation.valid) {
            isValid = false;
            errorMessage = validation.errors[0];
        }
    }
    
    if (field.hasAttribute('data-confirm-password')) {
        const passwordField = document.querySelector(field.getAttribute('data-confirm-password'));
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            errorMessage = 'Les mots de passe ne correspondent pas';
        }
    }
    
    if (errorElement) {
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.textContent = errorMessage;
        }
    }
    
    return isValid;
}

function initMultiStepForm(form) {
    const steps = form.querySelectorAll('.form-step');
    const nextButtons = form.querySelectorAll('[data-next-step]');
    const prevButtons = form.querySelectorAll('[data-prev-step]');
    const progressBar = form.querySelector('.progress-bar');
    
    let currentStep = 0;
    
    showStep(0);
    
    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currentStepElement = steps[currentStep];
            const inputs = currentStepElement.querySelectorAll('input[required], textarea[required], select[required]');
            
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress();
                }
            }
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgress();
            }
        });
    });
    
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            if (index === stepIndex) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }
    
    function updateProgress() {
        if (progressBar) {
            const progress = ((currentStep + 1) / steps.length) * 100;
            progressBar.style.width = progress + '%';
        }
    }
}

function initTabs() {
    const tabContainers = document.querySelectorAll('.tabs-container');
    
    tabContainers.forEach(container => {
        const tabs = container.querySelectorAll('.tab-item');
        const contents = container.querySelectorAll('.tab-content');
        
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active');
                if (contents[index]) {
                    contents[index].classList.add('active');
                }
            });
        });
    });
}

function initTables() {
    const sortableHeaders = document.querySelectorAll('.table th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            const currentSort = this.classList.contains('sort-asc') ? 'asc' : 
                              this.classList.contains('sort-desc') ? 'desc' : null;
            
            sortableHeaders.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            rows.sort((a, b) => {
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();
                
                const aNum = parseFloat(aText);
                const bNum = parseFloat(bText);
                
                let comparison = 0;
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    comparison = aNum - bNum;
                } else {
                    comparison = aText.localeCompare(bText, 'fr');
                }
                
                return currentSort === 'asc' ? -comparison : comparison;
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            this.classList.remove('sort-asc', 'sort-desc');
            this.classList.add(currentSort === 'asc' ? 'sort-desc' : 'sort-asc');
        });
    });
}

function initFilters() {
    const filterInputs = document.querySelectorAll('.filter-input');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const filterValue = this.value.toLowerCase();
            const filterTarget = this.getAttribute('data-filter');
            const items = document.querySelectorAll(filterTarget);
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }, 300));
    });
}

function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.position = 'absolute';
            tooltip.style.background = '#1f2937';
            tooltip.style.color = '#fff';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '0.875rem';
            tooltip.style.zIndex = '10000';
            tooltip.style.pointerEvents = 'none';
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 8) + 'px';
            tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
            
            this._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                document.body.removeChild(this._tooltip);
                this._tooltip = null;
            }
        });
    });
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        openModal,
        closeModal,
        validateField,
        initNavigation,
        initModals,
        initForms,
        initTabs,
        initTables,
        initFilters
    };
}

