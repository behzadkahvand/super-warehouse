<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

final class CellIterator extends AbstractIterator
{
    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $aisleSerial = explode('-', $this->current)[0];
        $baySerial   = explode('-', $this->current)[1];
        $cellSerial  = explode('-', $this->current)[2];

        for ($k = 0; $k < $this->getCellSectionIncrement(); $k++) {
            $cellSerial++;
            if ($cellSerial > $this->getCellSectionEndValue()) {
                $cellSerial = $this->getCellSectionStartValue();
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

                if (is_numeric($this->getBaySectionStartValue())) {
                    $baySerial = str_pad($baySerial, strlen($this->getAisleSectionStartValue()), '0', STR_PAD_LEFT);
                }
                break;
            }
        }

        if (is_numeric($this->getCellSectionStartValue())) {
            $cellSerial = str_pad($cellSerial, strlen($this->getAisleSectionStartValue()), '0', STR_PAD_LEFT);
        }

        $this->current = $this->concatSerials($aisleSerial, $baySerial, $cellSerial);
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        $aisleSection = explode('-', $this->current)[0];
        $baySection   = explode('-', $this->current)[1];
        $cellSection  = explode('-', $this->current)[2];

        return ($aisleSection <= $this->getAisleSectionEndValue()) &&
            ($baySection <= $this->getBaySectionEndValue()) &&
            ($cellSection <= $this->getCellSectionEndValue());
    }

    public function rewind()
    {
        $this->current = $this->concatSerials(
            $this->getAisleSectionStartValue(),
            $this->getBaySectionStartValue(),
            $this->getCellSectionStartValue()
        );
    }
}
