# Prova Técnica - Ageway

Projeto desenvolvido para gerenciamento de pessoas com cadastro, edição, listagem e exclusão.

## Autor

Jeferson Oliveira

## 🚀 Tecnologias Utilizadas
- **PHP** (7.4 ou superior)
- **MySQL** ou **MariaDB**
- **Adianti Framework**
- **Composer**

---

## ⚙️ Pré-requisitos

Antes de começar, certifique-se de ter instalado:
- **PHP** (7.4 ou superior).
- **Composer** (gerenciador de dependências do PHP).
- **MySQL** (banco de dados).
- **Servidor web** (opcional, caso utilize o PHP embutido, não será necessário).

---

## 🛠️ Passo a Passo para Rodar o Projeto

### 1. Baixar o Projeto
Clone o repositório do GitHub:
```bash
git clone https://github.com/Jef-Oliver/prova-tecnica-Ageway.git

cd prova-tecnica-Ageway
```
## 🛠️ Configurar o Banco de Dados
Crie um banco de dados no MySQL com o nome cadastrar_pessoas (ou o nome configurado no arquivo application.ini):

```sql
CREATE DATABASE cadastrar_pessoas;
```
## Crie a tabela necessária
Dentro do banco de dados cadastrar_pessoas, crie a tabela pessoas com a seguinte estrutura:

```sql
USE cadastrar_pessoas;

CREATE TABLE pessoas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Físico', 'Jurídico') NOT NULL,
    nome_completo VARCHAR(255),
    razao_social VARCHAR(255),
    cpf VARCHAR(14) UNIQUE,
    cnpj VARCHAR(18) UNIQUE,
    email VARCHAR(255),
    telefone VARCHAR(15),
    endereco_completo VARCHAR(500),
    data_cadastro DATETIME
);
```

## Configurar o Arquivo application.ini
No diretório app/config, edite o arquivo application.ini e atualize as informações do banco de dados:
OBS: se ainda não funcionar use o arquivo cadastrar_pessoas.ini dentro de app/config

```sql
[database]
type     = mysql
host     = localhost
name     = cadastrar_pessoas
user     = seu_usuario
pass     = sua_senha
```

## Instalar Dependências
```bash
composer install
```

## Iniciar o Servidor
```bash
php -S localhost:8000
```

## Imagens

![image](https://github.com/user-attachments/assets/84cfc4b9-1162-4f59-b2dd-2f14a4150f97)
![image](https://github.com/user-attachments/assets/c9b0366c-d2d4-4e82-a7f3-8dec0a6b96e8)
![image](https://github.com/user-attachments/assets/85aedcbe-230e-4336-97fb-7cdb3803f4b9)
![image](https://github.com/user-attachments/assets/26d1694c-3dd9-40eb-8caf-eb76a7c68f1a)
![image](https://github.com/user-attachments/assets/c194e14e-1437-4565-a5d5-cc6842b46cdc)
![image](https://github.com/user-attachments/assets/89657244-a42a-45e9-89be-8524c85dd618)
![image](https://github.com/user-attachments/assets/f6405146-2285-44ed-bb7f-f99b0857bb02)
