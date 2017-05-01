<?php
namespace Craft;

/**
 * Variable class.
 *
 * @author    Argentum Webware
 * @copyright Argentum Webware
 */
class DigitalProductsHelperVariable
{

    // Digital Products doesn't have a getProductTypeById() function
    public function getProductTypeById($productTypeId)
    {
        return craft()->digitalProducts_productTypes->getProductTypeById($productTypeId);
    }

}
