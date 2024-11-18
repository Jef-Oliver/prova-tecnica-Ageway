<?php
class PessoaDelete extends TPage
{
    private $datagrid;

    public function __construct()
    {
        parent::__construct();

        // Criando o datagrid que exibe os registros a serem excluídos
        $this->datagrid = new TDataGrid();

        // Coluna ID: para identificar unicamente cada pessoa
        $col_id = new TDataGridColumn('id', 'ID', 'center', '10%');
        
        // Coluna Nome/Razão Social: exibe o nome completo ou a razão social
        $col_nome = new TDataGridColumn('nome_completo', 'Nome/Razão Social', 'left', '50%');
        
        // Transformer para exibir Nome Completo ou Razão Social com base no tipo
        $col_nome->setTransformer(function ($value, $object) {
            return $object->tipo === 'Físico' ? $object->nome_completo : $object->razao_social;
        });

        // Coluna Tipo: indica se a pessoa é Física ou Jurídica
        $col_tipo = new TDataGridColumn('tipo', 'Tipo', 'center', '20%');

        // Adiciona as colunas ao datagrid
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_tipo);

        // Criação de uma ação para exclusão
        $actionDelete = new TDataGridAction([$this, 'onDelete']);
        $actionDelete->setField('id'); // Define o campo usado para identificar o registro
        $this->datagrid->addAction($actionDelete, 'Excluir', 'fa:trash red');

        // Configura o modelo do datagrid
        $this->datagrid->createModel();

        // Recarrega os dados no datagrid ao iniciar
        $this->onReload();

        // Adiciona o datagrid a um painel
        $panel = new TPanelGroup('Excluir Pessoas'); // Título do painel
        $panel->add($this->datagrid);

        // Adiciona o painel à página
        parent::add($panel);
    }

    /**
     * Recarrega os dados do datagrid com os registros do banco
     */
    public function onReload()
    {
        try {
            // Abre uma transação no banco
            TTransaction::open('cadastrar_pessoas');

            // Recupera os registros da tabela de pessoas
            $repository = new TRepository('Pessoa');
            $pessoas = $repository->load();

            if ($pessoas) {
                $this->datagrid->clear(); // Limpa o datagrid antes de adicionar novos itens
                foreach ($pessoas as $pessoa) {
                    $this->datagrid->addItem($pessoa); // Adiciona cada pessoa ao datagrid
                }
            }

            TTransaction::close(); // Fecha a transação
        } catch (Exception $e) {
            // Exibe uma mensagem de erro caso algo dê errado
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); // Reverte as alterações em caso de falha
        }
    }

    /**
     * Exclui uma pessoa com base no ID fornecido
     */
    public function onDelete($param)
    {
        try {
            // Abre uma transação no banco
            TTransaction::open('cadastrar_pessoas');

            if (isset($param['id'])) { // Verifica se o ID foi fornecido
                $pessoa = new Pessoa($param['id']); // Carrega o registro com o ID fornecido
                $pessoa->delete(); // Deleta o registro

                // Exibe uma mensagem de sucesso
                new TMessage('info', 'Pessoa excluída com sucesso!');
                $this->onReload(); // Atualiza o datagrid
            }

            TTransaction::close(); // Fecha a transação
        } catch (Exception $e) {
            // Exibe uma mensagem de erro caso algo dê errado
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); // Reverte as alterações em caso de falha
        }
    }
}
