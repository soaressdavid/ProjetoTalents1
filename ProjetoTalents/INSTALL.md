# 🚀 Guia de Instalação - TalentsHUB

## 📋 Pré-requisitos

- **PHP 7.4+** (recomendado PHP 8.0+)
- **MySQL 8.0+** ou **MariaDB 10.3+**
- **Apache** ou **Nginx**
- **XAMPP** (recomendado para desenvolvimento)
- **Composer** (opcional, para futuras dependências)

## 🛠️ Instalação Passo a Passo

### 1. Download e Configuração

```bash
# Clone o repositório (se usando Git)
git clone https://github.com/seu-usuario/talentshub.git
cd talentshub

# Ou baixe e extraia o arquivo ZIP
# Coloque os arquivos na pasta do seu servidor web
```

### 2. Configuração do Banco de Dados

#### 2.1 Criar o Banco de Dados

```sql
-- Conecte-se ao MySQL como root
mysql -u root -p

-- Crie o banco de dados
CREATE DATABASE talentshub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crie um usuário específico (recomendado)
CREATE USER 'talentshub_user'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON talentshub_db.* TO 'talentshub_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 2.2 Importar o Schema

```bash
# Importe o schema do banco
mysql -u root -p talentshub_db < database/schema.sql

# Ou usando o usuário específico
mysql -u talentshub_user -p talentshub_db < database/schema.sql
```

### 3. Configuração da Aplicação

#### 3.1 Configurar Credenciais do Banco

Edite o arquivo `config/config.php`:

```php
<?php
$servidor = "localhost";
$usuario = "talentshub_user";  // ou "root" para desenvolvimento
$senha = "sua_senha_segura";   // sua senha do banco
$banco = "talentshub_db";
?>
```

#### 3.2 Configurar Permissões de Arquivo

```bash
# Linux/Mac
chmod 755 public/uploads/
chmod 755 public/uploads/curriculos/
chmod 644 config/config.php

# Windows (se necessário)
# Certifique-se de que o IIS/Apache tem permissão de escrita na pasta uploads
```

### 4. Configuração do Servidor Web

#### 4.1 Apache (.htaccess)

Crie um arquivo `.htaccess` na raiz do projeto:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Segurança
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

#### 4.2 Nginx

Adicione ao seu arquivo de configuração do Nginx:

```nginx
server {
    listen 80;
    server_name talentshub.local;
    root /caminho/para/talentshub;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 5. Configuração do XAMPP (Desenvolvimento)

#### 5.1 Instalar XAMPP

1. Baixe o XAMPP do site oficial
2. Instale em `C:\xampp\` (Windows) ou `/opt/lampp/` (Linux)
3. Inicie o Apache e MySQL

#### 5.2 Configurar Projeto

1. Copie a pasta do projeto para `C:\xampp\htdocs\talentshub\`
2. Acesse `http://localhost/talentshub/`
3. Configure o banco conforme instruções acima

### 6. Verificação da Instalação

#### 6.1 Teste de Conectividade

Acesse: `http://seu-dominio.com/` ou `http://localhost/talentshub/`

Você deve ver a página inicial do TalentsHUB.

#### 6.2 Teste de Cadastro

1. Clique em "Cadastrar-se"
2. Crie uma conta de candidato
3. Crie uma conta de empresa
4. Teste o login

#### 6.3 Verificar Logs

```bash
# Verificar logs do PHP
tail -f /var/log/php_errors.log

# Verificar logs do Apache
tail -f /var/log/apache2/error.log

# Verificar logs do MySQL
tail -f /var/log/mysql/error.log
```

## 🔧 Configurações Avançadas

### 1. Configuração de Email (Produção)

Para notificações por email, configure no `config/config.php`:

```php
// Configurações de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu-email@gmail.com');
define('SMTP_PASSWORD', 'sua-senha-app');
define('SMTP_ENCRYPTION', 'tls');
```

### 2. Configuração de Upload

No `config/config.php`:

```php
// Configurações de upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx']);
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
```

### 3. Configuração de Cache

Para melhor performance em produção:

```php
// Configurações de cache
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../cache/');
define('CACHE_LIFETIME', 3600); // 1 hora
```

## 🚀 Deploy em Produção

### 1. Preparação do Servidor

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install apache2 mysql-server php8.0 php8.0-mysql php8.0-curl php8.0-gd php8.0-mbstring php8.0-xml php8.0-zip

# Configurar Apache
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 2. Configuração de Segurança

```bash
# Configurar firewall
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Configurar SSL (Let's Encrypt)
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d seu-dominio.com
```

### 3. Otimizações de Performance

```apache
# .htaccess para produção
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## 🐛 Solução de Problemas

### Problema: Erro de Conexão com Banco

**Solução:**
1. Verifique se o MySQL está rodando
2. Confirme as credenciais no `config/config.php`
3. Teste a conexão manualmente

### Problema: Página em Branco

**Solução:**
1. Ative o display_errors no PHP
2. Verifique os logs de erro
3. Confirme se todas as dependências estão instaladas

### Problema: Upload de Arquivos Não Funciona

**Solução:**
1. Verifique as permissões da pasta `public/uploads/`
2. Confirme o `upload_max_filesize` no PHP
3. Verifique se a pasta existe

### Problema: URLs Não Funcionam

**Solução:**
1. Confirme se o mod_rewrite está ativo
2. Verifique o arquivo `.htaccess`
3. Confirme a configuração do DocumentRoot

## 📞 Suporte

- **Email**: suporte@talentshub.com
- **Documentação**: [docs.talentshub.com](https://docs.talentshub.com)
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/talentshub/issues)

## 🎉 Parabéns!

Se você chegou até aqui, o TalentsHUB está instalado e funcionando! 

Agora você pode:
- ✅ Cadastrar usuários
- ✅ Publicar vagas
- ✅ Gerenciar candidaturas
- ✅ Usar todas as funcionalidades

**Bem-vindo ao TalentsHUB!** 🚀

