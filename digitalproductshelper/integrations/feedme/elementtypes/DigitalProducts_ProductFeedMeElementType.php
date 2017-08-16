<?php
namespace Craft;

use Cake\Utility\Hash as Hash;

class DigitalProducts_ProductFeedMeElementType extends BaseFeedMeElementType
{
    // Templates
    // =========================================================================

    public function getGroupsTemplate()
    {
        return 'digitalproductshelper/_integrations/feedme/elementtypes/groups';
    }

    public function getColumnTemplate()
    {
        return 'digitalproductshelper/_integrations/feedme/elementtypes/column';
    }

    public function getMappingTemplate()
    {
        return 'digitalproductshelper/_integrations/feedme/elementtypes/map';
    }


    // Public Methods
    // =========================================================================

    public function getGroups()
    {
        return craft()->digitalProducts_productTypes->getEditableProductTypes();
    }

    public function setModel($settings)
    {
        $element = new DigitalProducts_ProductModel();
        $element->typeId = $settings['elementGroup']['DigitalProducts_Product'];

        if ($settings['locale']) {
            $element->locale = $settings['locale'];
        }

        return $element;
    }

    public function setCriteria($settings)
    {
        $criteria = craft()->elements->getCriteria('DigitalProducts_Product');
        $criteria->status = null;
        $criteria->limit = null;
        $criteria->localeEnabled = null;

        $criteria->typeId = $settings['elementGroup']['DigitalProducts_Product'];

        if ($settings['locale']) {
            $criteria->locale = $settings['locale'];
        }

        return $criteria;
    }

    public function matchExistingElement(&$criteria, $data, $settings)
    {
        foreach ($settings['fieldUnique'] as $handle => $value) {
            if ((int)$value === 1) {
                $feedValue = Hash::get($data, $handle . '.data', $data[$handle]);

                if ($feedValue) {
                    $criteria->$handle = DbHelper::escapeParam($feedValue);
                }
            }
        }

        // Check to see if an element already exists - interestingly, find()[0] is faster than first()
        $elements = $criteria->find();

        if (count($elements)) {
            return $elements[0];
        }

        return null;
    }

    public function delete(array $elements)
    {
        $return = true;

        foreach ($elements as $element) {
            if (!craft()->digitalProducts_products->deleteProduct($element)) {
                $return = false;
            }
        }

        return $return;
    }

    public function prepForElementModel(BaseElementModel $element, array &$data, $settings)
    {
        foreach ($data as $handle => $value) {
            if (is_null($value)) {
                continue;
            }

            if (isset($value['data']) && $value['data'] === null) {
                continue;
            }

            if (is_array($value)) {
                $dataValue = Hash::get($value, 'data', $value);
            } else {
                $dataValue = $value;
            }
            
            switch ($handle) {
                case 'id':
                case 'taxCategoryId':
                case 'price':
                case 'sku':
                    $element->$handle = $dataValue;
                    break;
                case 'slug':
                    $element->$handle = ElementHelper::createSlug($dataValue);
                    break;
                case 'postDate':
                case 'expiryDate':
                    $element->$handle = $this->_prepareDateForElement($dataValue);
                    break;
                case 'enabled':
                case 'freeShipping':
                case 'promotable':
                    $element->$handle = FeedMeHelper::parseBoolean($dataValue);
                    break;
                case 'title':
                    $element->getContent()->$handle = $dataValue;
                    break;
                default:
                    continue 2;
            }

            // Update the original data in our feed - for clarity in debugging
            $data[$handle] = $element->$handle;
        }

        //$this->_populateProductVariantModels($element, $data, $settings);

        return $element;
    }

    public function save(BaseElementModel &$element, array $data, $settings)
    {
        // Are we targeting a specific locale here? If so, we create an essentially blank element
        // for the primary locale, and instead create a locale for the targeted locale
        if (isset($settings['locale']) && $settings['locale']) {
            // Save the default locale element empty
            $result = craft()->digitalProducts_products->saveProduct($element);

            if ($result) {
                // Now get the successfully saved (empty) element, and set content on that instead
                $elementLocale = craft()->digitalProducts_products->getProductById($element->id, $settings['locale']);
                $elementLocale->setContentFromPost($data);

                // Save the locale entry
                $result = craft()->digitalProducts_products->saveProduct($elementLocale);
            }
        } else {
            $result = craft()->digitalProducts_products->saveProduct($element);
        }

        // Because we can have product and variant error, make sure we show them
        if (!$result) {
            if ($element->getErrors()) {
                throw new Exception(json_encode($element->getErrors()));
            }
        }

        return $result;
    }

    public function afterSave(BaseElementModel $element, array $data, $settings)
    {

    }


    // Private Methods
    // =========================================================================

    private function _prepareDateForElement($date)
    {
        $craftDate = null;

        if (!is_array($date)) {
            $d = date_parse($date);
            $date_string = date('Y-m-d H:i:s', mktime($d['hour'], $d['minute'], $d['second'], $d['month'], $d['day'], $d['year']));

            $craftDate = DateTime::createFromString($date_string, craft()->timezone);
        } else {
            $craftDate = $date;
        }

        return $craftDate;
    }

}