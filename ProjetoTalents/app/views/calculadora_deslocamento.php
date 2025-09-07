<?php
require_once __DIR__ . '/../utils/init.php';
require_once __DIR__ . '/../../config/google_maps.php';

$vagaModel = new Vaga();
$empresaModel = new Empresa();

// Parâmetros da URL para pré-preenchimento
$empresa_url = $_GET['empresa'] ?? '';
$endereco_url = $_GET['endereco'] ?? '';

// Buscar vagas com endereços para sugestões
$vagas_com_endereco = $vagaModel->getVagasComEndereco();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Deslocamento - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Calcule o tempo de deslocamento até empresas e planeje sua rota de forma inteligente.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .calculator-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-calculate {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
        }
        
        .btn-calculate:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .result-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-top: 2rem;
            display: none;
        }
        
        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .result-label {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .result-value {
            font-size: 1.1rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .time-badge {
            background: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .suggestion-item {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .suggestion-item:hover {
            background: var(--light-color);
            border-color: var(--primary-color);
        }
        
        .suggestion-company {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .suggestion-address {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        .map-container {
            height: 400px;
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-top: 2rem;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .spinner-border {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .calculator-card {
                padding: 1.5rem;
            }
            
            .result-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="<?php echo BASE_DIR; ?>">
                    <i class="fas fa-briefcase me-2"></i><?php echo SITE_NAME; ?>
                </a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="<?php echo BASE_DIR; ?>">
                        <i class="fas fa-home me-1"></i>Início
                    </a>
                    <a class="nav-link" href="<?php echo BASE_DIR; ?>/app/views/vagas.php">
                        <i class="fas fa-search me-1"></i>Vagas
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-route me-3"></i>Calculadora de Deslocamento
                </h1>
                <p class="lead mb-0">
                    Planeje sua rota e calcule o tempo exato para chegar até a empresa
                </p>
            </div>
        </section>

        <div class="container my-5">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Calculadora -->
                    <div class="calculator-card">
                        <h3 class="mb-4">
                            <i class="fas fa-calculator me-2"></i>Calcular Tempo de Deslocamento
                        </h3>
                        
                        <?php if (!empty($empresa_url)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Empresa selecionada:</strong> <?php echo htmlspecialchars($empresa_url); ?>
                                <?php if (!empty($endereco_url)): ?>
                                    <br><small>Endereço pré-preenchido abaixo</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (getGoogleMapsApiKey() === 'YOUR_API_KEY'): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Modo Demo:</strong> Esta calculadora está funcionando em modo de demonstração. 
                            Os tempos são estimados baseados no meio de transporte e horário selecionados. 
                            Para cálculos precisos com dados reais de transporte público, configure a API do Google Maps seguindo as instruções em 
                            <code>GOOGLE_MAPS_SETUP.md</code>.
                        </div>
                        <?php elseif (getGoogleMapsApiKey() === 'FREE_MODE'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-gift me-2"></i>
                            <strong>Modo Gratuito:</strong> Esta calculadora está funcionando com estimativas inteligentes e dados simulados realistas. 
                            <strong>100% gratuito</strong> - sem necessidade de APIs pagas!
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Os dados são calculados baseados no meio de transporte, horário e características dos endereços.
                                </small>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>API Configurada:</strong> Esta calculadora está usando dados reais do Google Maps para transporte público. 
                            As rotas e linhas de ônibus mostradas são baseadas em informações atualizadas.
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Os endereços serão geocodificados automaticamente para maior precisão.
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form id="calculatorForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="origem" class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Seu Endereço de Origem
                                        </label>
                                        <input type="text" 
                                               id="origem" 
                                               name="origem" 
                                               class="form-control" 
                                               placeholder="Digite seu endereço completo..."
                                               data-suggestions="endereco"
                                               autocomplete="off"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="destino" class="form-label">
                                            <i class="fas fa-building me-2"></i>Endereço da Empresa
                                        </label>
                                        <input type="text" 
                                               id="destino" 
                                               name="destino" 
                                               class="form-control" 
                                               placeholder="Digite o endereço da empresa..."
                                               value="<?php echo htmlspecialchars($endereco_url); ?>"
                                               data-suggestions="endereco"
                                               autocomplete="off"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_saida" class="form-label">
                                            <i class="fas fa-calendar me-2"></i>Data de Saída
                                        </label>
                                        <input type="date" 
                                               id="data_saida" 
                                               name="data_saida" 
                                               class="form-control" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora_saida" class="form-label">
                                            <i class="fas fa-clock me-2"></i>Hora de Saída
                                        </label>
                                        <input type="time" 
                                               id="hora_saida" 
                                               name="hora_saida" 
                                               class="form-control" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="meio_transporte" class="form-label">
                                            <i class="fas fa-car me-2"></i>Meio de Transporte
                                        </label>
                                        <select id="meio_transporte" name="meio_transporte" class="form-select" required>
                                            <option value="driving">Carro</option>
                                            <option value="transit">Transporte Público</option>
                                            <option value="walking">A Pé</option>
                                            <option value="bicycling">Bicicleta</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn-calculate">
                                    <i class="fas fa-route me-2"></i>Calcular Deslocamento
                                </button>
                            </div>
                        </form>
                        
                        <!-- Loading -->
                        <div class="loading" id="loading">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Calculando...</span>
                            </div>
                            <p class="mt-2">Calculando sua rota...</p>
                        </div>
                        
                        <!-- Resultado -->
                        <div class="result-card" id="resultado">
                            <h4 class="mb-3">
                                <i class="fas fa-route me-2"></i>Resultado do Cálculo
                            </h4>
                            
                            <div class="result-item">
                                <span class="result-label">
                                    <i class="fas fa-clock me-2"></i>Tempo de Viagem:
                                </span>
                                <span class="result-value" id="tempo_viagem">-</span>
                            </div>
                            
                            <div class="result-item">
                                <span class="result-label">
                                    <i class="fas fa-route me-2"></i>Distância:
                                </span>
                                <span class="result-value" id="distancia">-</span>
                            </div>
                            
                            <div class="result-item">
                                <span class="result-label">
                                    <i class="fas fa-flag-checkered me-2"></i>Chegada Prevista:
                                </span>
                                <span class="result-value" id="chegada_prevista">-</span>
                            </div>
                            
                            <div class="result-item">
                                <span class="result-label">
                                    <i class="fas fa-info-circle me-2"></i>Status do Trânsito:
                                </span>
                                <span class="result-value" id="status_transito">-</span>
                            </div>
                            
                            <div class="mt-3">
                                <button class="btn btn-outline-primary" onclick="abrirNoMaps()">
                                    <i class="fas fa-external-link-alt me-2"></i>Abrir no Google Maps
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Sugestões de Empresas -->
                    <div class="calculator-card">
                        <h5 class="mb-3">
                            <i class="fas fa-building me-2"></i>Empresas com Vagas Abertas
                        </h5>
                        
                        <?php if (!empty($vagas_com_endereco)): ?>
                            <div class="suggestions-container">
                                <?php foreach ($vagas_com_endereco as $vaga): ?>
                                    <div class="suggestion-item" onclick="selecionarEmpresa('<?php echo htmlspecialchars($vaga['endereco']); ?>', '<?php echo htmlspecialchars($vaga['razao_social']); ?>')">
                                        <div class="suggestion-company">
                                            <?php echo htmlspecialchars($vaga['razao_social']); ?>
                                        </div>
                                        <div class="suggestion-address">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($vaga['endereco']); ?>
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($vaga['titulo']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Nenhuma empresa com endereço disponível no momento.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Dicas -->
                    <div class="calculator-card">
                        <h5 class="mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Dicas para o Deslocamento
                        </h5>
                        
                        <div class="tips-list">
                            <div class="tip-item mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Horários de Pico:</strong> Evite sair entre 7h-9h e 17h-19h
                            </div>
                            
                            <div class="tip-item mb-3">
                                <i class="fas fa-bus text-primary me-2"></i>
                                <strong>Transporte Público:</strong> Considere atrasos de 10-15 minutos
                            </div>
                            
                            <div class="tip-item mb-3">
                                <i class="fas fa-car text-primary me-2"></i>
                                <strong>Carro:</strong> Adicione 20% ao tempo calculado para imprevistos
                            </div>
                            
                            <div class="tip-item">
                                <i class="fas fa-umbrella text-primary me-2"></i>
                                <strong>Chuva:</strong> Tempo pode aumentar em 30-50% em dias chuvosos
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mapa -->
            <div class="map-container" id="map"></div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
    
    <?php if (getGoogleMapsApiKey() !== 'FREE_MODE' && getGoogleMapsApiKey() !== 'YOUR_API_KEY'): ?>
    <script>
        window.USE_GOOGLE_PLACES = true;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getGoogleMapsApiKey(); ?>&libraries=places&language=pt-BR&region=BR"></script>
    <?php else: ?>
    <script>
        window.FREE_MODE = true;
    </script>
    <?php endif; ?>
    
    <script>
        let currentRoute = null;
        
        // Definir data e hora padrão
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            document.getElementById('data_saida').value = tomorrow.toISOString().split('T')[0];
            document.getElementById('hora_saida').value = '08:00';
            
            // Inicializar mapa simples
            initSimpleMap();
            
            // Inicializar Google Places Autocomplete caso disponível
            if (window.USE_GOOGLE_PLACES && window.google && google.maps && google.maps.places) {
                initPlacesAutocomplete();
            } else if (window.FREE_MODE) {
                initOsmAutocomplete();
            }
        });
        
        function initPlacesAutocomplete() {
            const inputOrigem = document.getElementById('origem');
            const inputDestino = document.getElementById('destino');
            
            // Desabilitar sugestões customizadas do site para estes campos
            inputOrigem.removeAttribute('data-suggestions');
            inputDestino.removeAttribute('data-suggestions');
            
            const options = {
                fields: ['address_components', 'formatted_address', 'geometry', 'name'],
                componentRestrictions: { country: ['br'] }
            };
            
            const autocompleteOrigem = new google.maps.places.Autocomplete(inputOrigem, options);
            const autocompleteDestino = new google.maps.places.Autocomplete(inputDestino, options);
            
            const formatAddress = (place) => {
                if (!place) return '';
                const comps = place.address_components || [];
                const get = (type) => {
                    const c = comps.find(ac => ac.types.includes(type));
                    return c ? c.long_name : '';
                };
                const getShort = (type) => {
                    const c = comps.find(ac => ac.types.includes(type));
                    return c ? c.short_name : '';
                };
                const route = get('route');
                const streetNumber = get('street_number');
                const neighborhood = get('sublocality_level_1') || get('sublocality') || get('political');
                const locality = get('locality');
                const adminArea = getShort('administrative_area_level_1');
                const parts = [];
                if (route) parts.push(streetNumber ? `${route}, ${streetNumber}` : route);
                if (neighborhood) parts.push(neighborhood);
                const cityUf = [locality, adminArea].filter(Boolean).join(' - ');
                if (cityUf) parts.push(cityUf);
                return parts.join(', ');
            };
            
            autocompleteOrigem.addListener('place_changed', () => {
                const place = autocompleteOrigem.getPlace();
                if (place && place.address_components) {
                    inputOrigem.value = formatAddress(place);
                } else if (place && place.formatted_address) {
                    inputOrigem.value = place.formatted_address;
                }
            });
            
            autocompleteDestino.addListener('place_changed', () => {
                const place = autocompleteDestino.getPlace();
                if (place && place.address_components) {
                    inputDestino.value = formatAddress(place);
                } else if (place && place.formatted_address) {
                    inputDestino.value = place.formatted_address;
                }
            });
        }
        
        function initOsmAutocomplete() {
            const inputOrigem = document.getElementById('origem');
            const inputDestino = document.getElementById('destino');
            
            // Desabilitar sugestões customizadas do site para estes campos
            inputOrigem.removeAttribute('data-suggestions');
            inputDestino.removeAttribute('data-suggestions');
            
            const attachOsm = (input) => {
                let timeout;
                const container = document.createElement('div');
                container.className = 'suggestions-container position-absolute w-100 bg-white border rounded shadow-lg';
                container.style.display = 'none';
                container.style.zIndex = '1000';
                container.style.maxHeight = '250px';
                container.style.overflowY = 'auto';
                input.parentNode.style.position = 'relative';
                input.parentNode.appendChild(container);
                
                const formatAddress = (item) => {
                    const r = item.address || {};
                    const route = r.road || r.pedestrian || r.footway || r.cycleway || r.path || r.neighbourhood || '';
                    const house = r.house_number || '';
                    const neighborhood = r.suburb || r.neighbourhood || r.village || r.hamlet || '';
                    const city = r.city || r.town || r.municipality || r.village || '';
                    const state = r.state || '';
                    const uf = r.state_code || '';
                    const cityUf = [city, (uf || state)].filter(Boolean).join(' - ');
                    const street = [route, house].filter(Boolean).join(', ');
                    return [street, neighborhood, cityUf].filter(Boolean).join(', ');
                };
                
                const search = (q) => {
                    const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=br&limit=8&q=${encodeURIComponent(q)}`;
                    return fetch(url, { headers: { 'Accept-Language': 'pt-BR' }})
                        .then(r => r.json())
                        .then(list => list.map(item => ({
                            label: formatAddress(item) || item.display_name,
                            raw: item
                        })));
                };
                
                const render = (items) => {
                    container.innerHTML = '';
                    if (!items || items.length === 0) {
                        container.style.display = 'none';
                        return;
                    }
                    items.forEach(s => {
                        const el = document.createElement('div');
                        el.className = 'suggestion-item p-2';
                        el.style.cursor = 'pointer';
                        el.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <div>
                                    <div class="fw-bold">${s.label}</div>
                                    <div class="small text-muted">OpenStreetMap</div>
                                </div>
                            </div>
                        `;
                        el.addEventListener('click', () => {
                            input.value = s.label;
                            container.style.display = 'none';
                            input.focus();
                        });
                        container.appendChild(el);
                    });
                    container.style.display = 'block';
                };
                
                input.addEventListener('input', function() {
                    const q = this.value.trim();
                    clearTimeout(timeout);
                    if (q.length < 2) {
                        container.style.display = 'none';
                        return;
                    }
                    timeout = setTimeout(() => {
                        search(q).then(render).catch(() => {
                            container.style.display = 'none';
                        });
                    }, 250);
                });
                
                document.addEventListener('click', function(e) {
                    if (!input.contains(e.target) && !container.contains(e.target)) {
                        container.style.display = 'none';
                    }
                });
            };
            
            attachOsm(inputOrigem);
            attachOsm(inputDestino);
        }
        
        function initSimpleMap() {
            // Mapa simples sem Google Maps API
            const mapContainer = document.getElementById('map');
            mapContainer.innerHTML = `
                <div style="
                    height: 100%;
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    border: 2px dashed #dee2e6;
                ">
                    <div style="text-align: center; color: #6c757d;">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h5>Mapa será exibido após calcular a rota</h5>
                        <p>Digite os endereços e clique em "Calcular Deslocamento"</p>
                    </div>
                </div>
            `;
        }
        
        function selecionarEmpresa(endereco, empresa) {
            document.getElementById('destino').value = endereco;
            
            // Destacar a empresa selecionada
            document.querySelectorAll('.suggestion-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }
        
        document.getElementById('calculatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            calcularDeslocamento();
        });
        
        function calcularDeslocamento() {
            const origem = document.getElementById('origem').value;
            const destino = document.getElementById('destino').value;
            const dataSaida = document.getElementById('data_saida').value;
            const horaSaida = document.getElementById('hora_saida').value;
            const meioTransporte = document.getElementById('meio_transporte').value;
            
            if (!origem || !destino) {
                alert('Por favor, preencha origem e destino.');
                return;
            }
            
            // Mostrar loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('resultado').style.display = 'none';
            
            // Verificar se temos API key do Google Maps
            const apiKey = '<?php echo getGoogleMapsApiKey(); ?>';
            
            // Se possuir API configurada válida, usar rota real; caso contrário, usar modo gratuito
            const isFreeMode = !apiKey || apiKey === 'YOUR_API_KEY' || apiKey === 'FREE_MODE';
            if (isFreeMode) {
                setTimeout(() => {
                    document.getElementById('loading').style.display = 'none';
                    calcularRotaGratuita(origem, destino, dataSaida, horaSaida, meioTransporte);
                }, 300);
            } else {
                calcularRotaReal(origem, destino, dataSaida, horaSaida, apiKey, meioTransporte);
            }
        }
        
        function calcularRotaReal(origem, destino, dataSaida, horaSaida, apiKey, meioTransporte) {
            // Criar data/hora de partida para a API
            const dataHoraSaida = new Date(dataSaida + 'T' + horaSaida);
            const timestamp = Math.floor(dataHoraSaida.getTime() / 1000);
            
            // Primeiro, geocodificar os endereços para obter coordenadas precisas
            Promise.all([
                geocodificarEndereco(origem, apiKey),
                geocodificarEndereco(destino, apiKey)
            ]).then(([coordsOrigem, coordsDestino]) => {
                // Usar coordenadas se disponíveis, senão usar endereços originais
                const origemFinal = coordsOrigem ? `${coordsOrigem.lat},${coordsOrigem.lng}` : origem;
                const destinoFinal = coordsDestino ? `${coordsDestino.lat},${coordsDestino.lng}` : destino;
                
                // Mapear meio de transporte para o formato da API
                let mode = 'driving';
                switch(meioTransporte) {
                    case 'transit':
                        mode = 'transit';
                        break;
                    case 'walking':
                        mode = 'walking';
                        break;
                    case 'bicycling':
                        mode = 'bicycling';
                        break;
                    default:
                        mode = 'driving';
                }
                
                // URL da Google Directions API
                const transitParams = mode === 'transit' ? `&transit_mode=bus&transit_routing_preference=fewer_transfers` : '';
                const url = `https://maps.googleapis.com/maps/api/directions/json?` +
                    `origin=${encodeURIComponent(origemFinal)}&` +
                    `destination=${encodeURIComponent(destinoFinal)}&` +
                    `mode=${mode}&` +
                    `departure_time=${timestamp}${transitParams}&` +
                    `key=${apiKey}`;
                
                console.log('Buscando rota real:', { origemFinal, destinoFinal, mode, timestamp });
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loading').style.display = 'none';
                        
                        if (data.status === 'OK' && data.routes.length > 0) {
                            processarRotaReal(data, origem, destino, dataHoraSaida, meioTransporte);
                        } else {
                            console.error('Erro na API do Google Maps:', data.error_message || 'Erro desconhecido');
                            console.error('Dados da resposta:', data);
                            // Fallback para simulação
                            calcularRotaSimulada(origem, destino, dataSaida, horaSaida, meioTransporte);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        document.getElementById('loading').style.display = 'none';
                        // Fallback para simulação
                        calcularRotaSimulada(origem, destino, dataSaida, horaSaida, meioTransporte);
                    });
            }).catch(error => {
                console.error('Erro na geocodificação:', error);
                document.getElementById('loading').style.display = 'none';
                // Fallback para simulação
                calcularRotaSimulada(origem, destino, dataSaida, horaSaida, meioTransporte);
            });
        }
        
        function geocodificarEndereco(endereco, apiKey) {
            return fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(endereco)}&key=${apiKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK' && data.results.length > 0) {
                        const location = data.results[0].geometry.location;
                        console.log(`Endereço geocodificado: ${endereco} -> ${location.lat}, ${location.lng}`);
                        return {
                            lat: location.lat,
                            lng: location.lng,
                            enderecoFormatado: data.results[0].formatted_address
                        };
                    } else {
                        console.warn(`Erro na geocodificação de: ${endereco}`, data);
                        return null;
                    }
                })
                .catch(error => {
                    console.error(`Erro na geocodificação de: ${endereco}`, error);
                    return null;
                });
        }
        
        function processarRotaReal(data, origem, destino, dataHoraSaida, meioTransporte) {
            console.log('Dados da API do Google Maps:', data);
            
            if (!data.routes || data.routes.length === 0) {
                console.error('Nenhuma rota encontrada');
                // Mostrar mensagem de erro
                document.getElementById('loading').style.display = 'none';
                document.getElementById('resultado').style.display = 'block';
                document.getElementById('tempo_viagem').innerHTML = '<span class="text-danger">Nenhuma rota encontrada</span>';
                document.getElementById('distancia').textContent = '-';
                document.getElementById('chegada_prevista').textContent = '-';
                document.getElementById('status_transito').textContent = 'Erro';
                return;
            }
            
            const route = data.routes[0];
            const leg = route.legs[0];
            
            // Calcular tempo total em minutos
            const tempoEstimado = Math.floor(leg.duration.value / 60);
            const distanciaEstimada = Math.floor(leg.distance.value / 1000);
            
            // Calcular chegada baseada no tempo de partida real
            const partidaReal = leg.departure_time ? new Date(leg.departure_time.value * 1000) : dataHoraSaida;
            const chegada = new Date(partidaReal.getTime() + (leg.duration?.value ? leg.duration.value * 1000 : tempoEstimado * 60000));
            
            // Exibir resultados básicos
            document.getElementById('tempo_viagem').innerHTML = 
                `<span class="time-badge">${leg.duration?.text || (tempoEstimado + ' min')}</span>`;
            document.getElementById('distancia').textContent = `${leg.distance?.text || (distanciaEstimada + ' km')}`;
            document.getElementById('chegada_prevista').textContent = chegada.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Status do trânsito baseado no horário real
            let statusTransito = 'Normal';
            const hora = partidaReal.getHours();
            if ((hora >= 7 && hora <= 9) || (hora >= 17 && hora <= 19)) {
                statusTransito = 'Trânsito Intenso';
            } else if ((hora >= 6 && hora <= 7) || (hora >= 9 && hora <= 10) || (hora >= 16 && hora <= 17) || (hora >= 19 && hora <= 20)) {
                statusTransito = 'Trânsito Moderado';
            }
            
            document.getElementById('status_transito').textContent = statusTransito;
            
            // Processar opções de transporte público reais apenas se for transit
            if (meioTransporte === 'transit') {
                processarOpcoesTransporteReal(data.routes, origem, destino);
            } else {
                // Esconder seção de transporte público se não for transit
                const transporteSection = document.getElementById('transporte-publico-section');
                if (transporteSection) {
                    transporteSection.style.display = 'none';
                }
            }
            
            // Mostrar resultado
            document.getElementById('resultado').style.display = 'block';
            
            // Atualizar mapa com informações básicas
            atualizarMapaSimples(origem, destino);
        }
        
        function processarOpcoesTransporteReal(routes, origem, destino) {
            // Filtrar apenas rotas que têm transporte público
            const rotasComTransito = routes.filter(route => {
                const leg = route.legs[0];
                return leg.steps.some(step => step.travel_mode === 'TRANSIT');
            });
            
            // Se não há rotas com transporte público, não mostrar a seção
            if (rotasComTransito.length === 0) {
                const transporteSection = document.getElementById('transporte-publico-section');
                if (transporteSection) {
                    transporteSection.style.display = 'none';
                }
                return;
            }
            
            // Criar ou mostrar seção de transporte público
            let transporteSection = document.getElementById('transporte-publico-section');
            if (!transporteSection) {
                transporteSection = document.createElement('div');
                transporteSection.id = 'transporte-publico-section';
                transporteSection.className = 'result-card mt-3';
                transporteSection.innerHTML = `
                    <h5 class="mb-3">
                        <i class="fas fa-bus me-2"></i>Opções de Transporte Público
                    </h5>
                    <div id="opcoes-transporte"></div>
                `;
                document.getElementById('resultado').parentNode.insertBefore(transporteSection, document.getElementById('resultado').nextSibling);
            }
            
            transporteSection.style.display = 'block';
            
            const opcoesContainer = document.getElementById('opcoes-transporte');
            opcoesContainer.innerHTML = '';
            
            // Processar cada rota com transporte público (máximo 3)
            const rotasParaMostrar = rotasComTransito.slice(0, 3);
            
            rotasParaMostrar.forEach((route, routeIndex) => {
                const leg = route.legs[0];
                
                // Calcular informações da rota
                const tempoTotal = Math.floor(leg.duration.value / 60);
                const distancia = Math.floor(leg.distance.value / 1000);
                const trocas = leg.steps.filter(step => step.travel_mode === 'TRANSIT').length - 1;
                
                // Processar etapas da viagem (apenas transporte público e caminhada)
                const etapas = processarEtapasViagem(leg.steps);
                
                // Verificar se há etapas de transporte público
                const temTransito = etapas.some(etapa => 
                    etapa.icone === 'subway' || 
                    etapa.icone === 'bus' || 
                    etapa.icone === 'train'
                );
                
                // Só mostrar se tiver transporte público
                if (!temTransito) {
                    return;
                }
                
                const opcaoElement = document.createElement('div');
                opcaoElement.className = `opcao-transporte mb-3 p-3 border rounded ${routeIndex === 0 ? 'border-primary bg-light' : ''}`;
                opcaoElement.setAttribute('data-opcao', routeIndex);
                
                // Calcular horários
                const partida = leg.departure_time ? new Date(leg.departure_time.value * 1000) : new Date();
                const chegada = leg.arrival_time ? new Date(leg.arrival_time.value * 1000) : new Date(partida.getTime() + tempoTotal * 60000);
                
                opcaoElement.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 ${routeIndex === 0 ? 'text-primary' : ''}">
                                <i class="fas fa-route me-2"></i>
                                Rota ${routeIndex + 1}
                                ${routeIndex === 0 ? '<span class="badge bg-primary ms-2">Recomendada</span>' : ''}
                            </h6>
                            <small class="text-muted">${distancia} km • ${trocas} troca${trocas > 1 ? 's' : ''}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold ${routeIndex === 0 ? 'text-primary' : ''}">${tempoTotal} min</div>
                            <small class="text-muted">
                                ${partida.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})} → 
                                ${chegada.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
                            </small>
                        </div>
                    </div>
                    <div class="rota-detalhada">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <span class="small">${origem}</span>
                        </div>
                        ${etapas.map(etapa => `
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-${etapa.icone} text-primary me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${etapa.nome}</div>
                                    <div class="small text-muted">${etapa.descricao}</div>
                                </div>
                                <div class="text-muted small">${etapa.tempo} min</div>
                            </div>
                        `).join('')}
                        <div class="d-flex align-items-center">
                            <i class="fas fa-flag-checkered text-danger me-2"></i>
                            <span class="small">${destino}</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Dados fornecidos pelo Google Maps
                        </small>
                    </div>
                `;
                
                // Adicionar evento de clique
                opcaoElement.addEventListener('click', function() {
                    selecionarOpcaoTransporte(routeIndex, {
                        tempo: tempoTotal,
                        trocas: trocas,
                        tipo: 'transit',
                        titulo: `Rota ${routeIndex + 1}`,
                        observacoes: 'Dados fornecidos pelo Google Maps'
                    });
                });
                
                opcoesContainer.appendChild(opcaoElement);
            });
            
            // Se não há opções viáveis, mostrar mensagem
            if (opcoesContainer.children.length === 0) {
                opcoesContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Nenhuma opção de transporte público encontrada</strong><br>
                        <small>Não há linhas de ônibus ou metrô disponíveis para esta rota no horário selecionado.</small>
                    </div>
                `;
            }
        }
        
        function processarEtapasViagem(steps) {
            const etapas = [];
            
            steps.forEach(step => {
                if (step.travel_mode === 'WALKING') {
                    // Limpar instruções HTML
                    const descricao = step.html_instructions.replace(/<[^>]*>/g, '').trim();
                    if (descricao && descricao !== 'Caminhe') {
                        etapas.push({
                            icone: 'walking',
                            nome: 'Caminhada',
                            descricao: descricao,
                            tempo: Math.floor(step.duration.value / 60)
                        });
                    }
                } else if (step.travel_mode === 'TRANSIT' && step.transit_details) {
                    const transit = step.transit_details;
                    const icone = transit.line.vehicle.type === 'SUBWAY' ? 'subway' : 
                                 transit.line.vehicle.type === 'TRAIN' ? 'train' : 
                                 transit.line.vehicle.type === 'TRAM' ? 'train' : 'bus';
                    
                    // Obter nome da linha
                    const nomeLinha = transit.line.short_name || transit.line.name || 'Linha de Transporte';
                    
                    // Obter descrição das estações
                    const estacaoOrigem = transit.departure_stop ? transit.departure_stop.name : 'Ponto de partida';
                    const estacaoDestino = transit.arrival_stop ? transit.arrival_stop.name : 'Ponto de chegada';
                    
                    etapas.push({
                        icone: icone,
                        nome: nomeLinha,
                        descricao: `${estacaoOrigem} → ${estacaoDestino}`,
                        tempo: Math.floor(step.duration.value / 60)
                    });
                }
            });
            
            return etapas;
        }
        
        function calcularRotaGratuita(origem, destino, dataSaida, horaSaida, meioTransporte) {
            // Modo gratuito com dados simulados realistas e inteligentes
            const dataHoraSaida = new Date(dataSaida + 'T' + horaSaida);
            
            // Calcular distância baseada no tamanho dos endereços (simulação inteligente)
            const tamanhoOrigem = origem.length;
            const tamanhoDestino = destino.length;
            const diferencaTamanho = Math.abs(tamanhoOrigem - tamanhoDestino);
            
            // Base de cálculo mais realista
            let tempoEstimado, distanciaEstimada;
            
            // Simular cálculo baseado no meio de transporte
            switch(meioTransporte) {
                case 'driving':
                    tempoEstimado = 35 + Math.floor(diferencaTamanho * 0.8); // 35-55 minutos
                    distanciaEstimada = 8 + Math.floor(diferencaTamanho * 0.3); // 8-20 km
                    break;
                case 'transit':
                    tempoEstimado = 50 + Math.floor(diferencaTamanho * 1.2); // 50-80 minutos
                    distanciaEstimada = 6 + Math.floor(diferencaTamanho * 0.4); // 6-18 km
                    break;
                case 'walking':
                    tempoEstimado = 80 + Math.floor(diferencaTamanho * 2); // 80-140 minutos
                    distanciaEstimada = 3 + Math.floor(diferencaTamanho * 0.2); // 3-8 km
                    break;
                case 'bicycling':
                    tempoEstimado = 25 + Math.floor(diferencaTamanho * 0.6); // 25-45 minutos
                    distanciaEstimada = 5 + Math.floor(diferencaTamanho * 0.3); // 5-15 km
                    break;
                default:
                    tempoEstimado = 40 + Math.floor(diferencaTamanho * 0.8);
                    distanciaEstimada = 10 + Math.floor(diferencaTamanho * 0.3);
            }
            
            // Ajustar baseado no horário (simular trânsito realista)
            const hora = dataHoraSaida.getHours();
            const diaSemana = dataHoraSaida.getDay(); // 0 = domingo, 6 = sábado
            
            // Horário de pico mais realista
            if (diaSemana >= 1 && diaSemana <= 5) { // Segunda a sexta
                if ((hora >= 7 && hora <= 9) || (hora >= 17 && hora <= 19)) {
                    tempoEstimado = Math.floor(tempoEstimado * 1.4); // +40% no horário de pico
                } else if ((hora >= 6 && hora <= 7) || (hora >= 9 && hora <= 10) || (hora >= 16 && hora <= 17) || (hora >= 19 && hora <= 20)) {
                    tempoEstimado = Math.floor(tempoEstimado * 1.15); // +15% no horário moderado
                }
            } else if (diaSemana === 0 || diaSemana === 6) { // Fim de semana
                tempoEstimado = Math.floor(tempoEstimado * 0.9); // -10% no fim de semana
            }
            
            // Calcular chegada
            const chegada = new Date(dataHoraSaida.getTime() + tempoEstimado * 60000);
            
            // Exibir resultados
            document.getElementById('tempo_viagem').innerHTML = 
                `<span class="time-badge">${tempoEstimado} minutos</span>`;
            document.getElementById('distancia').textContent = `${distanciaEstimada} km`;
            document.getElementById('chegada_prevista').textContent = chegada.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Status do trânsito mais realista
            let statusTransito = 'Normal';
            if (diaSemana >= 1 && diaSemana <= 5) { // Dias úteis
                if ((hora >= 7 && hora <= 9) || (hora >= 17 && hora <= 19)) {
                    statusTransito = 'Trânsito Intenso';
                } else if ((hora >= 6 && hora <= 7) || (hora >= 9 && hora <= 10) || (hora >= 16 && hora <= 17) || (hora >= 19 && hora <= 20)) {
                    statusTransito = 'Trânsito Moderado';
                }
            } else {
                statusTransito = 'Trânsito Leve';
            }
            
            document.getElementById('status_transito').textContent = statusTransito;
            
            // Se for transporte público, mostrar opções simuladas
            if (meioTransporte === 'transit') {
                mostrarOpcoesTransporteGratuito(origem, destino, tempoEstimado, distanciaEstimada);
                // Buscar linhas reais (OSM) no modo gratuito
                sugerirLinhasOnibusComOSM(origem, destino);
            } else {
                // Esconder seção de transporte público se não for transit
                const transporteSection = document.getElementById('transporte-publico-section');
                if (transporteSection) {
                    transporteSection.style.display = 'none';
                }
            }
            
            // Mostrar resultado
            document.getElementById('resultado').style.display = 'block';
            
            // Atualizar mapa com informações básicas
            atualizarMapaSimples(origem, destino);
        }
        
        function mostrarOpcoesTransporteGratuito(origem, destino, tempoEstimado, distanciaEstimada) {
            // Criar ou mostrar seção de transporte público
            let transporteSection = document.getElementById('transporte-publico-section');
            if (!transporteSection) {
                transporteSection = document.createElement('div');
                transporteSection.id = 'transporte-publico-section';
                transporteSection.className = 'result-card mt-3';
                transporteSection.innerHTML = `
                    <h5 class="mb-3">
                        <i class="fas fa-bus me-2"></i>Opções de Transporte Público
                    </h5>
                    <div id="opcoes-transporte"></div>
                `;
                document.getElementById('resultado').parentNode.insertBefore(transporteSection, document.getElementById('resultado').nextSibling);
            }
            
            transporteSection.style.display = 'block';
            
            const opcoesContainer = document.getElementById('opcoes-transporte');
            opcoesContainer.innerHTML = '';
            
            // Gerar opções baseadas no tempo e distância calculados
            const opcoes = gerarOpcoesTransporteGratuito(origem, destino, tempoEstimado, distanciaEstimada);
            
            opcoes.forEach((opcao, index) => {
                const opcaoElement = document.createElement('div');
                opcaoElement.className = `opcao-transporte mb-3 p-3 border rounded ${index === 0 ? 'border-primary bg-light' : ''}`;
                opcaoElement.setAttribute('data-opcao', index);
                
                opcaoElement.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 ${index === 0 ? 'text-primary' : ''}">
                                <i class="fas fa-${opcao.icone} me-2"></i>
                                ${opcao.titulo}
                                ${index === 0 ? '<span class="badge bg-primary ms-2">Recomendada</span>' : ''}
                            </h6>
                            <small class="text-muted">${opcao.distancia} km • ${opcao.trocas} troca${opcao.trocas > 1 ? 's' : ''}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold ${index === 0 ? 'text-primary' : ''}">${opcao.tempo} min</div>
                            <small class="text-muted">Estimativa inteligente</small>
                        </div>
                    </div>
                    <div class="rota-detalhada">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <span class="small">${origem}</span>
                        </div>
                        ${opcao.etapas.map(etapa => `
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-${etapa.icone} text-primary me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${etapa.nome}</div>
                                    <div class="small text-muted">${etapa.descricao}</div>
                                </div>
                                <div class="text-muted small">${etapa.tempo} min</div>
                            </div>
                        `).join('')}
                        <div class="d-flex align-items-center">
                            <i class="fas fa-flag-checkered text-danger me-2"></i>
                            <span class="small">${destino}</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            ${opcao.observacoes}
                        </small>
                    </div>
                `;
                
                opcoesContainer.appendChild(opcaoElement);
            });
        }
        
        function gerarOpcoesTransporteGratuito(origem, destino, tempoEstimado, distanciaEstimada) {
            const opcoes = [];
            
            // Detectar se é uma cidade com metrô baseado em palavras-chave
            const temMetro = origem.toLowerCase().includes('são paulo') || 
                           origem.toLowerCase().includes('rio de janeiro') || 
                           origem.toLowerCase().includes('belo horizonte') || 
                           origem.toLowerCase().includes('recife') || 
                           origem.toLowerCase().includes('salvador') ||
                           destino.toLowerCase().includes('são paulo') || 
                           destino.toLowerCase().includes('rio de janeiro') || 
                           destino.toLowerCase().includes('belo horizonte') || 
                           destino.toLowerCase().includes('recife') || 
                           destino.toLowerCase().includes('salvador');
            
            // Opção 1: Metrô + Ônibus (se disponível)
            if (temMetro) {
                opcoes.push({
                    titulo: 'Metrô + Ônibus',
                    icone: 'subway',
                    tempo: tempoEstimado,
                    distancia: distanciaEstimada,
                    trocas: 1,
                    etapas: [
                        { icone: 'walking', nome: 'Caminhada', descricao: 'Origem → Estação mais próxima', tempo: Math.floor(tempoEstimado * 0.08) },
                        { icone: 'subway', nome: 'Linha 3 (Vermelha)', descricao: 'Estação A → Estação B', tempo: Math.floor(tempoEstimado * 0.4) },
                        { icone: 'bus', nome: 'Ônibus 107', descricao: 'Estação B → Próximo ao destino', tempo: Math.floor(tempoEstimado * 0.35) },
                        { icone: 'walking', nome: 'Caminhada', descricao: 'Destino final', tempo: Math.floor(tempoEstimado * 0.17) }
                    ],
                    observacoes: 'Melhor opção para horário de pico. Evite entre 7h-9h e 17h-19h.'
                });
            }
            
            // Opção 2: Apenas Ônibus
            opcoes.push({
                titulo: 'Apenas Ônibus',
                icone: 'bus',
                tempo: Math.floor(tempoEstimado * 1.2),
                distancia: Math.floor(distanciaEstimada * 1.1),
                trocas: 2,
                etapas: [
                    { icone: 'walking', nome: 'Caminhada', descricao: 'Origem → Ponto de ônibus', tempo: Math.floor(tempoEstimado * 0.1) },
                    { icone: 'bus', nome: 'Ônibus (linha sugerida)', descricao: 'Ponto A → Terminal Central', tempo: Math.floor(tempoEstimado * 0.4) },
                    { icone: 'bus', nome: 'Ônibus (linha sugerida)', descricao: 'Terminal → Próximo ao destino', tempo: Math.floor(tempoEstimado * 0.4) },
                    { icone: 'walking', nome: 'Caminhada', descricao: 'Destino final', tempo: Math.floor(tempoEstimado * 0.1) }
                ],
                observacoes: 'Estimativa genérica. Verifique a linha exata no Google Maps.'
            });
            
            // Opção 3: Ônibus Expresso
            opcoes.push({
                titulo: 'Ônibus Expresso',
                icone: 'bus',
                tempo: Math.floor(tempoEstimado * 1.1),
                distancia: Math.floor(distanciaEstimada * 1.2),
                trocas: 0,
                etapas: [
                    { icone: 'walking', nome: 'Caminhada', descricao: 'Origem → Terminal Expresso', tempo: Math.floor(tempoEstimado * 0.1) },
                    { icone: 'bus', nome: 'Ônibus expresso (linha sugerida)', descricao: 'Terminal → Próximo ao destino', tempo: Math.floor(tempoEstimado * 0.8) },
                    { icone: 'walking', nome: 'Caminhada', descricao: 'Destino final', tempo: Math.floor(tempoEstimado * 0.1) }
                ],
                observacoes: 'Estimativa genérica. Verifique a linha exata no Google Maps.'
            });
            
            // Opção 4: Metrô + Caminhada (se disponível)
            if (temMetro) {
                opcoes.push({
                    titulo: 'Metrô + Caminhada',
                    icone: 'subway',
                    tempo: Math.floor(tempoEstimado * 0.9),
                    distancia: Math.floor(distanciaEstimada * 0.8),
                    trocas: 0,
                    etapas: [
                        { icone: 'walking', nome: 'Caminhada', descricao: 'Origem → Estação mais próxima', tempo: Math.floor(tempoEstimado * 0.15) },
                        { icone: 'subway', nome: 'Linha de metrô (sugerida)', descricao: 'Estação A → Estação mais próxima', tempo: Math.floor(tempoEstimado * 0.6) },
                        { icone: 'walking', nome: 'Caminhada', descricao: 'Estação → Destino (15 min a pé)', tempo: 15 }
                    ],
                    observacoes: 'Estimativa genérica. Verifique a linha exata no Google Maps.'
                });
            }
            
            return opcoes;
        }
        
        // ===== OSM: Linhas reais de ônibus (sem API paga) =====
        function geocodeOsm(endereco) {
            const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&countrycodes=br&q=${encodeURIComponent(endereco)}`;
            return fetch(url, { headers: { 'Accept-Language': 'pt-BR' }})
                .then(r => r.json())
                .then(list => list && list[0] ? { lat: parseFloat(list[0].lat), lon: parseFloat(list[0].lon) } : null)
                .catch(() => null);
        }
        
        function fetchBusRoutesNear(lat, lon, radius = 700) {
            const query = `\n[out:json][timeout:25];\nnode(around:${radius},${lat},${lon})[highway=bus_stop]->.st;\nrel(route=bus)(bn.st);\nout tags id;`;
            return fetch('https://overpass-api.de/api/interpreter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: `data=${encodeURIComponent(query)}`
            })
            .then(r => r.json())
            .then(data => (data.elements || []).filter(el => el.type === 'relation'))
            .then(relations => relations.map(rel => ({
                id: rel.id,
                name: (rel.tags && (rel.tags.name || rel.tags.ref)) || 'Linha de ônibus',
                ref: rel.tags && rel.tags.ref ? rel.tags.ref : null,
                operator: rel.tags && rel.tags.operator ? rel.tags.operator : null,
                network: rel.tags && rel.tags.network ? rel.tags.network : null
            })))
            .catch(() => []);
        }
        
        function intersectRoutes(routesA, routesB) {
            const idsB = new Set(routesB.map(r => r.id));
            const mapB = new Map(routesB.map(r => [r.id, r]));
            const common = [];
            routesA.forEach(r => { if (idsB.has(r.id)) common.push(mapB.get(r.id) || r); });
            return common;
        }
        
        function sugerirLinhasOnibusComOSM(origem, destino) {
            // Criar/obter contêiner de linhas reais
            let transporteSection = document.getElementById('transporte-publico-section');
            if (!transporteSection) {
                transporteSection = document.createElement('div');
                transporteSection.id = 'transporte-publico-section';
                transporteSection.className = 'result-card mt-3';
                transporteSection.innerHTML = `
                    <h5 class="mb-3">
                        <i class="fas fa-bus me-2"></i>Opções de Transporte Público
                    </h5>
                    <div id="opcoes-transporte"></div>
                `;
                document.getElementById('resultado').parentNode.insertBefore(transporteSection, document.getElementById('resultado').nextSibling);
            }
            
            let linhasSection = document.getElementById('linhas-onibus-reais');
            if (!linhasSection) {
                linhasSection = document.createElement('div');
                linhasSection.id = 'linhas-onibus-reais';
                linhasSection.className = 'result-card mt-3';
                linhasSection.innerHTML = `
                    <h6 class="mb-2"><i class="fas fa-route me-2"></i>Linhas de ônibus sugeridas (OSM)</h6>
                    <div class="small text-muted mb-2">Dados comunitários OpenStreetMap, podem conter imprecisões. Confirme no Google Maps/empresa de transporte.</div>
                    <div id="lista-linhas-osm">
                        <div class="text-muted">Buscando linhas próximas...</div>
                    </div>
                `;
                transporteSection.parentNode.insertBefore(linhasSection, transporteSection.nextSibling);
            } else {
                linhasSection.querySelector('#lista-linhas-osm').innerHTML = '<div class="text-muted">Buscando linhas próximas...</div>';
            }
            
            Promise.all([geocodeOsm(origem), geocodeOsm(destino)]).then(([o, d]) => {
                if (!o || !d) {
                    linhasSection.querySelector('#lista-linhas-osm').innerHTML = '<div class="text-danger">Não foi possível localizar paradas próximas.</div>';
                    return;
                }
                return Promise.all([
                    fetchBusRoutesNear(o.lat, o.lon),
                    fetchBusRoutesNear(d.lat, d.lon)
                ]).then(([routesO, routesD]) => {
                    const comuns = intersectRoutes(routesO, routesD);
                    const lista = linhasSection.querySelector('#lista-linhas-osm');
                    lista.innerHTML = '';
                    if (!comuns.length) {
                        lista.innerHTML = '<div class="text-muted">Nenhuma linha direta encontrada. Tente baldeações indicadas no Google Maps.</div>';
                        return;
                    }
                    comuns.slice(0, 8).forEach(r => {
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2';
                        const title = r.ref ? `${r.ref} — ${r.name}` : r.name;
                        const subtitleParts = [r.operator, r.network].filter(Boolean);
                        item.innerHTML = `
                            <i class="fas fa-bus text-primary me-2"></i>
                            <div>
                                <div class="fw-bold">${title}</div>
                                ${subtitleParts.length ? `<div class="small text-muted">${subtitleParts.join(' • ')}</div>` : ''}
                            </div>
                        `;
                        lista.appendChild(item);
                    });
                });
            }).catch(() => {
                const lista = linhasSection.querySelector('#lista-linhas-osm');
                lista.innerHTML = '<div class="text-danger">Falha ao consultar linhas de ônibus.</div>';
            });
        }

        // Função removida - agora usa apenas dados reais da API
        
        // Função removida - agora usa apenas dados reais da API
        
        function selecionarOpcaoTransporte(index, opcao) {
            // Remover seleção anterior
            document.querySelectorAll('.opcao-transporte').forEach(el => {
                el.classList.remove('border-primary', 'bg-light');
                el.querySelector('h6').classList.remove('text-primary');
                el.querySelector('.fw-bold').classList.remove('text-primary');
            });
            
            // Selecionar nova opção
            const opcaoElement = document.querySelector(`[data-opcao="${index}"]`);
            opcaoElement.classList.add('border-primary', 'bg-light');
            opcaoElement.querySelector('h6').classList.add('text-primary');
            opcaoElement.querySelector('.fw-bold').classList.add('text-primary');
            
            // Mostrar detalhes da opção selecionada
            mostrarDetalhesOpcao(opcao);
        }
        
        function mostrarDetalhesOpcao(opcao) {
            // Criar ou atualizar seção de detalhes
            let detalhesSection = document.getElementById('detalhes-opcao');
            if (!detalhesSection) {
                detalhesSection = document.createElement('div');
                detalhesSection.id = 'detalhes-opcao';
                detalhesSection.className = 'result-card mt-3';
                document.getElementById('transporte-publico-section').parentNode.insertBefore(detalhesSection, document.getElementById('transporte-publico-section').nextSibling);
            }
            
            detalhesSection.innerHTML = `
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>Detalhes da Opção Selecionada
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Tempo Total: ${opcao.tempo} minutos</h6>
                        <h6>Trocas: ${opcao.trocas}</h6>
                        <h6>Tipo: ${opcao.tipo === 'metro' ? 'Metrô' : opcao.tipo === 'trem' ? 'Trem/VLT' : 'Ônibus'}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6>Dicas:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>${opcao.observacoes}</li>
                            <li><i class="fas fa-clock text-info me-2"></i>Considere atrasos de 5-10 minutos</li>
                            <li><i class="fas fa-money-bill text-success me-2"></i>Verifique tarifas e integração</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="abrirNoMaps()">
                        <i class="fas fa-external-link-alt me-2"></i>Abrir Rota no Google Maps
                    </button>
                    <button class="btn btn-outline-secondary ms-2" onclick="copiarRota()">
                        <i class="fas fa-copy me-2"></i>Copiar Rota
                    </button>
                </div>
            `;
            
            detalhesSection.style.display = 'block';
        }
        
        function copiarRota() {
            const origem = document.getElementById('origem').value;
            const destino = document.getElementById('destino').value;
            const opcaoSelecionada = document.querySelector('.opcao-transporte.border-primary');
            
            if (!opcaoSelecionada) {
                alert('Selecione uma opção de transporte primeiro.');
                return;
            }
            
            const titulo = opcaoSelecionada.querySelector('h6').textContent.replace('Mais Rápido', '').trim();
            const descricao = opcaoSelecionada.querySelector('small').textContent;
            const tempo = opcaoSelecionada.querySelector('.fw-bold').textContent;
            
            const rotaTexto = `Rota: ${origem} → ${destino}\n` +
                            `Opção: ${titulo}\n` +
                            `Descrição: ${descricao}\n` +
                            `Tempo: ${tempo}\n` +
                            `Gerado em: ${new Date().toLocaleString('pt-BR')}`;
            
            navigator.clipboard.writeText(rotaTexto).then(() => {
                showToast('Rota copiada para a área de transferência!', 'success');
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                showToast('Erro ao copiar rota', 'danger');
            });
        }
        
        function atualizarMapaSimples(origem, destino) {
            const mapContainer = document.getElementById('map');
            mapContainer.innerHTML = `
                <div style="
                    height: 100%;
                    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    border: 2px solid #2196f3;
                    position: relative;
                ">
                    <div style="text-align: center; color: #1976d2;">
                        <i class="fas fa-route" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h5>Rota Calculada</h5>
                        <p><strong>Origem:</strong> ${origem}</p>
                        <p><strong>Destino:</strong> ${destino}</p>
                        <button class="btn btn-primary mt-3" onclick="abrirNoMaps()">
                            <i class="fas fa-external-link-alt me-2"></i>Abrir no Google Maps
                        </button>
                    </div>
                </div>
            `;
        }
        
        
        function abrirNoMaps() {
            const origem = document.getElementById('origem').value;
            const destino = document.getElementById('destino').value;
            
            if (!origem || !destino) {
                alert('Por favor, preencha origem e destino primeiro.');
                return;
            }
            
            const meioTransporte = document.getElementById('meio_transporte').value;
            let url = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origem)}&destination=${encodeURIComponent(destino)}`;
            if (meioTransporte === 'transit') {
                url += `&travelmode=transit&transit_mode=bus`;
            } else if (meioTransporte === 'walking') {
                url += `&travelmode=walking`;
            } else if (meioTransporte === 'bicycling') {
                url += `&travelmode=bicycling`;
            } else {
                url += `&travelmode=driving`;
            }
            window.open(url, '_blank');
        }
    </script>
    
    <style>
        .selected {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }
        
        .selected .suggestion-company,
        .selected .suggestion-address {
            color: white !important;
        }
        
        .tip-item {
            display: flex;
            align-items: flex-start;
        }
        
        .tip-item i {
            margin-top: 0.2rem;
        }
        
        .opcao-transporte {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .opcao-transporte:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .opcao-transporte.border-primary {
            border-width: 2px !important;
        }
        
        .rota-detalhada {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
        }
        
        .rota-detalhada .d-flex {
            border-left: 3px solid #e9ecef;
            padding-left: 0.75rem;
            margin-bottom: 0.5rem;
        }
        
        .rota-detalhada .d-flex:last-child {
            margin-bottom: 0;
        }
        
        .rota-detalhada .d-flex:first-child {
            border-left-color: #28a745;
        }
        
        .rota-detalhada .d-flex:last-child {
            border-left-color: #dc3545;
        }
        
        .badge.bg-primary {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
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
</body>
</html>
