<?php
namespace Craft;

use Cake\Utility\Hash as Hash;

class VzAddressFeedMeFieldType extends BaseFeedMeFieldType
{
    // Templates
    // =========================================================================

    public function getMappingTemplate()
    {
        return 'vzaddresshelper/_integrations/feedme/fields/vzaddress';
    }
    


    // Public Methods
    // =========================================================================

    public function prepFieldData($element, $field, $fieldData, $handle, $options)
    {
        // Initialize content array
        $content = array();

        $data = Hash::get($fieldData, 'data');

        foreach ($data as $subfieldHandle => $subfieldData) {
            // Set value to subfield of correct address array
            $content[$subfieldHandle] = Hash::get($subfieldData, 'data');
        }

        // Return data
        return $content;
    }
    
}