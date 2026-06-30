<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Atendimentos</h2>

<!-- Formulário de novo atendimento -->
<div class="card mb-4">
    <div class="card-body">
        <h5>Novo Atendimento</h5>
        <form id="form-atendimento">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="pessoa_id" id="select-pessoa" class="form-select" required>
                        <option value="">Pessoa...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo_atendimento_id" id="select-tipo" class="form-select" required>
                        <option value="">Tipo...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="data_atendimento" id="data-atendimento" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="time" name="horario_atendimento" id="horario-atendimento" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Registrar</button>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-12">
                    <input type="text" name="descricao" id="descricao" class="form-control" placeholder="Descrição do atendimento" required>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Mensagem -->
<div id="mensagem" class="alert d-none"></div>

<!-- Listagem -->
<table class="table table-striped">
    <thead>
        <tr><th>Data</th><th>Horário</th><th>Pessoa</th><th>Tipo</th><th>Descrição</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody id="tabela-atendimentos">
        <tr><td colspan="7" class="text-center">Carregando...</td></tr>
    </tbody>
</table>

<!-- Modal Concluir -->
<div class="modal fade" id="modalConcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-concluir">
                <div class="modal-header">
                    <h5 class="modal-title">Concluir Atendimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="concluir-id">
                    <div class="mb-3">
                        <label class="form-label">Observação Final</label>
                        <textarea id="concluir-observacao" class="form-control" rows="3" placeholder="Descreva a conclusão do atendimento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Concluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Preencher data e hora atuais
    const agora = new Date();
    document.getElementById('data-atendimento').value = agora.toISOString().split('T')[0];
    document.getElementById('horario-atendimento').value = agora.toTimeString().slice(0, 5);

    carregarOpcoes();
    carregarAtendimentos();

    // Criar atendimento
    document.getElementById('form-atendimento').addEventListener('submit', async (e) => {
        e.preventDefault();
        const dados = {
            pessoa_id: document.getElementById('select-pessoa').value,
            tipo_atendimento_id: document.getElementById('select-tipo').value,
            data_atendimento: document.getElementById('data-atendimento').value,
            horario_atendimento: document.getElementById('horario-atendimento').value,
            descricao: document.getElementById('descricao').value
        };

        try {
            const resposta = await AtendeLabApi.post('atendimentos', 'criar', dados);
            mostrarMensagem(resposta.mensagem, 'success');
            document.getElementById('form-atendimento').reset();
            document.getElementById('data-atendimento').value = agora.toISOString().split('T')[0];
            document.getElementById('horario-atendimento').value = agora.toTimeString().slice(0, 5);
            carregarAtendimentos();
        } catch (erro) {
            mostrarMensagem('Erro ao registrar atendimento.', 'danger');
        }
    });

    // Concluir atendimento
    document.getElementById('form-concluir').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('concluir-id').value;
        const observacao_final = document.getElementById('concluir-observacao').value;

        try {
            const resposta = await AtendeLabApi.post('atendimentos', 'alterarStatus', {
                id,
                status: 'concluido',
                observacao_final
            });
            mostrarMensagem(resposta.mensagem, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalConcluir')).hide();
            carregarAtendimentos();
        } catch (erro) {
            mostrarMensagem('Erro ao concluir atendimento.', 'danger');
        }
    });
});

async function carregarOpcoes() {
    try {
        const resposta = await AtendeLabApi.get('atendimentos', 'opcoesFormulario');
        if (resposta.sucesso) {
            const selectPessoa = document.getElementById('select-pessoa');
            resposta.pessoas.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nome;
                selectPessoa.appendChild(opt);
            });

            const selectTipo = document.getElementById('select-tipo');
            resposta.tipos.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nome;
                selectTipo.appendChild(opt);
            });
        }
    } catch (erro) {
        console.error('Erro ao carregar opções:', erro);
    }
}

async function carregarAtendimentos() {
    try {
        const resposta = await AtendeLabApi.get('atendimentos', 'listar');
        const tbody = document.getElementById('tabela-atendimentos');
        if (resposta.sucesso && resposta.dados.length > 0) {
            tbody.innerHTML = resposta.dados.map(a => {
                const badgeClass = a.status === 'aberto' ? 'warning' : a.status === 'em_andamento' ? 'primary' : 'success';
                const statusLabel = a.status === 'em_andamento' ? 'Em andamento' : a.status.charAt(0).toUpperCase() + a.status.slice(1);
                let acoes = '';

                if (a.status === 'aberto') {
                    acoes = `<button class="btn btn-sm btn-primary" onclick="iniciarAtendimento(${a.id})">Iniciar</button>`;
                } else if (a.status === 'em_andamento') {
                    acoes = `<button class="btn btn-sm btn-success" onclick="abrirModalConcluir(${a.id})">Concluir</button>`;
                } else {
                    acoes = '<span class="text-muted">—</span>';
                }

                return `
                    <tr>
                        <td>${a.data_atendimento || '—'}</td>
                        <td>${a.horario_atendimento || '—'}</td>
                        <td>${esc(a.pessoa_nome || '—')}</td>
                        <td>${esc(a.tipo_nome || '—')}</td>
                        <td>${esc(a.descricao || '—')}</td>
                        <td><span class="badge bg-${badgeClass}">${statusLabel}</span></td>
                        <td>${acoes}</td>
                    </tr>
                `;
            }).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Nenhum atendimento registrado.</td></tr>';
        }
    } catch (erro) {
        console.error('Erro ao carregar atendimentos:', erro);
    }
}

async function iniciarAtendimento(id) {
    try {
        const resposta = await AtendeLabApi.post('atendimentos', 'alterarStatus', { id, status: 'em_andamento' });
        mostrarMensagem(resposta.mensagem, 'success');
        carregarAtendimentos();
    } catch (erro) {
        mostrarMensagem('Erro ao iniciar atendimento.', 'danger');
    }
}

function abrirModalConcluir(id) {
    document.getElementById('concluir-id').value = id;
    document.getElementById('concluir-observacao').value = '';
    new bootstrap.Modal(document.getElementById('modalConcluir')).show();
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
