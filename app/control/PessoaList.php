<?php
class PessoaList extends TPage
{
    private $datagrid; // Datagrid onde os dados serão exibidos
    private $form;     // Formulário de filtros

    public function __construct()
    {
        parent::__construct();

        // Criando um formulário para os filtros de busca
        $this->form = new TQuickForm('form_busca_pessoas');
        $this->form->setFormTitle('Busca de Pessoas'); // Título do formulário

        // Campo para filtrar por tipo (Físico ou Jurídico)
        $tipo = new TCombo('tipo');
        $tipo->addItems([
            'Físico' => 'Pessoa Física',
            'Jurídico' => 'Pessoa Jurídica',
        ]);

        // Campo de busca por Nome ou Razão Social
        $busca = new TEntry('busca');

        // Adicionando os campos ao formulário
        $this->form->addQuickField('Tipo', $tipo, 200);
        $this->form->addQuickField('Busca (Nome/Razão Social)', $busca, 300);

        // Botão de ação para realizar a busca
        $this->form->addQuickAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');

        // Criando o datagrid onde os resultados serão exibidos
        $this->datagrid = new TDataGrid();

        // Adicionando as colunas ao datagrid
        $col_id = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_tipo = new TDataGridColumn('tipo', 'Tipo', 'center', '15%');
        $col_nome = new TDataGridColumn('nome_completo', 'Nome/Razão Social', 'left', '35%');
        $col_email = new TDataGridColumn('email', 'Email', 'left', '30%');
        $col_data_cadastro = new TDataGridColumn('data_cadastro', 'Data de Cadastro', 'center', '20%');

        // Transformer para mostrar "Nome Completo" ou "Razão Social" dependendo do tipo
        $col_nome->setTransformer(function($value, $object) {
            return $object->tipo === 'Físico' ? $object->nome_completo : $object->razao_social;
        });

        // Adiciona as colunas ao datagrid
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_tipo);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_email);
        $this->datagrid->addColumn($col_data_cadastro);

        // Configura o modelo do datagrid
        $this->datagrid->createModel();

        // Criando um layout para adicionar o formulário e o datagrid
        $container = new TVBox();
        $container->style = 'width: 100%'; // Estilo para ocupar toda a largura
        $container->add($this->form);
        $container->add($this->datagrid);

        parent::add($container); // Adiciona o layout à página

        // Carrega os dados automaticamente ao inicializar a página
        $this->onReload();
    }

    /**
     * Recarrega os dados no datagrid
     */
    public function onReload()
    {
        try {
            // Abre uma transação no banco
            TTransaction::open('cadastrar_pessoas');

            $repository = new TRepository('Pessoa'); // Repositório da tabela Pessoa
            $criteria = new TCriteria(); // Critérios de busca

            // Aplica os filtros do formulário
            $dados = $this->form->getData();
            if (!empty($dados->tipo)) {
                $criteria->add(new TFilter('tipo', '=', $dados->tipo)); // Filtra por tipo
            }
            if (!empty($dados->busca)) {
                // Adiciona filtros para buscar pelo nome ou razão social
                $criteria->add(new TFilter('nome_completo', 'like', "%{$dados->busca}%"), TExpression::OR_OPERATOR);
                $criteria->add(new TFilter('razao_social', 'like', "%{$dados->busca}%"), TExpression::OR_OPERATOR);
            }

            $pessoas = $repository->load($criteria); // Carrega os dados filtrados

            // Limpa os dados do datagrid antes de adicionar novos
            $this->datagrid->clear();
            if ($pessoas) {
                foreach ($pessoas as $pessoa) {
                    $this->datagrid->addItem($pessoa); // Adiciona cada pessoa ao datagrid
                }
            }

            TTransaction::close(); // Fecha a transação
        } catch (Exception $e) {
            // Exibe uma mensagem de erro caso algo dê errado
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Aplica os filtros de busca
     */
    public function onSearch($param)
    {
        $this->form->setData($param); // Preenche o formulário com os dados da busca
        $this->onReload(); // Recarrega os dados com os filtros aplicados
    }
}
