<?php

/**
 * Resmon PHP base class
 * 
 * Can be used to generate Resmon parsable outputs. These outputs can be consumed
 * in Reconnoiter or other monitoring solutions.
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
class Resmon {
	
	protected $module = 'main';
	protected $service = 'local';
	protected $data;
	
	const TYPE_STRING = 's';
	const TYPE_DEFAULT = '0';
	const TYPE_LONGINT = 'l';
	const TYPE_INT = 'i';
	const TYPE_UNSIGNED_LONGINT = 'L';
	const TYPE_UNSIGNED_INT = 'I';
	const TYPE_FLOAT = 'n';
	const STATE_OK = 'OK';
	const STATE_BAD = 'BAD';
	const STATE_WARNING = 'WARNING';
	
	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * @return the $service
	 */
	public function getService() {
		return $this->service;
	}
	
	/**
	 * Set currently used module name
	 * 
	 * @param string $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}
	
	/**
	 * Set currently used service
	 * 
	 * @param string $service
	 */
	public function setService($service) {
		$this->service = $service;
	}
	
	/**
	 * Add new metric
	 * 
	 * @param string $name Unique name of the metric
	 * @param mixed $value The value of the metric
	 * @param string $type Metric type (best defined by the TYPE_* constants)
	 */
	public function addMetric($name, $value, $type = self::TYPE_DEFAULT) {
		$this->data [$this->getModule ()] [$this->getService ()] ['metrics'] [] = array (
			'name' => $name, 
			'value' => $value, 
			'type' => $type
		);
	}

	/**
	 * Set the state of currently used module/service
	 * 
	 * @param string $state State of the check, can be OK, BAD or WARNING
	 */
	public function setState($state = self::STATE_OK) {
		$this->data [$this->getModule ()] [$this->getService ()] ['state'] = $state;
	}

	/**
	 * Return the generated Resmon XML
	 * 
	 * @param boolean $print If set to true (default) print the XML with correct header
	 * @return string The generated XML
	 */
	public function outputAsXML( $print = true ) {
		$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$output .= "<?xml-stylesheet type=\"text/xsl\" href=\"resmon.xsl\"?>\n";
		$output .= "<ResmonResults>\n";

		foreach ($this->data as $module => $services) {
			foreach ($services as $service => $serviceData) {
				$output .= sprintf("\t<ResmonResult module=\"%s\" service=\"%s\">\n", 
					htmlentities($module), htmlentities($service));
				$output .= sprintf("\t\t<last_update>%d</last_update>\n", time());
				
				foreach ($serviceData['metrics'] as $metric) {
					
					switch ($metric['type']) {
						case self::TYPE_FLOAT:	
							$value = sprintf('%f', $metric['value']); 
							break;
						case self::TYPE_INT:
						case self::TYPE_LONGINT:
						case self::TYPE_UNSIGNED_INT:
						case self::TYPE_UNSIGNED_LONGINT:
							$value = sprintf('%d', $metric['value']); 
							break;
						default:
							$value = sprintf('%s', $metric['value']);
					}
					
					$output .= sprintf ( "\t\t<metric name=\"%s\" type=\"%s\">%s</metric>\n", 
						htmlentities($metric['name']), $metric['type'], htmlentities($value) );
				}
				$output .= sprintf("\t\t<state>%s</state>\n\t</ResmonResult>\n", 
					empty($serviceData['state'])?self::STATE_OK:$serviceData['state']);
			}
		}
		$output .= "</ResmonResults>\n";
		
		if ($print) {
			header ( "Content-Type: text/xml; encoding=UTF-8" );
			echo $output;
		}
		
		return $output;
	}
	
} // Resmon