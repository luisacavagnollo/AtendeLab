<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Usuários</h2>

<!-- Formulário de cadastro/edição -->
<div class="card mb-4">
    <div class="card-body">
        <h5 id="form-titulo">Novo Usuário</h5>
        <form id="form-usuario">
            <input type="hidden" name="id" id="usuario-id">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="nome" id="usuario-nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" id="usuario-email" class="form-control" placeholder="E-mail" required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="senha" id="usuario-senha" class="form-control" placeholder="Senha">
                </div>
                <div class="col-md-2">
                    <select name="perfil" id="usuario-perfil" class="form-select">
                        <option value="atendente">Atendente</option>
                        <option value="admin">Admin</option>
                    </select>
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

<!-- Tabela de usuários -->
<table class="table table-striped">
    <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Criado em</th><th>Ações</th></tr>
    </thead>
    <tbody id="tabela-usuarios">
        <tr><td colspan="6" class="text-center">Carregando...</td></tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', () => {
    carregarUsuarios();

    document.getElementById('form-usuario').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('usuario-id').value;
        const dados = {
            nome: document.getElementById('usuario-nome').value,
            email: document.getElementById('usuario-email').value,
            perfil: document.getElementById('usuario-perfil').value
        };

        try {
            let resposta;
            if (id) {
                dados.id = id;
                resposta = await AtendeLabApi.post('usuarios', 'atualizar', dados);
            } else {
                dados.senha = document.getElementById('usuario-senha').value;
                if (!dados.senha) {
                    mostrarMensagem('Informe uma senha para o novo usuário.', 'danger');
                    return;
                }
                resposta = await AtendeLabApi.post('usuarios', 'criar', dados);
            }
            mostrarMensagem(resposta.mensagem, 'success');
            cancelarEdicao();
            carregarUsuarios();
        } catch (erro) {
            mostrarMensagem('Erro ao salvar usuário.', 'danger');
        }
    });
});

async function carregarUsuarios() {
    try {
        const resposta = await AtendeLabApi.get('usuarios', 'listar');
        const tbody = document.getElementById('tabela-usuarios');
        if (resposta.sucesso && resposta.dados.length > 0) {
            tbody.innerHTML = resposta.dados.map(u => `
                <tr>
                    <td>${esc(u.nome)}</td>
                    <td>${esc(u.email)}</td>
                    <td><span class="badge bg-${u.perfil === 'admin' ? 'primary' : 'secondary'}">${u.perfil}</span></td>
                    <td><span class="badge bg-${u.status === 'ativo' ? 'success' : 'secondary'}">${u.status}</span></td>
                    <td>${u.criado_em || '—'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editarUsuario(${u.id})">Editar</button>
                        ${u.status === 'ativo' ? `<button class="btn btn-sm btn-danger" onclick="inativarUsuario(${u.id})">Inativar</button>` : ''}
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhum usuário cadastrado.</td></tr>';
        }
    } catch (erro) {
        console.error('Erro ao carregar usuários:', erro);
    }
}

async function editarUsuario(id) {
    try {
        const resposta = await AtendeLabApi.get('usuarios', 'buscar', { id });
        if (resposta.sucesso) {
            const u = resposta.dados;
            document.getElementById('usuario-id').value = u.id;
            document.getElementById('usuario-nome').value = u.nome || '';
            document.getElementById('usuario-email').value = u.email || '';
            document.getElementById('usuario-perfil').value = u.perfil || 'atendente';
            document.getElementById('usuario-senha').value = '';
            document.getElementById('usuario-senha').placeholder = 'Deixe em branco para manter';
            document.getElementById('form-titulo').textContent = 'Editar Usuário';
            document.getElementById('btn-salvar').textContent = 'Salvar';
            document.getElementById('btn-cancelar').classList.remove('d-none');
        }
    } catch (erro) {
        mostrarMensagem('Erro ao buscar usuário.', 'danger');
    }
}

async function inativarUsuario(id) {
    if (!confirm('Deseja inativar este usuário?')) return;
    try {
        const resposta = await AtendeLabApi.post('usuarios', 'inativar', { id });
        mostrarMensagem(resposta.mensagem, 'success');
        carregarUsuarios();
    } catch (erro) {
        mostrarMensagem('Erro ao inativar usuário.', 'danger');
    }
}

function cancelarEdicao() {
    document.getElementById('form-usuario').reset();
    document.getElementById('usuario-id').value = '';
    document.getElementById('usuario-senha').placeholder = 'Senha';
    document.getElementById('form-titulo').textContent = 'Novo Usuário';
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
