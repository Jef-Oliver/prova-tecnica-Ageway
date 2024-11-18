<?php
class PessoaForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        // Criando o formulário com título "Cadastro de Pessoa"
        $this->form = new TQuickForm('form_pessoa');
        $this->form->setFormTitle('Cadastro de Pessoa');

        // Definindo os campos do formulário
        $tipo = new TCombo('tipo');
        $nomeCompleto = new TEntry('nome_completo');
        $razaoSocial = new TEntry('razao_social');
        $cpf = new TEntry('cpf');
        $cnpj = new TEntry('cnpj');
        $email = new TEntry('email');
        $telefone = new TEntry('telefone');

        // Campos relacionados ao endereço
        $rua = new TEntry('rua');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $estado = new TEntry('estado');
        $cep = new TEntry('cep');

        // Configurando as opções do campo "Tipo" (Pessoa Física ou Jurídica)
        $tipo->addItems([
            'Físico' => 'Pessoa Física',
            'Jurídico' => 'Pessoa Jurídica',
        ]);

        // Adicionando os campos ao formulário
        $this->form->addQuickField('Tipo', $tipo, 200);
        $this->form->addQuickField('Nome Completo', $nomeCompleto, 300);
        $this->form->addQuickField('Razão Social', $razaoSocial, 300);
        $this->form->addQuickField('CPF', $cpf, 200);
        $this->form->addQuickField('CNPJ', $cnpj, 200);
        $this->form->addQuickField('Email', $email, 300);
        $this->form->addQuickField('Telefone', $telefone, 200);

        // Adicionando os campos de endereço ao formulário
        $this->form->addQuickField('Rua', $rua, 300);
        $this->form->addQuickField('Número', $numero, 100);
        $this->form->addQuickField('Complemento', $complemento, 300);
        $this->form->addQuickField('Bairro', $bairro, 200);
        $this->form->addQuickField('Cidade', $cidade, 200);
        $this->form->addQuickField('Estado', $estado, 100);
        $this->form->addQuickField('CEP', $cep, 150);

        // Definindo uma ação para mudar os campos baseados no tipo selecionado
        $tipo->setChangeAction(new TAction([$this, 'atualizaCampos']));

        // Máscaras para CPF, CNPJ e CEP para melhorar a experiência do usuário
        TScript::create("
            $(document).ready(function() {
                $('#cpf').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, '');
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d{1,2})$/, '$1-$2');
                    $(this).val(value);
                });

                $('#cnpj').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, '');
                    value = value.replace(/(\\d{2})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d)/, '$1.$2');
                    value = value.replace(/(\\d{3})(\\d{4})(\\d)/, '$1/$2-$3');
                    $(this).val(value);
                });

                $('#cep').on('input', function() {
                    var value = $(this).val();
                    value = value.replace(/\\D/g, '');
                    value = value.replace(/(\\d{5})(\\d{3})$/, '$1-$2');
                    $(this).val(value);
                });
            });
        ");

        // Botão de salvar com ação associada
        $this->form->addQuickAction('Salvar', new TAction([$this, 'salvarPessoa']), 'fa:save');

        parent::add($this->form);

        // Inicializando os campos com base em "Pessoa Física" como padrão
        $this->ajustarCampos('Físico');
    }

    // Função para ajustar quais campos ficam ativos ou desativados
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

    // Lógica para salvar os dados
    public function salvarPessoa($param)
    {
        try {
            TTransaction::open('cadastrar_pessoas');
            $dados = $this->form->getData();

            // Validação de CPF e CNPJ
            if ($dados->tipo === 'Físico' && !$this->validarCPF($dados->cpf)) {
                throw new Exception('CPF inválido!');
            }
            if ($dados->tipo === 'Jurídico' && !$this->validarCNPJ($dados->cnpj)) {
                throw new Exception('CNPJ inválido!');
            }

            // Removendo formatações para salvar corretamente no banco
            $dados->cpf = isset($dados->cpf) ? preg_replace('/\D/', '', $dados->cpf) : null;
            $dados->cnpj = isset($dados->cnpj) ? preg_replace('/\D/', '', $dados->cnpj) : null;
            $dados->cep = isset($dados->cep) ? preg_replace('/\D/', '', $dados->cep) : null;

            // Adicionando a data de cadastro automaticamente
            $dados->data_cadastro = date('Y-m-d H:i:s');

            $pessoa = new Pessoa;
            $pessoa->fromArray((array) $dados);
            $pessoa->store();

            TTransaction::close();

            new TMessage('info', 'Pessoa cadastrada com sucesso!');
            $this->form->clear();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código de erro para duplicados
                if (strpos($e->getMessage(), 'pessoas.cpf') !== false) {
                    new TMessage('error', 'CPF já cadastrado, favor cadastrar outro.');
                } elseif (strpos($e->getMessage(), 'pessoas.cnpj') !== false) {
                    new TMessage('error', 'CNPJ já cadastrado, favor cadastrar outro.');
                }
            } else {
                new TMessage('error', $e->getMessage());
            }
            TTransaction::rollback();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    // Validação do CPF
    private function validarCPF($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    // Validação do CNPJ
    private function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) != 14) {
            return false;
        }

        $t = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $d1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $d1 += $cnpj[$i] * $t[$i];
        }
        $d1 = 11 - ($d1 % 11);
        $d1 = ($d1 >= 10) ? 0 : $d1;

        $t = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $d2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $d2 += $cnpj[$i] * $t[$i];
        }
        $d2 = 11 - ($d2 % 11);
        $d2 = ($d2 >= 10) ? 0 : $d2;

        return ($cnpj[12] == $d1 && $cnpj[13] == $d2);
    }
}
