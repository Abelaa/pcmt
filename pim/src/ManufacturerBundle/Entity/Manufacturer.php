<?php

namespace ManufacturerBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

class Manufacturer extends AbstractCustomEntity
{
    protected $name;
    protected $gln;
    protected $countryoforigin;
    protected $sn;
    
    public function getReferenceDataEntityType(): string
    {
        return AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT;
    }

    protected static function getClass(): string
    {
        return 'Manufacturer';
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getGln()
    {
        return $this->gln;
    }

    public function setGln($gln)
    {
        $this->gln = $gln;
    }

    public function getCountryOfOrigin()
    {
        return $this->countryoforigin;
    }

    public function setCountryOfOrigin($countryoforigin)
    {
        $this->countryoforigin = $countryoforigin;
    }

    public function getSn()
    {
        return $this->sn;
    }

    public function setSn($sn)
    {
        $this->sn = $sn;
    }
}