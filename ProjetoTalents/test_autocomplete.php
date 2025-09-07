<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Autocompletar Endereços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .suggestions-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
        }
        
        .suggestion-item {
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .suggestion-item:hover {
            background-color: #f8f9fa !important;
            border-left: 3px solid #007bff !important;
        }
        
        .suggestion-item .fas {
            color: #007bff;
        }
        
        .suggestion-item .text-muted {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-map-marker-alt me-2"></i>Teste de Autocompletar Endereços</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="teste1" class="form-label">Teste 1 - Digite "ENIAC":</label>
                            <input type="text" 
                                   id="teste1" 
                                   class="form-control" 
                                   placeholder="Digite ENIAC..."
                                   data-suggestions="endereco"
                                   autocomplete="off">
                        </div>
                        
                        <div class="mb-4">
                            <label for="teste2" class="form-label">Teste 2 - Digite "MCDONALDS":</label>
                            <input type="text" 
                                   id="teste2" 
                                   class="form-control" 
                                   placeholder="Digite MCDONALDS..."
                                   data-suggestions="endereco"
                                   autocomplete="off">
                        </div>
                        
                        <div class="mb-4">
                            <label for="teste3" class="form-label">Teste 3 - Digite "HOSPITAL":</label>
                            <input type="text" 
                                   id="teste3" 
                                   class="form-control" 
                                   placeholder="Digite HOSPITAL..."
                                   data-suggestions="endereco"
                                   autocomplete="off">
                        </div>
                        
                        <div class="mb-4">
                            <label for="teste4" class="form-label">Teste 4 - Digite "SHOPPING":</label>
                            <input type="text" 
                                   id="teste4" 
                                   class="form-control" 
                                   placeholder="Digite SHOPPING..."
                                   data-suggestions="endereco"
                                   autocomplete="off">
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Como Funciona:</h6>
                            <ul class="mb-0">
                                <li>Digite pelo menos 2 caracteres para ativar as sugestões</li>
                                <li>As sugestões aparecem automaticamente abaixo do campo</li>
                                <li>Clique em uma sugestão para selecioná-la</li>
                                <li>As sugestões são ordenadas por relevância</li>
                                <li>Cada sugestão mostra ícone, categoria e tipo de local</li>
                                <li>Funciona tanto para origem quanto destino na calculadora</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-success">
                            <h6><i class="fas fa-star me-2"></i>Tipos de Locais Disponíveis:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><i class="fas fa-university text-primary me-1"></i> Universidades (USP, UNICAMP, PUC)</li>
                                        <li><i class="fas fa-graduation-cap text-info me-1"></i> Escolas e Colégios</li>
                                        <li><i class="fas fa-hospital text-danger me-1"></i> Hospitais</li>
                                        <li><i class="fas fa-shopping-bag text-warning me-1"></i> Shoppings</li>
                                        <li><i class="fas fa-utensils text-success me-1"></i> Restaurantes</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><i class="fas fa-university text-primary me-1"></i> Bancos</li>
                                        <li><i class="fas fa-plane text-info me-1"></i> Aeroportos</li>
                                        <li><i class="fas fa-bus text-secondary me-1"></i> Estações e Terminais</li>
                                        <li><i class="fas fa-tree text-success me-1"></i> Parques</li>
                                        <li><i class="fas fa-theater-masks text-purple me-1"></i> Cultura (Museus, Teatros)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar sugestões
        document.addEventListener('DOMContentLoaded', function() {
            initSearchSuggestions();
        });
        
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
                url = `api/sugestoes_endereco.php?termo=${encodeURIComponent(query)}`;
            } else {
                url = `app/controllers/SearchController.php?action=suggestions&type=${type}&q=${encodeURIComponent(query)}`;
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
                    // Formato de endereço com relevância
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="fw-bold">${suggestion.endereco}</span>
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
    </script>
</body>
</html>
