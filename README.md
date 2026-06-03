# AtendeLab

Sistema de Controle de Atendimentos Acadêmicos desenvolvido para a disciplina Fábrica de Software da Univille.

## Tecnologias Utilizadas

- PHP 8.x
- MySQL
- Bootstrap
- HTML/CSS/JavaScript
- XAMPP (Apache + MySQL)
- Git e GitHub

## Funcionalidades Previstas

- Cadastro e autenticação de usuários (admin e atendente)
- Cadastro de pessoas (alunos/atendidos)
- Cadastro de tipos de atendimento
- Registro e acompanhamento de atendimentos
- Controle de status (aberto, em andamento, concluído)

## Como Executar Localmente

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/atendelab.git
   ```

2. Copie a pasta do projeto para o diretório do XAMPP:
   ```
   C:\xampp\htdocs\atendelab
   ```

3. Inicie o Apache e o MySQL no painel do XAMPP.

4. Acesse o phpMyAdmin (http://localhost/phpmyadmin) e importe o arquivo:
   ```
   database/atendelab.sql
   ```

5. Acesse o sistema no navegador:
   ```
   http://localhost/atendelab/public/
   ```
