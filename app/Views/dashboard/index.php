<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Dashboard</h2>
<p>Bem-vindo, <strong><?= htmlspecialchars($_SESSION['usuario']['nome'] ?? '') ?></strong>!</p>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-center text-bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Pessoas Cadastradas</h5>
                <p class="card-text display-6" id="total-pessoas">...</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center text-bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Tipos de Atendimento</h5>
                <p class="card-text display-6" id="total-tipos">...</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center text-bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Total de Atendimentos</h5>
                <p class="card-text display-6" id="total-atendimentos">...</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-4">
        <div class="card text-center text-bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Abertos</h5>
                <p class="card-text display-6" id="total-abertos">...</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center text-bg-secondary mb-3">
            <div class="card-body">
                <h5 class="card-title">Em Andamento</h5>
                <p class="card-text display-6" id="total-em-andamento">...</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center text-bg-dark mb-3">
            <div class="card-body">
                <h5 class="card-title">Concluídos</h5>
                <p class="card-text display-6" id="total-concluidos">...</p>
            </div>
        </div>
    </div>
</div>

<h4 class="mt-4">Atendimentos Recentes</h4>
<table class="table table-striped">
    <thead>
        <tr><th>ID</th><th>Pessoa</th><th>Descrição</th><th>Status</th><th>Data</th></tr>
    </thead>
    <tbody id="tabela-recentes">
        <tr><td colspan="5" class="text-center">Carregando...</td></tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const resposta = await AtendeLabApi.get('dashboard', 'resumo');
        if (resposta.sucesso) {
            const ind = resposta.indicadores;
            document.getElementById('total-pessoas').textContent = ind.total_pessoas;
            document.getElementById('total-tipos').textContent = ind.total_tipos;
            document.getElementById('total-atendimentos').textContent = ind.total_atendimentos;
            document.getElementById('total-abertos').textContent = ind.abertos;
            document.getElementById('total-em-andamento').textContent = ind.em_andamento;
            document.getElementById('total-concluidos').textContent = ind.concluidos;

            const tbody = document.getElementById('tabela-recentes');
            if (resposta.atendimentos_recentes && resposta.atendimentos_recentes.length > 0) {
                tbody.innerHTML = resposta.atendimentos_recentes.map(a => `
                    <tr>
                        <td>${a.id}</td>
                        <td>${a.pessoa_nome || '—'}</td>
                        <td>${a.descricao || '—'}</td>
                        <td><span class="badge bg-${a.status === 'aberto' ? 'warning' : a.status === 'em_andamento' ? 'primary' : 'success'}">${a.status}</span></td>
                        <td>${a.data_atendimento || '—'}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum atendimento recente.</td></tr>';
            }
        }
    } catch (erro) {
        console.error('Erro ao carregar dashboard:', erro);
        document.getElementById('total-pessoas').textContent = '!';
        document.getElementById('total-tipos').textContent = '!';
        document.getElementById('total-atendimentos').textContent = '!';
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
