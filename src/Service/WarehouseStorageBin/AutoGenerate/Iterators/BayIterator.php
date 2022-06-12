<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

final class BayIterator extends AbstractIterator
{
    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $aisleSerial = explode('-', $this->current)[0];
        $baySerial   = explode('-', $this->current)[1];

        for ($j = 0; $j < $this->getBaySectionIncrement(); $j++) {
            $baySerial++;
            if ($baySerial > $this->getBaySectionEndValue()) {
                $baySerial = $this->getBaySectionStartValue();
                for ($i = 0; $i < $this->getAisleSectionIncrement(); $i++) {
                    $aisleSerial++;
                }
                if (is_numeric($this->getAisleSectionStartValue())) {
                    $aisleSerial = str_pad($aisleSerial, strlen($this->getAisleSectionStartValue()), '0', STR_PAD_LEFT);
                }
                break;
            }
        }

        $this->current = $this->concatSerials($aisleSerial, $baySerial);
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        $aisleSection = explode('-', $this->current)[0];
        $baySection   = explode('-', $this->current)[1];

        return ($aisleSection <= $this->getAisleSectionEndValue()) &&
            ($baySection <= $this->getBaySectionEndValue());
    }

    public function rewind()
    {
        $this->current = $this->concatSerials($this->getAisleSectionStartValue(), $this->getBaySectionStartValue());
    }
}
