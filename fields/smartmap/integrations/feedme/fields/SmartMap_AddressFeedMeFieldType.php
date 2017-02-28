<?php
namespace Craft;

class SmartMap_AddressFeedMeFieldType extends BaseFeedMeFieldType
{
    // Templates
    // =========================================================================

    public function getMappingTemplate()
    {
        return 'smartmap/_integrations/feedme/fields/smartmap_address';
    }
    


    // Public Methods
    // =========================================================================

    public function prepFieldData($element, $field, $fieldData, $handle, $options)
    {
        // Initialize content array
        $content = array();

        $data = $fieldData['data'];

        foreach ($data as $subfieldHandle => $subfieldData) {
            // Set value to subfield of correct address array
            $content[$subfieldHandle] = $subfieldData['data'];
        }

        // Return data
        return $content;
    }
    
}