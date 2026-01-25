<?php

/**
 * Classe ContaBancaria
 * 
 * Esta classe representa uma conta bancária do mundo real.
 * Ela possui atributos como titular, número da conta, agência e saldo,
 * além de métodos para realizar operações básicas como depósito,
 * saque e consulta de saldo.
 * 
 * @author Aluno EBAC
 * @version 1.0
 */
class ContaBancaria
{
    // ========== PROPRIEDADES (ATRIBUTOS) ==========
    
    /**
     * Nome do titular da conta
     * @var string
     */
    private $titular;
    
    /**
     * Número da conta bancária
     * @var string
     */
    private $numeroConta;
    
    /**
     * Número da agência bancária
     * @var string
     */
    private $agencia;
    
    /**
     * Saldo atual da conta
     * @var float
     */
    private $saldo;

    // ========== CONSTRUTOR ==========
    
    /**
     * Construtor da classe ContaBancaria
     * Inicializa uma nova conta com os dados fornecidos
     * 
     * @param string $titular Nome do titular
     * @param string $numeroConta Número da conta
     * @param string $agencia Número da agência
     * @param float $saldoInicial Saldo inicial (padrão: 0)
     */
    public function __construct($titular, $numeroConta, $agencia, $saldoInicial = 0)
    {
        $this->titular = $titular;
        $this->numeroConta = $numeroConta;
        $this->agencia = $agencia;
        $this->saldo = $saldoInicial;
    }

    // ========== MÉTODOS (AÇÕES) ==========
    
    /**
     * Realiza um depósito na conta
     * 
     * @param float $valor Valor a ser depositado
     * @return bool Retorna true se o depósito foi realizado com sucesso
     */
    public function depositar($valor)
    {
        if ($valor > 0) {
            $this->saldo += $valor;
            echo "Depósito de R$ " . number_format($valor, 2, ',', '.') . " realizado com sucesso!\n";
            return true;
        }
        echo "Valor inválido para depósito.\n";
        return false;
    }

    /**
     * Realiza um saque da conta
     * 
     * @param float $valor Valor a ser sacado
     * @return bool Retorna true se o saque foi realizado com sucesso
     */
    public function sacar($valor)
    {
        if ($valor > 0 && $valor <= $this->saldo) {
            $this->saldo -= $valor;
            echo "Saque de R$ " . number_format($valor, 2, ',', '.') . " realizado com sucesso!\n";
            return true;
        }
        echo "Saldo insuficiente ou valor inválido para saque.\n";
        return false;
    }

    /**
     * Consulta e exibe o saldo atual da conta
     * 
     * @return float Retorna o saldo atual
     */
    public function consultarSaldo()
    {
        echo "Saldo atual: R$ " . number_format($this->saldo, 2, ',', '.') . "\n";
        return $this->saldo;
    }

    /**
     * Exibe todas as informações da conta
     */
    public function exibirDados()
    {
        echo "====================================\n";
        echo "        DADOS DA CONTA\n";
        echo "====================================\n";
        echo "Titular: " . $this->titular . "\n";
        echo "Agência: " . $this->agencia . "\n";
        echo "Conta: " . $this->numeroConta . "\n";
        echo "Saldo: R$ " . number_format($this->saldo, 2, ',', '.') . "\n";
        echo "====================================\n";
    }

    // ========== GETTERS E SETTERS ==========
    
    /**
     * Retorna o nome do titular
     * @return string
     */
    public function getTitular()
    {
        return $this->titular;
    }

    /**
     * Define o nome do titular
     * @param string $titular
     */
    public function setTitular($titular)
    {
        $this->titular = $titular;
    }

    /**
     * Retorna o número da conta
     * @return string
     */
    public function getNumeroConta()
    {
        return $this->numeroConta;
    }

    /**
     * Retorna o número da agência
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Retorna o saldo atual
     * @return float
     */
    public function getSaldo()
    {
        return $this->saldo;
    }
}

// ========== EXEMPLO DE USO ==========

// Criando uma nova conta bancária
$minhaConta = new ContaBancaria("João Silva", "12345-6", "0001", 1000);

// Exibindo os dados da conta
$minhaConta->exibirDados();

// Realizando operações
$minhaConta->depositar(500);
$minhaConta->sacar(200);
$minhaConta->consultarSaldo();

?>
