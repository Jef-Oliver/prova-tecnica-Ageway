<?php
class PessoaForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        // Formulário
        $this->form = new TQuickForm('form_pessoa');
        $this->form->setFormTitle('Cadastro de Pessoa');

        // Campos
        $tipo = new TCombo('tipo');
        $nomeCompleto = new TEntry('nome_completo');
        $razaoSocial = new TEntry('razao_social');
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

        // Adiciona opções ao campo Tipo
        $tipo->addItems([
            'Físico' => 'Pessoa Física',
            'Jurídico' => 'Pessoa Jurídica',
        ]);

        // Adiciona campos ao formulário
        $this->form->addQuickField('Tipo', $tipo, 200);
        $this->form->addQuickField('Nome Completo', $nomeCompleto, 300);
        $this->form->addQuickField('Razão Social', $razaoSocial, 300);
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

        // Ação ao mudar o tipo
        $tipo->setChangeAction(new TAction([$this, 'atualizaCampos']));

        // Adiciona máscaras de CPF, CNPJ e CEP nos campos
        TScript::create("
            $(document).ready(function() {
                $('#cpf').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, ''); // Remove caracteres não numéricos
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d{1,2})$/, '$1-$2');
                    $(this).val(value);
                });

                $('#cnpj').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, ''); // Remove caracteres não numéricos
                    value = value.replace(/(\\d{2})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d{4})(\\d)/, '$1/$2-$3');
                    $(this).val(value);
                });

                $('#cep').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, ''); // Remove caracteres não numéricos
                    value = value.replace(/(\\d{5})(\\d{3})$/, '$1-$2');
                    $(this).val(value);
                });
            });
        ");

        // Botão de salvar
        $this->form->addQuickAction('Salvar', new TAction([$this, 'salvarPessoa']), 'fa:save');

        parent::add($this->form);

        // Estado inicial (Pessoa Física)
        $this->ajustarCampos('Físico');
    }

    public static function atualizaCampos($param)
    {
        $tipo = $param['tipo'] ?? 'Físico';
        $pagina = new self;
        $pagina->ajustarCampos($tipo);
    }

    private function ajustarCampos($tipo)
    {
        if ($tipo === 'Físico') {
            TField::disableField('form_pessoa', 'razao_social');
            TField::disableField('form_pessoa', 'cnpj');
            TField::enableField('form_pessoa', 'nome_completo');
            TField::enableField('form_pessoa', 'cpf');
        } else {
            TField::enableField('form_pessoa', 'razao_social');
            TField::enableField('form_pessoa', 'cnpj');
            TField::disableField('form_pessoa', 'nome_completo');
            TField::disableField('form_pessoa', 'cpf');
        }
    }

    public function salvarPessoa($param)
    {
        try {
            TTransaction::open('cadastrar_pessoas');
            $dados = $this->form->getData();

            // Remove a formatação do CPF, CNPJ e CEP antes de salvar
            $dados->cpf = isset($dados->cpf) ? preg_replace('/\D/', '', $dados->cpf) : null;
            $dados->cnpj = isset($dados->cnpj) ? preg_replace('/\D/', '', $dados->cnpj) : null;
            $dados->cep = isset($dados->cep) ? preg_replace('/\D/', '', $dados->cep) : null;

            $pessoa = new Pessoa;
            $pessoa->fromArray((array) $dados);

            // Adiciona a data de cadastro automaticamente
            $pessoa->data_cadastro = date('Y-m-d H:i:s');

            $pessoa->store();

            TTransaction::close();

            new TMessage('info', 'Pessoa cadastrada com sucesso.');
            $this->form->clear();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
{
    try {
        if (isset($param['id'])) {
            TTransaction::open('cadastrar_pessoas');

            // Carrega a pessoa pelo ID
            $pessoa = Pessoa::find($param['id']);
            if ($pessoa) {
                $this->form->setData($pessoa); // Preenche o formulário com os dados
            }

            TTransaction::close();
        }
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage());
        TTransaction::rollback();
    }
}
}
