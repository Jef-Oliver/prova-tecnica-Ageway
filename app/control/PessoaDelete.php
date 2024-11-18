<?php
class PessoaDelete extends TPage
{
    private $datagrid;

    public function __construct()
    {
        parent::__construct();

        // Criação do datagrid
        $this->datagrid = new TDataGrid();

        // Adiciona colunas
        $col_id = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_nome = new TDataGridColumn('nome_completo', 'Nome', 'left', '50%');
        $col_tipo = new TDataGridColumn('tipo', 'Tipo', 'center', '20%');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_tipo);

        // Criação do botão excluir na coluna
        $actionDelete = new TDataGridAction([$this, 'onDelete']);
        $actionDelete->setField('id');
        $this->datagrid->addAction($actionDelete, 'Excluir', 'fa:trash red');

        // Adiciona ao datagrid
        $this->datagrid->createModel();

        // Popula o datagrid
        $this->onReload();

        // Adiciona à página
        $panel = new TPanelGroup('Excluir Pessoas');
        $panel->add($this->datagrid);

        parent::add($panel);
    }

    /**
     * Recarrega os dados do datagrid
     */
    public function onReload()
    {
        try {
            TTransaction::open('cadastrar_pessoas');

            $repository = new TRepository('Pessoa');
            $pessoas = $repository->load();

            if ($pessoas) {
                $this->datagrid->clear();
                foreach ($pessoas as $pessoa) {
                    $this->datagrid->addItem($pessoa);
                }
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Exclui uma pessoa pelo ID
     */
    public function onDelete($param)
    {
        try {
            TTransaction::open('cadastrar_pessoas');

            if (isset($param['id'])) {
                $pessoa = new Pessoa($param['id']);
                $pessoa->delete();

                new TMessage('info', 'Pessoa excluída com sucesso!');
                $this->onReload();
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
