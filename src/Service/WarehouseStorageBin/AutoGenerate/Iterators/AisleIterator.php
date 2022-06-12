<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

final class AisleIterator extends AbstractIterator
{
    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        for ($i = 0; $i < $this->getAisleSectionIncrement(); $i++) {
            $this->current++;
        }

        if (is_numeric($this->getAisleSectionStartValue())) {
            $this->current = str_pad($this->current, strlen($this->getAisleSectionStartValue()), '0', STR_PAD_LEFT);
        }
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        return $this->current <= $this->getAisleSectionEndValue();
    }

    public function rewind()
    {
        $this->current = $this->getAisleSectionStartValue();
    }
}
