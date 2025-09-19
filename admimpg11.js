// Variáveis globais
const modal = document.getElementById('modal-editar');
const formEditar = document.getElementById('form-editar-vaga');
const btnFecharModal = document.getElementById('fechar-modal');
let vagaAtualParaEditar = null; // Guarda a vaga que está sendo editada

// Função para criar a vaga no DOM
function criarVaga() {
    const enterpriseName = getInputValue("enterprise");
    const positionName = getInputValue("position");
    const description = getInputValue("description");
    const paiment = getInputValue("paiment");

    const novaUl = criarElementoVaga(enterpriseName, positionName, description, paiment);
    adicionarVagaNoContainer(novaUl);
    resetarFormulario('criar-vaga');
}

// Função para pegar o valor de um input pelo id
function getInputValue(id) {
    return document.getElementById(id).value;
}

// Função para criar o elemento UL com as informações da vaga e botões
function criarElementoVaga(enterpriseName, positionName, description, paiment) {
    const novaUl = document.createElement("ul");
    novaUl.classList.add("vaga");

    const liEnterprise = criarLi(enterpriseName);
    const liPosition = criarLi(positionName);
    const liDescription = criarLi(description);
    const liPaiment = criarLi(paiment);
    const liButtons = document.createElement("li");

    const editarBtn = criarBotao("Editar", "editar", () => abrirModalEdicao(novaUl));
    const excluirBtn = criarBotao("Excluir", "excluir", () => novaUl.remove());

    liButtons.appendChild(editarBtn);
    liButtons.appendChild(excluirBtn);

    novaUl.appendChild(liEnterprise);
    novaUl.appendChild(liPosition);
    novaUl.appendChild(liDescription);
    novaUl.appendChild(liPaiment);
    novaUl.appendChild(liButtons);

    return novaUl;
}

// Função para criar um <li> com texto
function criarLi(texto) {
    const li = document.createElement("li");
    li.textContent = texto;
    return li;
}

// Função para criar um botão com texto, classe e ação no click
function criarBotao(texto, classe, onClick) {
    const btn = document.createElement("button");
    btn.textContent = texto;
    btn.classList.add(classe);
    btn.addEventListener("click", onClick);
    return btn;
}

// Função para adicionar a vaga criada no container
function adicionarVagaNoContainer(elemento) {
    const container = document.getElementById("container-de-vagas");
    container.appendChild(elemento);
}

// Função para resetar formulário pelo id
function resetarFormulario(id) {
    document.getElementById(id).reset();
}

// Função para abrir o modal preenchendo os dados da vaga que será editada
function abrirModalEdicao(vagaElement) {
    vagaAtualParaEditar = vagaElement;

    const lis = vagaElement.querySelectorAll("li");

    document.getElementById("edit-enterprise").value = lis[0].textContent;
    document.getElementById("edit-position").value = lis[1].textContent;
    document.getElementById("edit-description").value = lis[2].textContent;
    document.getElementById("edit-paiment").value = lis[3].textContent;

    modal.style.display = "flex";
}

// Função para fechar o modal
function fecharModal() {
    modal.style.display = "none";
}

// Função para salvar as alterações feitas no modal
function salvarEdicao(event) {
    event.preventDefault();

    if (!vagaAtualParaEditar) return;

    const lis = vagaAtualParaEditar.querySelectorAll('li');

    lis[0].textContent = getInputValue("edit-enterprise");
    lis[1].textContent = getInputValue("edit-position");
    lis[2].textContent = getInputValue("edit-description");
    lis[3].textContent = getInputValue("edit-paiment");

    fecharModal();
}


btnFecharModal.addEventListener('click', fecharModal);

formEditar.addEventListener('submit', salvarEdicao);

// Modal de criação de vaga
const modalCriar = document.getElementById('modal-criar');
const btnAbrirModalCriacao = document.getElementById('abrir-modal-criacao');
const btnFecharModalCriacao = document.getElementById('fechar-modal-criacao');

btnAbrirModalCriacao.addEventListener('click', () => {
    modalCriar.style.display = 'flex';
});

btnFecharModalCriacao.addEventListener('click', () => {
    modalCriar.style.display = 'none';
});

// Fechar modal ao criar vaga
document.getElementById('criar-vaga').addEventListener('submit', function(event) {
    event.preventDefault();
    criarVaga();
    modalCriar.style.display = 'none'; // fecha o modal
});
