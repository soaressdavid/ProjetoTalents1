# 🗺️ Como Configurar a API do Google Maps

## 📋 Pré-requisitos
- Conta no Google (Gmail)
- Cartão de crédito (para verificação, mas há crédito gratuito)
- Acesso ao Google Cloud Console

## 🚀 Passo a Passo

### 1. Acesse o Google Cloud Console
1. Vá para: https://console.cloud.google.com/
2. Faça login com sua conta Google
3. Aceite os termos de serviço

### 2. Crie ou Selecione um Projeto
1. No canto superior esquerdo, clique no nome do projeto
2. Clique em "NOVO PROJETO"
3. Digite um nome: `ProjetoTalents-Maps`
4. Clique em "CRIAR"

### 3. Habilite as APIs Necessárias
1. No menu lateral, vá em "APIs e Serviços" > "Biblioteca"
2. Procure e habilite cada uma das APIs:

#### 3.1. Directions API
- Procure por "Directions API"
- Clique em "ATIVAR"
- Aguarde a ativação

#### 3.2. Places API
- Procure por "Places API"
- Clique em "ATIVAR"
- Aguarde a ativação

#### 3.3. Geocoding API
- Procure por "Geocoding API"
- Clique em "ATIVAR"
- Aguarde a ativação

#### 3.4. Maps JavaScript API (opcional)
- Procure por "Maps JavaScript API"
- Clique em "ATIVAR"
- Aguarde a ativação

### 4. Crie uma Chave de API
1. Vá em "APIs e Serviços" > "Credenciais"
2. Clique em "CRIAR CREDENCIAIS" > "Chave de API"
3. Copie a chave gerada (ex: `AIzaSyB...`)

### 5. Configure Restrições (Recomendado)
1. Clique na chave criada para editá-la
2. Em "Restrições de aplicativo", selecione "Sites HTTP"
3. Adicione os domínios:
   - `localhost`
   - `127.0.0.1`
   - Seu domínio de produção
4. Em "Restrições de API", selecione "Restringir chave"
5. Selecione as APIs que você habilitou
6. Clique em "SALVAR"

### 6. Configure no Projeto
1. Abra o arquivo `config/google_maps.php`
2. Substitua `YOUR_API_KEY` pela sua chave real:

```php
define('GOOGLE_MAPS_API_KEY', 'AIzaSyB...sua_chave_aqui');
```

### 7. Teste a Configuração
1. Acesse a calculadora de deslocamento
2. Digite endereços reais
3. Selecione "Transporte Público"
4. Clique em "Calcular Deslocamento"
5. Deve aparecer dados reais do Google Maps

## 💰 Custos e Limites

### Crédito Gratuito
- **$200 USD** de crédito gratuito por mês
- **28.000 solicitações** de Directions API
- **40.000 solicitações** de Geocoding API
- **40.000 solicitações** de Places API

### Preços (após crédito gratuito)
- **Directions API**: $5,00 por 1.000 solicitações
- **Geocoding API**: $5,00 por 1.000 solicitações
- **Places API**: $17,00 por 1.000 solicitações

## 🔧 Solução de Problemas

### Erro: "API key not valid"
- Verifique se a chave está correta
- Verifique se as APIs estão habilitadas
- Verifique as restrições de domínio

### Erro: "This API project is not authorized"
- Verifique se as APIs estão habilitadas no projeto correto
- Aguarde alguns minutos para propagação

### Erro: "Quota exceeded"
- Verifique se não excedeu o limite gratuito
- Aguarde até o próximo mês ou adicione pagamento

### Dados não aparecem
- Verifique o console do navegador (F12)
- Verifique se a chave está configurada corretamente
- Teste com endereços conhecidos

## 📱 Exemplo de Teste

**Endereços para testar:**
- **Origem**: Avenida Paulista, 1000, Bela Vista, São Paulo - SP
- **Destino**: Estação Vila Madalena, São Paulo - SP
- **Meio**: Transporte Público
- **Horário**: 08:00

**Resultado esperado:**
- Tempo real (ex: 45 minutos)
- Distância real (ex: 12 km)
- Linhas reais de ônibus/metrô
- Horários de partida/chegada precisos

## 🛡️ Segurança

### Boas Práticas
1. **Nunca** commite a chave no Git
2. Use restrições de domínio
3. Use restrições de API
4. Monitore o uso no console
5. Configure alertas de cota

### Arquivo .gitignore
Adicione ao `.gitignore`:
```
config/google_maps.php
```

## ✅ Verificação Final

Após configurar, você deve ver:
- ✅ Dados reais do Google Maps
- ✅ Linhas corretas de ônibus/metrô
- ✅ Tempos e distâncias precisos
- ✅ Horários reais de partida/chegada
- ✅ Múltiplas opções de rota

## 🆘 Suporte

Se tiver problemas:
1. Verifique o console do navegador (F12)
2. Verifique se a chave está correta
3. Verifique se as APIs estão habilitadas
4. Teste com endereços simples primeiro

---

**🎉 Pronto! Agora você terá dados reais e precisos na calculadora de deslocamento!**