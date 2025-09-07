// Arquivo: public/js/auth.js - TalentsHUB

document.addEventListener('DOMContentLoaded', () => {
    console.log('JavaScript carregado - DOM pronto');
    
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    console.log('Elementos encontrados:', {
        signUpButton: !!signUpButton,
        signInButton: !!signInButton,
        container: !!container
    });

    if (signUpButton && signInButton && container) {
        console.log('Adicionando event listeners...');
        
        signUpButton.addEventListener('click', (e) => {
            console.log('Botão Cadastrar clicado');
            e.preventDefault();
            container.classList.add("right-panel-active");
            console.log('Classe right-panel-active adicionada');
        });

        signInButton.addEventListener('click', (e) => {
            console.log('Botão Entrar clicado');
            e.preventDefault();
            container.classList.remove("right-panel-active");
            console.log('Classe right-panel-active removida');
        });
        
        console.log('Event listeners adicionados com sucesso');
    } else {
        console.error('Elementos não encontrados:', {
            signUpButton: signUpButton,
            signInButton: signInButton,
            container: container
        });
    }

    // Adicionar animações suaves aos inputs
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
        });
    });

    // Debug: Verificar se os formulários estão funcionando
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            console.log('Formulário enviado:', form.action);
            // Não prevenir o envio padrão - deixar o formulário ser enviado normalmente
        });
    });

    // Debug: Verificar se os botões de submit estão funcionando
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            console.log('Botão de submit clicado:', button.textContent);
            // Não prevenir o clique padrão
        });
    });

    // Mostrar/ocultar campos específicos baseado no tipo de usuário
    const tipoUsuarioSelect = document.getElementById('tipoUsuario');
    const camposCandidato = document.getElementById('camposCandidato');
    const camposEmpresa = document.getElementById('camposEmpresa');

    if (tipoUsuarioSelect && camposCandidato && camposEmpresa) {
        tipoUsuarioSelect.addEventListener('change', (e) => {
            const tipo = e.target.value;
            
            // Ocultar todos os campos específicos
            camposCandidato.style.display = 'none';
            camposEmpresa.style.display = 'none';
            
            // Mostrar campos específicos baseado na seleção
            if (tipo === 'candidato') {
                camposCandidato.style.display = 'block';
            } else if (tipo === 'empresa') {
                camposEmpresa.style.display = 'block';
            }
        });
    }

    // Validação do formulário de cadastro (simplificada)
    const cadastroForm = document.getElementById('cadastroForm');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', (e) => {
            console.log('Formulário de cadastro sendo enviado...');
            
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const nome = document.querySelector('input[name="nome"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const senha = document.querySelector('input[name="senha"]').value;
            
            console.log('Dados do formulário:', { nome, email, senha: senha.length + ' caracteres', tipoUsuario });
            
            // Validações básicas (sem prevenir o envio por enquanto)
            if (!nome || !email || !senha || !tipoUsuario) {
                console.log('Campos obrigatórios não preenchidos');
                // e.preventDefault();
                // alert('Por favor, preencha todos os campos obrigatórios.');
                // return false;
            }
            
            // Validação de senha
            if (senha.length < 8) {
                console.log('Senha muito curta');
                // e.preventDefault();
                // alert('A senha deve ter pelo menos 8 caracteres.');
                // return false;
            }
            
            console.log('Formulário de cadastro validado e enviado');
        });
    }
});