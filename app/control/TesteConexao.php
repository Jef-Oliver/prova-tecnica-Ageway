<?php
class TesteConexao extends TPage
{
    public function __construct()
    {
        parent::__construct();

        try {
            // Abre uma transação com o banco de dados
            TTransaction::open('cadastrar_pessoas'); // nome da conexão configurada no application.ini

            // Se a conexão foi bem-sucedida
            new TMessage('info', 'Conexão com o banco de dados bem-sucedida!');

            // Fecha a transação
            TTransaction::close();
        } catch (Exception $e) {
            // Caso ocorra algum erro
            new TMessage('error', 'Erro ao conectar com o banco de dados: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
