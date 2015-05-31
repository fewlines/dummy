<?php
namespace Fewlines\Core\Application;

class Buffer
{
	/**
	 * Starts a buffer
	 */
	public static function start() {
		ob_start();
	}

	/**
	 * Clears the buffer
	 *
	 * @param  boolean $force Clear all output
	 */
	public static function clear($force = false) {
		if (true == $force) {
			while ( ! empty(ob_get_contents())) {
            	ob_end_clean();
        	}
		}
		else {
			ob_end_clean();
		}
	}
}