<?php

class GDirection {
	
	const BOTH = 0;
	const IN = 1;
	const OUT = 2;

	public function oposite($direction){
		if ($direction == GDirection::IN){
			return GDirection::OUT;
		}
		if ($direction == GDirection::OUT){
			return GDirection::IN;
		}

		return $direction;
	}

}
