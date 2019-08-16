<?php

class Conexao extends PDO
{
	private $tip;
    private $server;
    private $dbname;
    private $user;
    private $password;
	private $stmt;

	protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
	);
    

	public function __construct()
    {
		try
		{
					$this->dbname = "SIF";
					$this->server = "10.0.31.60";
					$this->user = "sifrelatorio";
					$this->password = "S1frelat_12";
					$this->tip = "mssql";
			
			if ($this->tip == 'pgsql')
				return parent::__construct("pgsql:host={$this->server};dbname={$this->dbname}", $this->user, 
					$this->password, $this->options);

			elseif ($this->tip == 'mssql')
			{
				return parent::__construct("dblib:host={$this->server};dbname={$this->dbname}", $this->user, 
					$this->password, $this->options);

			}
			else if ($this->tip == 'mysql') {
				return parent::__construct("mysql:host={$this->server};dbname={$this->dbname}", $this->user, 
					$this->password, $this->options);
			}
		} 
		catch (Exception $ex)
		{
			throw new Exception($ex);
		}

    }

}