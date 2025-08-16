# Project Setup Guide  

## Instalação do XAMPP  
1. Baixe a versão mais recente do [XAMPP](https://www.apachefriends.org).  
2. Execute o instalador e selecione **Apache, MySQL, PHP e phpMyAdmin** (opções padrão).  

## Localizando a pasta *htdocs*  
- **Windows:** `C:\xampp\htdocs`  
- **Mac/Linux:** `/opt/lampp/htdocs`  

Copie seus arquivos do projeto para essa pasta (ex.: extraia o ZIP ou clone o repositório dentro dela).  

## Importando o Banco de Dados  
1. Localize seu arquivo **.sql**.  
2. Acesse o [phpMyAdmin](http://localhost/phpmyadmin).  
3. Crie um novo banco de dados → vá em **Importar** → selecione o arquivo **.sql** → clique em **Executar**.  

## Executando o Projeto  
1. Inicie o **Apache** e o **MySQL** no Painel de Controle do XAMPP.  
2. No navegador, acesse: localhost/"nome do projeto"
3. 
---

# Stingray  

Stingray é um projeto acadêmico de **e-commerce especializado na venda de acessórios e periféricos para computadores**.  
O objetivo é desenvolver uma plataforma completa, responsiva e intuitiva, que permita ao usuário **navegar, pesquisar, comparar e comprar produtos** de forma simples e segura.  

## Descrição  
O projeto Stingray foi desenvolvido como parte de um trabalho acadêmico, aplicando conceitos de **desenvolvimento web, design responsivo, boas práticas de programação e usabilidade**.  
O sistema simula uma loja virtual de acessórios de computador, permitindo que usuários:  
- Realizem buscas  
- Visualizem detalhes dos produtos  
- Adicionem itens ao carrinho de compras  

## Funcionalidades  
- Sistema de login com controle de sessão  
- Catálogo de produtos com imagens, descrições e preços  
- Barra de pesquisa e filtros por categoria  
- Sistema de carrinho de compras  
- Página de detalhes do produto  
- Layout responsivo (desktop e mobile)  
- Design moderno e intuitivo  

## Tecnologias Utilizadas  
- **HTML5** – Estrutura das páginas  
- **CSS3 (e Bootstrap)** – Estilização e responsividade  
- **JavaScript** – Interatividade e lógica do cliente  
- **PHP/MySQL** – Backend para gerenciamento de produtos e pedidos  

---

# PHPproject  

## Main Objective  
Desenvolver um **sistema web para gerenciamento de funcionários e estoque**, com diferentes níveis de acesso (gerente, estoquista e funcionário), implementando funcionalidades **CRUD** (Create, Read, Update, Delete).  

## Tecnologias Utilizadas  
- **Back-end:** PHP puro (com autenticação por sessões)  
- **Banco de Dados:** MySQL (`funcionarios` e `produtos`)  
- **Front-end:** HTML5, CSS3 (design responsivo com gradientes), JavaScript (validações em tempo real)  
- **Segurança:** Sanitização de inputs, hashing de senhas (`password_hash`) e controle de acesso por papéis  

## Funcionalidades  

### Módulo de Funcionários  
- **Autenticação:** Login com verificação de credenciais e redirecionamento por função  
- **Dashboard:**  
  - Gerente: acesso total (funcionários e produtos)  
  - Estoquista: apenas gerenciamento de produtos  
  - Funcionário: apenas visualização de perfil  
- **CRUD Funcionários:**  
  - Adicionar: formulário com validação de senha em tempo real  
  - Listar: tabela pesquisável com ações de edição/remoção  
  - Editar: atualização de dados (senha opcional)  
  - Excluir: com confirmação (exceto usuário logado)  

### Módulo de Produtos  
- **CRUD Produtos:**  
  - Adicionar: nome, preço, quantidade e status (disponível/vendido)  
  - Listar: tabela com filtros e indicadores de estoque baixo  
  - Editar/Excluir: com feedback visual  

### Extras  
- Perfil do usuário: atualizar dados pessoais e senha  
- Logout: encerramento de sessão com redirecionamento  
- Validações em tempo real com JavaScript  

## Estrutura do Banco de Dados  
- **Tabela `funcionarios`**  
  - `idFunc`, `nickname`, `senha` (hash), `tipo` (manager/employee/stock keeper)  
- **Tabela `produtos`**  
  - `id_produto`, `nome_produto`, `preco`, `quantidade_estoque`, `estado`  

## Diferenciais  
- Interface intuitiva com feedback visual  
- Segurança contra SQL Injection  
- Senhas armazenadas de forma segura  
- Layout responsivo para múltiplos dispositivos  
- Validações em tempo real (ex.: força da senha, preço > 0)  

## Final Notes  
- **Pronto para uso:** com dados de exemplo
- **Escalável:** pode ser expandido com relatórios e módulos extras  
- Demonstra domínio de **full-stack web development**: do modelo de banco até a interface segura e interativa  

---

## Desenvolvido por  
- Aladar Pinoti  
- Daniel Lopes  
- Guilherme Rodrigues  
- Gustavo Antonio  

**Contato:** guilhermefilho095@gmail.com  
  


