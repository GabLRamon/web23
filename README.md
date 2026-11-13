Restaurante — TDE 2

# GRUPO!!!!: Gabriel Leite Ramon, Leonardo Bueno de Castilho dos Santos, Joao Victor Araujo Lourenco Domingues

Requisitos

- PHP (no Debian, instale `php-cli php-mysql`).
- MySQL.
- Navegador com JavaScript.

Instalação

* Importe o `banco.sql`

Em caso de Linux: 
 
sudo apt update
sudo apt install php-cli php-mysql mariadb-server
sudo systemctl enable --now mariadb

1. Inicie o MySQL/MariaDB (por exemplo, pelo painel do XAMPP).
2. Importe `banco.sql` pelo phpMyAdmin, aba Importar. Alternativamente, execute `mysql -u root -p < banco.sql` no terminal, dentro desta pasta. Use um banco novo; o script de tabelas deve ser executado uma vez.
3. Edite a linha `mysqli_connect` em `api/index.php`, informando endereço e porta, usuário, senha e nome do banco. O padrão é `127.0.0.1:3306`, banco `restaurante`, usuário `root`, sem senha.

4. Com o PHP instalado, abra o terminal e execute `php -S localhost:8000`.

5. Acesse `http://localhost:8000/index.html`

No XAMPP, também é possível copiar esta pasta para `htdocs/restaurante`, iniciar Apache e MySQL, importar o SQL e acessar `http://localhost/restaurante/`. 


Demonstração

1. Acesse Cadastro de usuário e informe nome de usuário e senha. Não há conta ou senha padrão.

2. Faça login. A tela Listagem mostra seu usuário, os pratos e seus pedidos.

3. Acessa Gerenciar. Cadastre um prato.

4. Cria um pedido de duas unidades desse prato. Confira o total de R$ 50,00.

5. Edite o nome do prato/preço e a quantidade do pedido. 

6. Tente excluir um prato com pedidos: o banco tem que impedir e a tela tem que mostrar uma mensagem de erro. Exclui o pedido e depois o prato.

7. Altera nome de usuário/senha em Meu usuário. Saia e entre novamente com as novas credenciais.

8. Cadastre outro usuário: ele acessa o catálogo compartilhado e somente seus próprios pedidos. A API também limita alterações e exclusões de pedidos ao dono.

9. Exclua sua conta para demonstrar a exclusão do usuário. Seus pedidos são removidos junto, por chave estrangeira.

10. Depois de sair, tenta acessar `gerenciar.php` diretamente: deve voltar para login. Reinicie o servidor e confira que registros não excluídos continuam no banco.



