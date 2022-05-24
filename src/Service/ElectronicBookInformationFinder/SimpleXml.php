<?php

namespace App\Service\ElectronicBookInformationFinder;

trait SimpleXml
{
    protected function applyNamespace(\SimpleXMLElement $simpleXMLElement, string $namespace): \SimpleXMLElement
    {
        foreach ($simpleXMLElement->getDocNamespaces() as $strPrefix => $strNamespace) {
            if (strlen($strPrefix) === 0) {
                $strPrefix = $namespace;
            }
            $simpleXMLElement->registerXPathNamespace($strPrefix, $strNamespace);
        }
        return $simpleXMLElement;
    }
}
