<?php
class PessoaList extends TPage
{
    private $datagrid;
    private $form;

    public function __construct()
    {
        parent::__construct();

        // Criação do formulário de filtros
        $this->form = new TQuickForm('form_busca_pessoas');
        $this->form->setFormTitle('Busca de Pessoas');

        $tipo = new TCombo('tipo');
        $tipo->addItems([
            'Físico' => 'Pessoa Física',
            'Jurídico' => 'Pessoa Jurídica',
        ]);

        $busca = new TEntry('busca');

        $this->form->addQuickField('Tipo', $tipo, 200);
        $this->form->addQuickField('Busca (Nome/Razão Social)', $busca, 300);

        $this->form->addQuickAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');

        // Criação do datagrid
        $this->datagrid = new TDataGrid();

        // Colunas do datagrid
        $col_id = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_tipo = new TDataGridColumn('tipo', 'Tipo', 'center', '15%');
        $col_nome = new TDataGridColumn('nome_completo', 'Nome/Razão Social', 'left', '35%');
        $col_email = new TDataGridColumn('email', 'Email', 'left', '30%');

        // Adiciona colunas ao datagrid
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_tipo);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_email);

        // Ações no datagrid
        $actionEdit = new TDataGridAction(['PessoaForm', 'onEdit'], ['id' => '{id}']);
        $this->datagrid->addAction($actionEdit, 'Editar', 'fa:edit blue');

        $actionDelete = new TDataGridAction(['PessoaDelete', 'onDelete'], ['id' => '{id}']);
        $this->datagrid->addAction($actionDelete, 'Excluir', 'fa:trash red');

        // Modelo do datagrid
        $this->datagrid->createModel();

        // Contêiner para o layout
        $container = new TVBox();
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($this->datagrid);

        parent::add($container);

        // Carrega os dados inicialmente
        $this->onReload();
    }

    /**
     * Método para carregar os dados no datagrid
     */
    public function onReload()
    {
        try {
            TTransaction::open('cadastrar_pessoas');

            $repository = new TRepository('Pessoa');
            $criteria = new TCriteria();

            // Filtros
            $dados = $this->form->getData();
            if (!empty($dados->tipo)) {
                $criteria->add(new TFilter('tipo', '=', $dados->tipo));
            }
            if (!empty($dados->busca)) {
                $criteria->add(new TFilter('nome_completo', 'like', "%{$dados->busca}%"), TExpression::OR_OPERATOR);
                $criteria->add(new TFilter('razao_social', 'like', "%{$dados->busca}%"), TExpression::OR_OPERATOR);
            }

            $pessoas = $repository->load($criteria);

            $this->datagrid->clear();
            if ($pessoas) {
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
     * Método para aplicar filtros
     */
    public function onSearch($param)
    {
        $this->form->setData($param);
        $this->onReload();
    }
}