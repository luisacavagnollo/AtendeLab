<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Relatório de Atendimentos</h2>
    <button type="button" onclick="window.print()" class="btn btn-outline-light btn-sm no-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-1" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 10a1 1 0 0 1-1-1v-2h8v2a1 1 0 0 1-1 1z"/>
        </svg>
        Imprimir
    </button>
</div>

<!-- Filtros -->
<div class="card mb-4 no-print">
    <div class="card-body">
        <form id="form-filtros" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="filtro-status" class="form-select">
                    <option value="">Todos</option>
                    <option value="aberto">Aberto</option>
                    <option value="em_andamento">Em andamento</option>
                    <option value="concluido">Concluído</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pessoa</label>
                <select id="filtro-pessoa" class="form-select">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Data início</label>
                <input type="date" id="filtro-data-inicio" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Data fim</label>
                <input type="date" id="filtro-data-fim" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="limparFiltros()">Limpar</button>
            </div>
        </form>
    </div>
</div>

<!-- Resumo -->
<p class="text-muted mb-3" id="resumo-relatorio">Carregando...</p>

<!-- Tabela -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Pessoa</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Responsável</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="tabela-relatorio">
            <tr><td colspan="7" class="text-center">Carregando...</td></tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    carregarPessoas();
    carregarRelatorio();

    document.getElementById('form-filtros').addEventListener('submit', (e) => {
        e.preventDefault();
        carregarRelatorio();
    });
});

async function carregarPessoas() {
    try {
        const resp = await AtendeLabApi.get('pessoas', 'listar');
        if (resp.sucesso) {
            const select = document.getElementById('filtro-pessoa');
            resp.dados.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nome;
                select.appendChild(opt);
            });
        }
    } catch (e) {
        console.error('Erro ao carregar pessoas:', e);
    }
}

async function carregarRelatorio() {
    const params = {};
    const status = document.getElementById('filtro-status').value;
    const pessoa = document.getElementById('filtro-pessoa').value;
    const dataInicio = document.getElementById('filtro-data-inicio').value;
    const dataFim = document.getElementById('filtro-data-fim').value;

    if (status) params.status = status;
    if (pessoa) params.pessoa_id = pessoa;
    if (dataInicio) params.data_inicio = dataInicio;
    if (dataFim) params.data_fim = dataFim;

    try {
        const resp = await AtendeLabApi.get('relatorios', 'gerar', params);
        const tbody = document.getElementById('tabela-relatorio');
        const resumo = document.getElementById('resumo-relatorio');

        if (resp.sucesso) {
            resumo.textContent = `Total: ${resp.total} registro(s) encontrado(s)`;

            if (resp.dados.length > 0) {
                tbody.innerHTML = resp.dados.map(a => {
                    const badgeClass = a.status === 'aberto' ? 'warning' : a.status === 'em_andamento' ? 'primary' : 'success';
                    const statusLabel = a.status === 'em_andamento' ? 'Em andamento' : a.status.charAt(0).toUpperCase() + a.status.slice(1);
                    return `
                        <tr>
                            <td>${a.data_atendimento || '—'}</td>
                            <td>${a.horario_atendimento || '—'}</td>
                            <td>${esc(a.pessoa_nome || '—')}</td>
                            <td>${esc(a.tipo_nome || '—')}</td>
                            <td>${esc(a.descricao || '—')}</td>
                            <td>${esc(a.responsavel_nome || '—')}</td>
                            <td><span class="badge bg-${badgeClass}">${statusLabel}</span></td>
                        </tr>
                    `;
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhum atendimento encontrado com os filtros aplicados.</td></tr>';
            }
        }
    } catch (e) {
        console.error('Erro ao carregar relatório:', e);
        document.getElementById('tabela-relatorio').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados.</td></tr>';
    }
}

function limparFiltros() {
    document.getElementById('filtro-status').value = '';
    document.getElementById('filtro-pessoa').value = '';
    document.getElementById('filtro-data-inicio').value = '';
    document.getElementById('filtro-data-fim').value = '';
    carregarRelatorio();
}

function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
