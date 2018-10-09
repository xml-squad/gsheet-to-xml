<?php

namespace XmlSquad\GsheetXml\Model\Domain;

use XmlSquad\GsheetXml\Model\Domain\DomainGSheetObjectFactoryInterface;

class InventoryFactory implements DomainGSheetObjectFactoryInterface
{

    /**
     * @var array The headers that we care about in the target sheets.
     */
    protected $targettedHeadingValues = [
        'Name',
        'KNumberExists',
        'KNumber',
        'Quantity',
        'AlternativeNumber',
        'Purpose',
        'PurposeOther',
        ];

    /**
     * Implements the process of making the object that is represented by a GSheet. In this case, an Inventory.
     *
     *
     * @param array $data
     * @param string $spreadsheetUrl
     * @return Inventory
     */
    public function createDomainGSheetObject(array $data, string $spreadsheetUrl)
    {
        //Delegate to the concrete class for specific implementation.
        return $this->doCreateDomainGSheetObject($data, $spreadsheetUrl);
    }

    /**
     * @return string Column identity of maximum range to get from sheet.
     */
    public function getColumnRangeLimit(){
        return 'ZZ';
    }


    /**
     * @param array|null $row
     * @param \XmlSquad\GsheetXml\Model\Domain\DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory
     * @return bool
     */
    public function isHeadingsRow(?array $row): bool
    {
        return $this->isAllHeadingValuesPresentInRow($row);
    }

    protected function isAllHeadingValuesPresentInRow(?array $row){

        if (true === empty($row)) {
            return false;
        }

        foreach($this->targettedHeadingValues as $headerValue){

            if (false === in_array(trim($headerValue), $row)) {
                //print($headerValue . ' not in ' . print_r($row,true));
                return false;
            }

        }

        return true;
    }

    /**
     * Returns true if string provided matches one of the header values of the Google Sheet that we are interested in.
     *
     * @param string $value
     * @return bool
     */
    public function isTargettedHeadingValue(string $value){


        if (true === in_array(trim($value), $this->targettedHeadingValues)) {
            return true;
        }

        return false;

    }


    /**
     *
     *
     *
     * @see https://en.wikipedia.org/wiki/Template_method_pattern
     *
     * @param array $data
     * @param string $spreadsheetUrl
     * @return Inventory
     */
    protected function doCreateDomainGSheetObject(array $data, string $spreadsheetUrl): Inventory {
        $inventory = new Inventory();
        $inventory->setSpreadsheetUrl($spreadsheetUrl);
        $inventory->setSheetName($data['sheetTitle'] ?? null);
        $inventory->setSpreadsheetName($data['spreadsheetTitle'] ?? null);

        $stockItems = $this->processStockItems($data);
        $inventory->setStockItems($stockItems);

        return $inventory;
    }


    private function processStockItems(array $data)
    {
        $stockItems = [];

        if (false === isset($data['values']) || true === empty($data['values'])) {
            return $stockItems;
        }

        foreach ($data['values'] as $rowData) {
            $stockItem = new StockItem();

            foreach ($rowData as $heading => $value) {
                $heading = trim($heading);
                if ($heading === 'Name') {
                    $stockItem->setName($value);
                }

                if ($heading === 'KNumber') {
                    $stockItem->setKNumber($value);
                }

                if ($heading === 'KNumberExists') {
                    $stockItem->setKNumberExists($value);
                }

                if ($heading === 'Quantity') {
                    $stockItem->setInventoryQuantity($value);
                }

                if ($heading === 'AlternativeNumber') {
                    $stockItem->setAlternativeNumber($value);
                }

                if ($heading === 'Purpose') {
                    $stockItem->setPurpose($value);
                }

                if ($heading === 'PurposeOther') {
                    $stockItem->setPurposeOther($value);
                }
            }

            $stockItems[] = $stockItem;
        }

        return $stockItems;
    }

}