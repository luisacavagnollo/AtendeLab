<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Pessoas</h2>

<!-- Formulário de cadastro/edição -->
<div class="card mb-4">
    <div class="card-body">
        <h5 id="form-titulo">Nova Pessoa</h5>
        <form id="form-pessoa">
            <input type="hidden" name="id" id="pessoa-id">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="nome" id="pessoa-nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="documento" id="pessoa-documento" class="form-control" placeholder="Documento">
                </div>
                <div class="col-md-2">
                    <input type="text" name="telefone" id="pessoa-telefone" class="form-control" placeholder="Telefone">
                </div>
                <div class="col-md-2">
                    <input type="email" name="email" id="pessoa-email" class="form-control" placeholder="E-mail">
                </div>
                <div class="col-md-2">
                    <input type="text" name="curso" id="pessoa-curso" class="form-control" placeholder="Curso">
                </div>
                <div class="col-md-1">
                    <input type="text" name="periodo" id="pessoa-periodo" class="form-control" placeholder="Período">
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-10">
                    <input type="text" name="observacoes" id="pessoa-observacoes" class="form-control" placeholder="Observações">
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

<!-- Tabela de pessoas -->
<table class="table table-striped">
    <thead>
        <tr><th>Nome</th><th>Documento</th><th>Telefone</th><th>E-mail</th><th>Curso</th><th>Período</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody id="tabela-pessoas">
        <tr><td colspan="8" class="text-center">Carregando...</td></tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', () => {
    carregarPessoas();

    document.getElementById('form-pessoa').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('pessoa-id').value;
        const dados = {
            nome: document.getElementById('pessoa-nome').value,
            documento: document.getElementById('pessoa-documento').value,
            telefone: document.getElementById('pessoa-telefone').value,
            email: document.getElementById('pessoa-email').value,
            curso: document.getElementById('pessoa-curso').value,
            periodo: document.getElementById('pessoa-periodo').value,
            observacoes: document.getElementById('pessoa-observacoes').value
        };

        try {
            let resposta;
            if (id) {
                dados.id = id;
                resposta = await AtendeLabApi.post('pessoas', 'atualizar', dados);
            } else {
                resposta = await AtendeLabApi.post('pessoas', 'criar', dados);
            }
            mostrarMensagem(resposta.mensagem, 'success');
            cancelarEdicao();
            carregarPessoas();
        } catch (erro) {
            mostrarMensagem('Erro ao salvar pessoa.', 'danger');
        }
    });
});

async function carregarPessoas() {
    try {
        const resposta = await AtendeLabApi.get('pessoas', 'listar');
        const tbody = document.getElementById('tabela-pessoas');
        if (resposta.sucesso && resposta.dados.length > 0) {
            tbody.innerHTML = resposta.dados.map(p => `
                <tr>
                    <td>${esc(p.nome)}</td>
                    <td>${esc(p.documento)}</td>
                    <td>${esc(p.telefone)}</td>
                    <td>${esc(p.email || '')}</td>
                    <td>${esc(p.curso)}</td>
                    <td>${esc(p.periodo)}</td>
                    <td><span class="badge bg-${p.status === 'ativo' ? 'success' : 'secondary'}">${p.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editarPessoa(${p.id})">Editar</button>
                        ${p.status === 'ativo' ? `<button class="btn btn-sm btn-danger" onclick="inativarPessoa(${p.id})">Inativar</button>` : ''}
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Nenhuma pessoa cadastrada.</td></tr>';
        }
    } catch (erro) {
        console.error('Erro ao carregar pessoas:', erro);
    }
}

async function editarPessoa(id) {
    try {
        const resposta = await AtendeLabApi.get('pessoas', 'buscar', { id });
        if (resposta.sucesso) {
            const p = resposta.dados;
            document.getElementById('pessoa-id').value = p.id;
            document.getElementById('pessoa-nome').value = p.nome || '';
            document.getElementById('pessoa-documento').value = p.documento || '';
            document.getElementById('pessoa-telefone').value = p.telefone || '';
            document.getElementById('pessoa-email').value = p.email || '';
            document.getElementById('pessoa-curso').value = p.curso || '';
            document.getElementById('pessoa-periodo').value = p.periodo || '';
            document.getElementById('pessoa-observacoes').value = p.observacoes || '';
            document.getElementById('form-titulo').textContent = 'Editar Pessoa';
            document.getElementById('btn-salvar').textContent = 'Salvar';
            document.getElementById('btn-cancelar').classList.remove('d-none');
        }
    } catch (erro) {
        mostrarMensagem('Erro ao buscar pessoa.', 'danger');
    }
}

async function inativarPessoa(id) {
    if (!confirm('Deseja inativar esta pessoa?')) return;
    try {
        const resposta = await AtendeLabApi.post('pessoas', 'inativar', { id });
        mostrarMensagem(resposta.mensagem, 'success');
        carregarPessoas();
    } catch (erro) {
        mostrarMensagem('Erro ao inativar pessoa.', 'danger');
    }
}

function cancelarEdicao() {
    document.getElementById('form-pessoa').reset();
    document.getElementById('pessoa-id').value = '';
    document.getElementById('form-titulo').textContent = 'Nova Pessoa';
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
