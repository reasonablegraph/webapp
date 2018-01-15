<?php

class LUtil {

	public static function log_var_dump($value){
		ob_start();
		var_dump($value);
		Log::info(ob_get_clean());
	}

}


class PArray extends ArrayObject implements JsonSerializable {

	// 	public function __construct($array = array()){
	// 			parent::__construct($array);
	// 	}

	public function appendIfNotEmpty($value) {
		if (!empty($value)) {
			$this->append($value);
		}
	}

	public function hasValue($value){
			return in_array( $value, (array)$this );
	}


///////////////////////////////////////////////////////////////////////////////////
//https://github.com/imsamurai/array-object-advanced
///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
	/**
	 * Merge arrays
	 *
	 * @param PArray $Array
	 * @return \PArray
	 */
	public function merge(PArray $Array) {
		$this->exchangeArray(array_merge($this->getArrayCopy(), $Array->getArrayCopy()));
		return $this;
	}

	/**
	 * Apply callback to each element of array
	 *
	 * @param callable $callback
	 * @return \PArray
	 */
	public function map(callable $callback) {
		$that = clone $this;
		foreach ($that->getIterator() as $index => $item) {
			$that->offsetSet($index, $callback($item));
		}
		return $that;
	}

	/**
	 * Remove all elements that not satisfy $callback
	 *
	 * @param callable $callback
	 * @param bool $resetKeys
	 * @return \PArray
	 */
	public function filter(callable $callback, $resetKeys = true) {
		$array = array_filter($this->getArrayCopy(), $callback);
		return new static($resetKeys ? array_values($array) : $array);
	}

	/**
	 * Reduces array into single value using $callback
	 *
	 * @param callable $callback
	 * @param mixed $init Initial value
	 * @return mixed
	 */
	public function reduce(callable $callback, $init = null) {
		return array_reduce(iterator_to_array($this->getIterator()), $callback, $init);
	}

	/**
	 * Group elements of array
	 *
	 * @param callable $callback
	 * @return \PArray
	 */
	public function group(callable $callback) {
		$array = array();
		foreach ($this->getIterator() as $index => $item) {
			$group = $callback($item);
			$array[$group][] = $item;
		}
		return new static($array);
	}

	/**
	 * For json serialization
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->getArrayCopy();
	}

	/**
	 * Make unique
	 *
	 * @param callable $callback Groupping callback
	 * @return \PArray
	 */
	public function unique(callable $callback) {
		$that = $this
			->group($callback)
			->map(function ($group) {
				return $group[0];
			});
		return $that->resetKeys();
	}

	/**
	 * Reset array keys to integer from 0 to n
	 *
	 * @return \PArray
	 */
	public function resetKeys() {
		return new static(array_values($this->getArrayCopy()));
	}

	/**
	 * Slice array
	 *
	 * @param int $offset
	 * @param int $length
	 * @param bool $preserveKeys
	 *
	 * @return \PArray
	 */
	public function slice($offset, $length = null, $preserveKeys = false) {
		return new static(array_slice($this->getArrayCopy(), $offset, $length, $preserveKeys));
	}

	/**
	 * Multisort array
	 *
	 * @param array|string $params
	 *
	 * @return \PArray
	 * @see ArraySort::multisort
	 */
	public function multisort($params) {
		return new static(ArraySort::multisort($this->getArrayCopy(), $params));
	}

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////


}
