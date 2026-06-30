<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Tipos de Atendimento</h2>

<!-- Formulário de cadastro/edição -->
<div class="card mb-4">
    <div class="card-body">
        <h5 id="form-titulo">Novo Tipo</h5>
        <form id="form-tipo">
            <input type="hidden" name="id" id="tipo-id">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="nome" id="tipo-nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="descricao" id="tipo-descricao" class="form-control" placeholder="Descrição">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" id="btn-salvar">Cadastrar</button>
                </div>
            </div>
        </form>
        <button class="btn btn-sm btn-outline-secondary mt-2 d-none" id="btn-cancelar" onclick="cancelarEdicao()">Cancelar edição</button>
    </div>
</div>

<!-- Mensagem -->
<div id="mensagem" class="alert d-none"></div>

<!-- Tabela de tipos -->
<table class="table table-striped">
    <thead>
        <tr><th>Nome</th><th>Descrição</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody id="tabela-tipos">
        <tr><td colspan="4" class="text-center">Carregando...</td></tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', () => {
    carregarTipos();

    document.getElementById('form-tipo').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('tipo-id').value;
        const dados = {
            nome: document.getElementById('tipo-nome').value,
            descricao: document.getElementById('tipo-descricao').value
        };

        try {
            let resposta;
            if (id) {
                dados.id = id;
                resposta = await AtendeLabApi.post('tipos', 'atualizar', dados);
            } else {
                resposta = await AtendeLabApi.post('tipos', 'criar', dados);
            }
            mostrarMensagem(resposta.mensagem, 'success');
            cancelarEdicao();
            carregarTipos();
        } catch (erro) {
            mostrarMensagem('Erro ao salvar tipo.', 'danger');
        }
    });
});

async function carregarTipos() {
    try {
        const resposta = await AtendeLabApi.get('tipos', 'listar');
        const tbody = document.getElementById('tabela-tipos');
        if (resposta.sucesso && resposta.dados.length > 0) {
            tbody.innerHTML = resposta.dados.map(t => `
                <tr>
                    <td>${esc(t.nome)}</td>
                    <td>${esc(t.descricao)}</td>
                    <td><span class="badge bg-${t.status === 'ativo' ? 'success' : 'secondary'}">${t.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editarTipo(${t.id})">Editar</button>
                        ${t.status === 'ativo' ? `<button class="btn btn-sm btn-danger" onclick="inativarTipo(${t.id})">Inativar</button>` : ''}
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhum tipo cadastrado.</td></tr>';
        }
    } catch (erro) {
        console.error('Erro ao carregar tipos:', erro);
    }
}

async function editarTipo(id) {
    try {
        const resposta = await AtendeLabApi.get('tipos', 'buscar', { id });
        if (resposta.sucesso) {
            const t = resposta.dados;
            document.getElementById('tipo-id').value = t.id;
            document.getElementById('tipo-nome').value = t.nome || '';
            document.getElementById('tipo-descricao').value = t.descricao || '';
            document.getElementById('form-titulo').textContent = 'Editar Tipo';
            document.getElementById('btn-salvar').textContent = 'Salvar';
            document.getElementById('btn-cancelar').classList.remove('d-none');
        }
    } catch (erro) {
        mostrarMensagem('Erro ao buscar tipo.', 'danger');
    }
}

async function inativarTipo(id) {
    if (!confirm('Deseja inativar este tipo de atendimento?')) return;
    try {
        const resposta = await AtendeLabApi.post('tipos', 'inativar', { id });
        mostrarMensagem(resposta.mensagem, 'success');
        carregarTipos();
    } catch (erro) {
        mostrarMensagem('Erro ao inativar tipo.', 'danger');
    }
}

function cancelarEdicao() {
    document.getElementById('form-tipo').reset();
    document.getElementById('tipo-id').value = '';
    document.getElementById('form-titulo').textContent = 'Novo Tipo';
    document.getElementById('btn-salvar').textContent = 'Cadastrar';
    document.getElementById('btn-cancelar').classList.add('d-none');
}

function mostrarMensagem(msg, tipo) {
    const el = document.getElementById('mensagem');
    el.className = `alert alert-${tipo}`;
    el.textContent = msg;
    el.classList.remove('d-none');
    setTimeout(() => el.classList.add('d-none'), 4000);
}

function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
