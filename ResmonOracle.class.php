<?php

/**
 * Resmon PHP class for pulling Oracle metrics
 * 
 * Extension of the base Resmon class. Currently supports following
 * metric types:
 *
 * - contents of the (G)V$SYSSTAT view 
 *
 * 
 * See the Resmon project website for further info on the format:
 * https://labs.omniti.com/labs/resmon
 * 
 * Copyright (c) 2011 Michal Taborsky
 * See LICENSE file for licensing info
 * 
 * @author Michal Taborsky <michal@taborsky.cz>
 *
 */
class ResmonOracle extends Resmon {
	
	protected $db;
	protected $RAC = false;
	protected $viewPrefix = 'V$';
	
	/**
	 * Class constructor
	 * 
	 * @param string $username Oracle user to connect as, must have privileges to querid tables
	 * @param string $password Oracle password
	 * @param string $connectString Oracle connec string (TNS name prefferably)
	 * @param boolean $isRAC Set to true if this is a RAC environment (default: false)
	 */
	public function __construct($username, $password, $connectString, $isRAC = false) {
		
		$tstart = microtime ( true );
		$this->db = @oci_connect ( $username, $password, $connectString );
		$tconnected = microtime ( true );
		$this->setModule ( 'Oracle::Core' );
		$this->setService ( 'local' );
		if ($this->db) {
			$this->addMetric ( 'connect_time', ($tconnected - $tstart) * 1000, self::TYPE_UNSIGNED_INT );
			$this->setState ( self::STATE_OK );
		} else {
			$this->setState ( self::STATE_BAD );
		}
		
		$this->RAC = $isRAC;
		if ($isRAC) {
			$this->viewPrefix = 'GV$';
		}
	
	}
	
	
	/**
	 * Collect metrics from (G)V$SYSSTAT view
	 * 
	 * @return boolean True on success, false on failure
	 */
	public function getSysStat() {
		
		if (!$this->db) {
			return false;
		}
		
		$tstart = microtime(true);
		$sql = "SELECT * FROM {$this->viewPrefix}SYSSTAT";
		$stm = oci_parse ( $this->db, $sql );
		$result = oci_execute ( $stm );
		$tsysstat = microtime ( true );
		
		$this->setModule ( 'Oracle::SysStat' );
		$ok = false;
		if ($result) {
			$this->setService('local');
			while ( $row = oci_fetch_assoc ( $stm ) ) {
				if ($this->RAC) {
					$this->setService ( 'Inst_' . $row ['INST_ID'] );
				}
				$this->addMetric ( $row ['NAME'], $row ['VALUE'], self::TYPE_LONGINT );
				$this->setState ( self::STATE_OK );
				$instances [$row ['INST_ID']] = true;
			}
		}
		oci_free_statement ( $stm );
		$this->setModule ( 'Oracle::Core' );
		$this->setService('local');
		$this->addMetric ( "sysstat_time", ($tsysstat - $tstart) * 1000, self::TYPE_UNSIGNED_INT );
		$this->addMetric ( "instances", count ( $instances ), Resmon::TYPE_INT );
		return true;
		
	}
	
	public function __destruct() {
		if ($this->db) {
			oci_close($this->db);
		}
	}

}