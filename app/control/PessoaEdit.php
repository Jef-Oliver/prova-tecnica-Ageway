<?php
class PessoaEdit extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        // Criação do formulário
        $this->form = new TQuickForm('form_edit_pessoa');
        $this->form->setFormTitle('Alterar Cadastro de Pessoa');

        // Campo de busca
        $campoBusca = new TEntry('campo_busca');
        $botaoBusca = new TButton('buscar');
        $botaoBusca->setLabel('Buscar');
        $botaoBusca->setImage('fa:search');
        $botaoBusca->setAction(new TAction([$this, 'onSearch']), 'Buscar Pessoa');

        $this->form->addQuickField('Buscar (CPF ou Nome Completo)', $campoBusca, 300);
        $this->form->addQuickField('', $botaoBusca);

        // Campos do formulário
        $id = new TEntry('id');
        $tipo = new TCombo('tipo');
        $nome_completo = new TEntry('nome_completo');
        $razao_social = new TEntry('razao_social');
        $cpf = new TEntry('cpf');
        $cnpj = new TEntry('cnpj');
        $email = new TEntry('email');
        $telefone = new TEntry('telefone');

        // Campos de endereço
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $estado = new TEntry('estado');
        $cep = new TEntry('cep');

        // Configurações dos campos
        $id->setEditable(false);
        $tipo->addItems([
            'Físico' => 'Pessoa Física',
            'Jurídico' => 'Pessoa Jurídica',
        ]);

        // Adiciona os campos ao formulário
        $this->form->addQuickField('ID', $id, 100);
        $this->form->addQuickField('Tipo', $tipo, 200);
        $this->form->addQuickField('Nome Completo', $nome_completo, 300);
        $this->form->addQuickField('Razão Social', $razao_social, 300);
        $this->form->addQuickField('CPF', $cpf, 200);
        $this->form->addQuickField('CNPJ', $cnpj, 200);
        $this->form->addQuickField('Email', $email, 300);
        $this->form->addQuickField('Telefone', $telefone, 200);

        // Adiciona os campos de endereço
        $this->form->addQuickField('Rua', $rua, 300);
        $this->form->addQuickField('Número', $numero, 100);
        $this->form->addQuickField('Complemento', $complemento, 300);
        $this->form->addQuickField('Bairro', $bairro, 200);
        $this->form->addQuickField('Cidade', $cidade, 200);
        $this->form->addQuickField('Estado', $estado, 100);
        $this->form->addQuickField('CEP', $cep, 150);

        // Botão de salvar
        $this->form->addQuickAction('Salvar Alterações', new TAction([$this, 'onSave']), 'fa:save');

        // Adiciona o formulário à página
        parent::add($this->form);
    }

    /**
     * Método para buscar pessoa pelo CPF ou Nome Completo
     */
    public function onSearch($param)
    {
        try {
            TTransaction::open('cadastrar_pessoas');

            $campoBusca = $param['campo_busca'];

            // Verifica se é CPF (apenas números)
            if (is_numeric($campoBusca)) {
                $pessoa = Pessoa::where('cpf', '=', $campoBusca)->first();
            } else {
                $pessoa = Pessoa::where('nome_completo', 'like', "%$campoBusca%")->first();
            }

            if ($pessoa) {
                // Carrega os dados da pessoa no formulário
                $this->form->setData($pessoa);

                // Ajusta os campos com base no tipo da pessoa
                if ($pessoa->tipo === 'Físico') {
                    TField::disableField('form_edit_pessoa', 'razao_social');
                    TField::disableField('form_edit_pessoa', 'cnpj');
                    TField::enableField('form_edit_pessoa', 'nome_completo');
                    TField::enableField('form_edit_pessoa', 'cpf');
                } else {
                    TField::enableField('form_edit_pessoa', 'razao_social');
                    TField::enableField('form_edit_pessoa', 'cnpj');
                    TField::disableField('form_edit_pessoa', 'nome_completo');
                    TField::disableField('form_edit_pessoa', 'cpf');
                }
            } else {
                new TMessage('info', 'Pessoa não encontrada.');
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Método para salvar alterações
     */
    public function onSave($param)
    {
        try {
            TTransaction::open('cadastrar_pessoas');

            // Salva os dados no banco
            $dados = $this->form->getData();

            // Remove a formatação de CPF, CNPJ e CEP antes de salvar
            $dados->cpf = isset($dados->cpf) ? preg_replace('/\D/', '', $dados->cpf) : null;
            $dados->cnpj = isset($dados->cnpj) ? preg_replace('/\D/', '', $dados->cnpj) : null;
            $dados->cep = isset($dados->cep) ? preg_replace('/\D/', '', $dados->cep) : null;

            $pessoa = new Pessoa;
            $pessoa->fromArray((array) $dados);
            $pessoa->store();

            TTransaction::close();

            new TMessage('info', 'Cadastro atualizado com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
