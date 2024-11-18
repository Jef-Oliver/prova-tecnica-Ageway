## Autor
Criado por Jeferson de Oliveira Santos.

## Configuração de ambiente
- Ambiente configurado
- base de dados criada
- tabela cadastrar_pessoas criada

## Ferramentas

- VSCODE
- MySQL Workbench

## Crud

# Cadastro de Pessoas

Projeto de cadastro de pessoas físicas e jurídicas utilizando o Adianti Framework.

## Pré-requisitos 

Antes de começar, certifique-se de ter as seguintes ferramentas instaladas no seu ambiente:
- [PHP 7.4](https://windows.php.net/downloads/releases/)  opção : 11/2/2022  9:47 PM     26237519 php-7.4.33-Win32-vc15-x64.zip
- [Composer](https://getcomposer.org/)
- [MySQL 5.7+](https://www.mysql.com/)
- [Git](https://git-scm.com/)
- [Adianti Framework](https://adiantiframework.com.br/downloads)

Após baixar o php7.4 coloqueo na unidade C

Acesse as variáveis de ambiente e configure o caminho do seu php exemplo: c:/php74

## Passo a Passo para Configuração do Projeto

### 1. Clone o Repositório
Execute o comando abaixo para clonar o projeto:
```bash
git clone https://github.com/Jef-Oliver/prova-tecnica-Ageway.git
```
### 2. Acesse a Pasta do Projeto pelo vscode

- cd sua-pasta

### 3. Instale as Dependências do Composer e quando baixar o Adianti

- composer install

### 4.  Configure o arquivo app extraido do template do Adianti, dentro tem o caminho app/config/application.ini se não funcionar, crie dentro da mesma pasta config_db.ini, e coloque o código abaixo dentro, com os dados do seu banco.

```bash
[database]
type = mysql
host = localhost
name = cadastrar_pessoas
user = root
pass = sua-senha
```

### 6. Inicialize o Servidor PHP
```bash
php -S localhost:8000
```

### 7. Acesse a Aplicação
```bash
http://localhost:8000
```

## Funcionalidades
1. Cadastro de Pessoas Físicas e Jurídicas.
2. Edição de Pessoas.
3. Exclusão de Registros.
4. Listagem com Filtros.

5. ## Imagens

![image](https://github.com/user-attachments/assets/84cfc4b9-1162-4f59-b2dd-2f14a4150f97)
![image](https://github.com/user-attachments/assets/c9b0366c-d2d4-4e82-a7f3-8dec0a6b96e8)
![image](https://github.com/user-attachments/assets/85aedcbe-230e-4336-97fb-7cdb3803f4b9)
![image](https://github.com/user-attachments/assets/26d1694c-3dd9-40eb-8caf-eb76a7c68f1a)
![image](https://github.com/user-attachments/assets/c194e14e-1437-4565-a5d5-cc6842b46cdc)
![image](https://github.com/user-attachments/assets/89657244-a42a-45e9-89be-8524c85dd618)
![image](https://github.com/user-attachments/assets/f6405146-2285-44ed-bb7f-f99b0857bb02)






