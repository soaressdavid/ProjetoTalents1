# 🚀 TalentsHUB

**Plataforma de Recrutamento e Seleção**

Uma plataforma completa de RH inspirada no Gupy, Indeed e Glassdoor, desenvolvida em PHP com MySQL.

## ✨ Características

### 🎯 Para Candidatos
- **Cadastro Completo**: Perfil detalhado com experiência, formação e habilidades
- **Busca de Vagas**: Sistema avançado de busca e filtros
- **Candidaturas**: Aplicação fácil com upload de currículo
- **Favoritos**: Salvar vagas de interesse
- **Acompanhamento**: Status das candidaturas em tempo real

### 🏢 Para Empresas
- **Cadastro Empresarial**: Perfil completo com CNPJ e informações corporativas
- **Publicação de Vagas**: Criação de vagas com requisitos detalhados
- **Gestão de Candidatos**: Visualização e análise de candidaturas
- **Relatórios**: Estatísticas de visualizações e candidaturas
- **Branding**: Página da empresa personalizada

### 👨‍💼 Para Administradores
- **Painel Administrativo**: Controle total da plataforma
- **Gestão de Usuários**: Moderação de contas
- **Relatórios Gerais**: Estatísticas da plataforma
- **Configurações**: Personalização do sistema

## 🛠️ Tecnologias

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript
- **Segurança**: PDO, Prepared Statements, Password Hashing
- **Arquitetura**: MVC (Model-View-Controller)

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 8.0 ou superior
- Apache/Nginx
- XAMPP (recomendado para desenvolvimento)

## 🚀 Instalação

### 1. Clone o Repositório
```bash
git clone https://github.com/seu-usuario/talentshub.git
cd talentshub
```

### 2. Configure o Banco de Dados
```bash
# Importe o schema do banco
mysql -u root -p < database/schema.sql
```

### 3. Configure as Credenciais
Edite o arquivo `config/config.php`:
```php
$servidor = "localhost";
$usuario = "seu_usuario";
$senha = "sua_senha";
$banco = "talentshub_db";
```

### 4. Configure o Servidor Web
- Coloque os arquivos na pasta do seu servidor web
- Configure o DocumentRoot para apontar para a pasta do projeto
- Certifique-se de que o mod_rewrite está habilitado

## 📊 Estrutura do Banco de Dados

### Tabelas Principais
- **usuarios**: Dados básicos de todos os usuários
- **candidatos**: Informações específicas de candidatos
- **empresas**: Dados corporativos das empresas
- **vagas**: Ofertas de emprego
- **candidaturas**: Aplicações dos candidatos
- **experiencias**: Histórico profissional
- **formacoes**: Formação acadêmica
- **habilidades**: Competências técnicas

## 🔐 Segurança

- **Validação de Entrada**: Sanitização de todos os dados
- **Prepared Statements**: Prevenção de SQL Injection
- **Password Hashing**: Senhas criptografadas com bcrypt
- **Validação de Email**: Verificação de formato e duplicatas
- **Validação de CNPJ**: Verificação de CNPJ único para empresas
- **Logs de Sistema**: Rastreamento de atividades

## 📱 Funcionalidades

### Sistema de Cadastro
- ✅ Validação de email único
- ✅ Validação de CNPJ para empresas
- ✅ Validação de força da senha
- ✅ Campos específicos por tipo de usuário
- ✅ Sanitização de dados

### Sistema de Vagas
- ✅ Criação de vagas com requisitos detalhados
- ✅ Filtros por área, modalidade, experiência
- ✅ Sistema de favoritos
- ✅ Controle de visualizações

### Sistema de Candidaturas
- ✅ Upload de currículo
- ✅ Carta de apresentação
- ✅ Status de acompanhamento
- ✅ Notificações de mudança de status

## 🎨 Interface

- **Design Responsivo**: Funciona em desktop, tablet e mobile
- **UX Moderna**: Interface intuitiva e profissional
- **Acessibilidade**: Seguindo padrões de acessibilidade web
- **Performance**: Otimizado para carregamento rápido

## 📈 Roadmap

### Versão 1.1
- [ ] Sistema de notificações por email
- [ ] Chat entre candidatos e recrutadores
- [ ] Testes online integrados
- [ ] API REST para integrações

### Versão 1.2
- [ ] Sistema de avaliações de empresas
- [ ] Relatórios avançados
- [ ] Integração com LinkedIn
- [ ] App mobile

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

- **Email**: suporte@talentshub.com
- **Documentação**: [docs.talentshub.com](https://docs.talentshub.com)
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/talentshub/issues)

## 👥 Equipe

- **Desenvolvedor Principal**: [Seu Nome]
- **Designer UX/UI**: [Nome do Designer]
- **Analista de Sistemas**: [Nome do Analista]

---

**TalentsHUB** - Conectando talentos às melhores oportunidades! 🎯

