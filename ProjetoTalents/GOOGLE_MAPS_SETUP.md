# Configuração da API do Google Maps

## 🗺️ Calculadora de Deslocamento - Configuração

Para que a funcionalidade de **Calculadora de Deslocamento** funcione corretamente, você precisa configurar a API do Google Maps.

### 📋 Pré-requisitos

1. Conta no Google Cloud Platform
2. Projeto criado no Google Cloud Console
3. APIs habilitadas
4. Chave de API gerada

### 🔧 Passo a Passo

#### 1. Acesse o Google Cloud Console
- Vá para: https://console.cloud.google.com/
- Faça login com sua conta Google

#### 2. Crie um Projeto (se não tiver)
- Clique em "Selecionar um projeto"
- Clique em "Novo projeto"
- Digite um nome para o projeto (ex: "TalentsHUB Maps")
- Clique em "Criar"

#### 3. Habilite as APIs Necessárias
No menu lateral, vá em "APIs e serviços" > "Biblioteca" e habilite:

- **Maps JavaScript API** - Para exibir mapas
- **Directions API** - Para calcular rotas
- **Places API** - Para autocomplete de endereços
- **Geocoding API** - Para converter endereços em coordenadas

#### 4. Crie uma Chave de API
- Vá em "APIs e serviços" > "Credenciais"
- Clique em "Criar credenciais" > "Chave de API"
- Copie a chave gerada

#### 5. Configure Restrições (Recomendado)
- Clique na chave criada para editá-la
- Em "Restrições de aplicativo":
  - Selecione "Sites da Web (referenciadores HTTP)"
  - Adicione seus domínios:
    - `http://localhost/*`
    - `https://seu-dominio.com/*`
- Em "Restrições de API":
  - Selecione "Restringir chave"
  - Selecione apenas as APIs que você habilitou

#### 6. Configure no Projeto
1. Abra o arquivo: `config/google_maps.php`
2. Substitua `YOUR_API_KEY` pela sua chave real:
   ```php
   define('GOOGLE_MAPS_API_KEY', 'SUA_CHAVE_AQUI');
   ```

### 💰 Custos

**IMPORTANTE**: A API do Google Maps tem custos após o limite gratuito:

- **Maps JavaScript API**: $7 por 1.000 carregamentos
- **Directions API**: $5 por 1.000 requisições
- **Places API**: $17 por 1.000 requisições
- **Geocoding API**: $5 por 1.000 requisições

**Limite gratuito mensal**:
- Maps JavaScript API: 28.000 carregamentos
- Directions API: 2.500 requisições
- Places API: 1.000 requisições
- Geocoding API: 40.000 requisições

### 🔒 Segurança

1. **Nunca exponha sua chave** em repositórios públicos
2. **Configure restrições** de domínio
3. **Monitore o uso** no Google Cloud Console
4. **Configure alertas** de cobrança

### 🧪 Testando

Após configurar:

1. Acesse: `app/views/calculadora_deslocamento.php`
2. Digite um endereço de origem
3. Digite um endereço de destino
4. Clique em "Calcular Deslocamento"

Se tudo estiver funcionando, você verá:
- ✅ Mapa carregado
- ✅ Rota desenhada
- ✅ Tempo de viagem calculado
- ✅ Distância mostrada

### 🚨 Solução de Problemas

#### Erro: "This page can't load Google Maps correctly"
- Verifique se a chave está correta
- Verifique se as APIs estão habilitadas
- Verifique as restrições de domínio

#### Erro: "REQUEST_DENIED"
- Verifique se a API está habilitada
- Verifique se a chave tem permissões
- Verifique as restrições de API

#### Mapa não carrega
- Verifique o console do navegador (F12)
- Verifique se há erros de JavaScript
- Verifique a conexão com a internet

### 📞 Suporte

Se precisar de ajuda:
1. Consulte a documentação oficial: https://developers.google.com/maps
2. Verifique o console do navegador para erros
3. Teste com uma chave de API simples primeiro

---

**Nota**: Esta funcionalidade é opcional. O sistema funcionará normalmente sem ela, apenas a calculadora de deslocamento não estará disponível.
