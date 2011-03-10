# Resmon PHP Classes

Collection of classes enabling generation of Resmon format outputs.

## Included classes

### Resmon.class.php 	
Base class to handle adding metrics and formatting the XML output, 
all other classes inherit from it. 

### ResmonOracle.class.php
Extension of the Resmon class, collects metrics from Oracle Database, 
provides methods to gather various Oracle metric types. Currently
supports methods:

-  getSysStat - gather (G)V$SYSSTAT metrics
-  getDRCPStats - gather DRCP (connection pool) statistics, from (G)V$CPOOL_STATS

When initializiing the class, specify whether it is a RAC environment.

## Usage

1. Include class files
2. Instantiate the class
3. Call methods to collect metrics. Before calling addMetric() make sure you 
   set the service and module by calling setService() resp. setModule()
4. Call the outputAsXML method to print the XML file


## Example

	<?php
	
	require_once('Resmon.class.php');
	require_once('ResmonOracle.class.php');
	$resmon = new ResmonOracle('resmon','resmon', 'MYTNSNAME', true);
	$resmon->getSysStat();
	$resmon->getDRCPStats();
	$resmon->outputAsXML();

## Author

Created by Michal Taborsky <michal@taborsky.cz>
http://www.taborsky.cz/
http://twitter.com/whizz


## License

Copyright (c) 2011 Michal Taborsky

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


Note: The XSL and CSS stylesheets are borrowed from the official
Resmon distribution <https://labs.omniti.com/labs/resmon> 
