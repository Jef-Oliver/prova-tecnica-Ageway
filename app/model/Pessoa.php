<?php

class Pessoa extends TRecord
{
    const TABLENAME = 'pessoas';   // Nome da tabela no banco de dados
    const PRIMARYKEY = 'id';      // Nome da chave primária
    const IDPOLICY = 'serial';    // (auto incremento)

    // Atributos adicionais para mapeamento de campos da tabela
    private $tipo;
    private $nome_completo;
    private $razao_social;
    private $cpf;
    private $cnpj;
    private $email;
    private $telefone;
    private $endereco_completo;
    private $data_cadastro;

    public function __construct($id = NULL)
    {
        parent::__construct($id);

        // Atributos que serão carregados do banco de dados
        $this->addAttribute('tipo');
        $this->addAttribute('nome_completo');
        $this->addAttribute('razao_social');
        $this->addAttribute('cpf');
        $this->addAttribute('cnpj');
        $this->addAttribute('email');
        $this->addAttribute('telefone');
        $this->addAttribute('endereco_completo');
        $this->addAttribute('data_cadastro');
    }
    /**
     * Atributo virtual para exibir Nome ou Razão Social com base no tipo
     */
    public function get_nome_razao()
    {
        return $this->tipo === 'Físico' ? $this->nome_completo : $this->razao_social;
    }
}
