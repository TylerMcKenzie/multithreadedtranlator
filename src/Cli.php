<?php
/**
 * Created by Tyler Mckenzie
 * Date: 4/12/2019
 */

class Cli
{
	private $config_values = [];

	public function __construct(array $cli_config)
	{
		$this->config_values = $cli_config;
	}

	public function process($args)
	{
		$skip = null;
		$return = [];

		for ($i = 0; $i < count($args); $i++) {
			$current_arg = $args[$i];
			if (!empty($skip) && $current_arg === $skip) {
				continue;
			} else {
				foreach ($this->config_values as $config => $value) {
					$flags = explode("|", $config);

					foreach ($flags as $flag) {
						if ($flag == $current_arg) {
							$return[$value] = $args[$i+1];
							$skip = $args[$i+1];
							break;
						}
					}
				}
			}
		}

		return $return;
	}
}
