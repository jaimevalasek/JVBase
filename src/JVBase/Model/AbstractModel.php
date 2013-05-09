<?php

namespace JVBase\Model;

abstract class AbstractModel
{
	public function __construct(AbstractModel $model = null) {
		if ($model) {
			foreach (get_object_vars($this) as $key => $values) {
				$getter = 'get' . ucfirst($key);
				$setter = 'set' . ucfirst($key);
				if (method_exists($this, $setter) && method_exists($this, $getter)) {
					$this->$setter($model->$getter());
				}
			}
		}
		
		return $this;
	}
	
	public function has($property) {
		$getter = 'get' . ucfirst($property);
		if (method_exists($this, $getter)) {
			if ('s' === substr($property, 0, -1) && is_array($this->$getter())) {
				return true;
			} elseif ($this->$getter()) {
				return true;
			}
		}
	}
	
	public function __toString() {
		return get_class($this);
	}
}