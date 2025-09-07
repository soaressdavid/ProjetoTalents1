// TalentsHUB - JavaScript Principal

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    initTooltips();
    initAlerts();
    initSearchSuggestions();
    initFileUpload();
    initNotifications();
    initAnimations();
});

// Tooltips
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Auto-hide alerts
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });
}

// Search suggestions
function initSearchSuggestions() {
    const searchInputs = document.querySelectorAll('[data-suggestions]');
    
    searchInputs.forEach(input => {
        let timeout;
        const suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'suggestions-container position-absolute w-100 bg-white border rounded shadow-lg';
        suggestionsContainer.style.display = 'none';
        suggestionsContainer.style.zIndex = '1000';
        suggestionsContainer.style.maxHeight = '200px';
        suggestionsContainer.style.overflowY = 'auto';
        
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(suggestionsContainer);
        
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            timeout = setTimeout(() => {
                fetchSuggestions(this.dataset.suggestions, query)
                    .then(suggestions => {
                        displaySuggestions(suggestions, suggestionsContainer, input);
                    })
                    .catch(error => {
                        console.error('Erro ao buscar sugestões:', error);
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    });
}

function fetchSuggestions(type, query) {
    let url;
    
    if (type === 'endereco') {
        url = `/ProjetoTalents/api/sugestoes_endereco.php?termo=${encodeURIComponent(query)}`;
    } else {
        url = `/ProjetoTalents/app/controllers/SearchController.php?action=suggestions&type=${type}&q=${encodeURIComponent(query)}`;
    }
    
    return fetch(url)
        .then(response => response.json())
        .catch(error => {
            console.error('Erro na requisição:', error);
            return [];
        });
}

function displaySuggestions(suggestions, container, input) {
    if (suggestions.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.innerHTML = '';
    suggestions.forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item p-2 cursor-pointer';
        item.style.cursor = 'pointer';
        
        // Verificar se é um objeto de endereço ou string simples
        if (typeof suggestion === 'object' && suggestion.endereco) {
            // Formato de endereço com relevância e categoria
            const icone = obterIconeCategoria(suggestion.categoria);
            const cor = obterCorCategoria(suggestion.categoria);
            
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-${icone} ${cor} me-2"></i>
                        <span class="fw-bold">${suggestion.endereco}</span>
                        <div class="small text-muted">${obterNomeCategoria(suggestion.categoria)}</div>
                    </div>
                    <div class="text-muted small">
                        ${suggestion.tipo === 'exata' ? 'Exata' : 'Parcial'}
                    </div>
                </div>
            `;
            item.addEventListener('click', function() {
                input.value = suggestion.endereco;
                container.style.display = 'none';
                input.focus();
            });
        } else {
            // Formato string simples (compatibilidade com outras sugestões)
            item.textContent = suggestion;
            item.addEventListener('click', function() {
                input.value = suggestion;
                container.style.display = 'none';
                input.focus();
            });
        }
        
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8fafc';
            this.style.borderLeft = '3px solid #007bff';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
            this.style.borderLeft = 'none';
        });
        
        container.appendChild(item);
    });
    
    container.style.display = 'block';
}

// Funções auxiliares para categorias
function obterIconeCategoria(categoria) {
    const icones = {
        'universidade': 'university',
        'escola': 'graduation-cap',
        'hospital': 'hospital',
        'shopping': 'shopping-bag',
        'restaurante': 'utensils',
        'banco': 'university',
        'aeroporto': 'plane',
        'transporte': 'bus',
        'parque': 'tree',
        'cultura': 'theater-masks',
        'farmacia': 'pills',
        'posto': 'gas-pump',
        'supermercado': 'shopping-cart',
        'praca': 'square',
        'endereco': 'map-marker-alt'
    };
    return icones[categoria] || 'map-marker-alt';
}

function obterCorCategoria(categoria) {
    const cores = {
        'universidade': 'text-primary',
        'escola': 'text-info',
        'hospital': 'text-danger',
        'shopping': 'text-warning',
        'restaurante': 'text-success',
        'banco': 'text-primary',
        'aeroporto': 'text-info',
        'transporte': 'text-secondary',
        'parque': 'text-success',
        'cultura': 'text-purple',
        'farmacia': 'text-danger',
        'posto': 'text-warning',
        'supermercado': 'text-success',
        'praca': 'text-info',
        'endereco': 'text-primary'
    };
    return cores[categoria] || 'text-primary';
}

function obterNomeCategoria(categoria) {
    const nomes = {
        'universidade': 'Universidade',
        'escola': 'Escola',
        'hospital': 'Hospital',
        'shopping': 'Shopping',
        'restaurante': 'Restaurante',
        'banco': 'Banco',
        'aeroporto': 'Aeroporto',
        'transporte': 'Transporte',
        'parque': 'Parque',
        'cultura': 'Cultura',
        'farmacia': 'Farmácia',
        'posto': 'Posto',
        'supermercado': 'Supermercado',
        'praca': 'Praça',
        'endereco': 'Endereço'
    };
    return nomes[categoria] || 'Local';
}

// File upload
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        const label = input.nextElementSibling;
        if (label && label.classList.contains('file-upload-label')) {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    label.textContent = `Arquivo selecionado: ${fileName}`;
                    label.style.backgroundColor = '#d1fae5';
                    label.style.color = '#065f46';
                } else {
                    label.textContent = 'Selecionar arquivo';
                    label.style.backgroundColor = '';
                    label.style.color = '';
                }
            });
        }
    });
}

// Notifications
function initNotifications() {
    // Check for unread notifications
    checkUnreadNotifications();
    
    // Set up notification polling
    setInterval(checkUnreadNotifications, 30000); // Check every 30 seconds
}

function checkUnreadNotifications() {
    fetch('/ProjetoTalents/app/controllers/NotificationController.php?action=count')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
        })
        .catch(error => {
            console.error('Erro ao verificar notificações:', error);
        });
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
            badge.classList.add('animate-pulse');
        } else {
            badge.style.display = 'none';
            badge.classList.remove('animate-pulse');
        }
    }
}

// Animations
function initAnimations() {
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements with animation classes
    document.querySelectorAll('.card, .stat-card, .job-card, .company-card').forEach(el => {
        observer.observe(el);
    });
}

// Utility functions
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="spinner me-2"></span>Carregando...';
    element.disabled = true;
    
    return function hideLoading() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}

// Form validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Password strength checker
function checkPasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };
    
    if (password.length >= 8) strength.score++;
    else strength.feedback.push('Mínimo 8 caracteres');
    
    if (/[a-z]/.test(password)) strength.score++;
    else strength.feedback.push('Pelo menos uma letra minúscula');
    
    if (/[A-Z]/.test(password)) strength.score++;
    else strength.feedback.push('Pelo menos uma letra maiúscula');
    
    if (/[0-9]/.test(password)) strength.score++;
    else strength.feedback.push('Pelo menos um número');
    
    if (/[^A-Za-z0-9]/.test(password)) strength.score++;
    else strength.feedback.push('Pelo menos um caractere especial');
    
    return strength;
}

// Initialize password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordInputs = document.querySelectorAll('input[type="password"][data-strength]');
    
    passwordInputs.forEach(input => {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength mt-2';
        input.parentNode.appendChild(strengthIndicator);
        
        input.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updatePasswordStrengthIndicator(strengthIndicator, strength);
        });
    });
});

function updatePasswordStrengthIndicator(indicator, strength) {
    const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#16a34a'];
    const labels = ['Muito fraca', 'Fraca', 'Média', 'Forte', 'Muito forte'];
    
    indicator.innerHTML = `
        <div class="progress" style="height: 4px;">
            <div class="progress-bar" style="width: ${(strength.score / 5) * 100}%; background-color: ${colors[strength.score - 1] || colors[0]};"></div>
        </div>
        <small class="text-muted">${labels[strength.score - 1] || labels[0]}</small>
        ${strength.feedback.length > 0 ? `<div class="text-muted small mt-1">${strength.feedback.join(', ')}</div>` : ''}
    `;
}

// AJAX form submission
function submitFormAjax(form, successCallback, errorCallback) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const hideLoading = showLoading(submitButton);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            if (successCallback) successCallback(data);
            else showToast(data.message || 'Operação realizada com sucesso!', 'success');
        } else {
            if (errorCallback) errorCallback(data);
            else showToast(data.message || 'Erro ao processar solicitação', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Erro:', error);
        if (errorCallback) errorCallback({message: 'Erro de conexão'});
        else showToast('Erro de conexão', 'danger');
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copiado para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Erro ao copiar:', err);
        showToast('Erro ao copiar', 'danger');
    });
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Format date
function formatDate(dateString) {
    return new Intl.DateTimeFormat('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(dateString));
}

// Debounce function
function debounce(func, wait) {
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

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global use
window.TalentsHUB = {
    showLoading,
    showToast,
    validateForm,
    submitFormAjax,
    copyToClipboard,
    formatCurrency,
    formatDate,
    debounce,
    throttle
};

