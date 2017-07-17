<?php
namespace Craft;

use Cake\Utility\Hash as Hash;

class Calendar_EventFeedMeElementType extends BaseFeedMeElementType
{
    // Templates
    // =========================================================================

    public function getGroupsTemplate()
    {
        return 'calendarhelper/_integrations/feedme/elementtypes/groups';
    }

    public function getColumnTemplate()
    {
        return 'calendarhelper/_integrations/feedme/elementtypes/column';
    }

    public function getMappingTemplate()
    {
        return 'calendarhelper/_integrations/feedme/elementtypes/map';
    }


    // Public Methods
    // =========================================================================

    public function getGroups()
    {
        return craft()->calendar_calendars->getAllAllowedCalendars();
    }

    public function setModel($settings)
    {
        $element = new Calendar_EventModel();
        $element->calendarId = $settings['elementGroup']['Calendar_Event'];

        if ($settings['locale']) {
            $element->locale = $settings['locale'];
        }

        return $element;
    }

    public function setCriteria($settings)
    {
        $criteria = craft()->elements->getCriteria(Calendar_EventModel::ELEMENT_TYPE);
        $criteria->status = null;
        $criteria->limit = null;
        $criteria->localeEnabled = null;

        $criteria->calendarId = $settings['elementGroup']['Calendar_Event'];

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
        foreach ($element as $elements) {
            craft()->calendar_events->deleteEventById($element->id);
        }
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
                case 'id';
                    $element->$handle = $dataValue;
                    break;
                case 'rrule';
                    $element->$handle = $dataValue;

                    // Because Calendars doesn't provide this parsing for us, we do it ourselves
                    $rules = $this->_parseRfcString($dataValue);

                    foreach ($rules as $ruleKey => $ruleValue) {
                        $modelHandle = strtolower($ruleKey);

                        switch ($rules) {
                            case 'bymonth':
                                $element->byMonth = $ruleValue;
                                break;
                            case 'byyearday':
                                $element->byYearDay = $ruleValue;
                                break;
                            case 'bymonthday':
                                $element->byMonthDay = $ruleValue;
                                break;
                            case 'byday':
                                $element->byDay = $ruleValue;
                                break;
                            default:
                                $element->setAttribute($modelHandle, $ruleValue);
                                break;
                        }
                    }

                    break;
                case 'authorId';
                    $element->$handle = $this->_prepareAuthorForElement($dataValue);
                    break;
                case 'slug';
                    $element->$handle = ElementHelper::createSlug($dataValue);
                    break;
                case 'startDate':
                case 'endDate';
                    $dateValue = $this->_prepareDateForElement($dataValue);

                    // Ensure there's a parsed data - null will auto-generate a new date
                    if ($dateValue) {
                        $element->$handle = $dateValue;
                    }

                    break;
                case 'enabled':
                case 'allDay':
                    $element->$handle = (bool)$dataValue;
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

        // Set default author if not set
        if (!$element->authorId) {
            $user = craft()->userSession->getUser();
            $element->authorId = ($element->authorId ? $element->authorId : ($user ? $user->id : 1));

            // Update the original data in our feed - for clarity in debugging
            $data['authorId'] = $element->authorId;
        }

        return $element;
    }

    public function save(BaseElementModel &$element, array $data, $settings)
    {
        // Put this back for now, until we can figure out a better solution with required fields
        $element->setContentFromPost($data);

        // Are we targeting a specific locale here? If so, we create an essentially blank element
        // for the primary locale, and instead create a locale for the targeted locale
        if (isset($settings['locale']) && $settings['locale']) {
            // Save the default locale element empty
            if (craft()->calendar_events->saveEvent($element)) {
                // Now get the successfully saved (empty) element, and set content on that instead
                $elementLocale = craft()->calendar_events->getEventById($element->id, $settings['locale']);
                $elementLocale->setContentFromPost($data);

                // Save the locale entry
                if (craft()->calendar_events->saveEvent($elementLocale)) {
                    return true;
                } else {
                    if ($elementLocale->getErrors()) {
                        throw new Exception(json_encode($elementLocale->getErrors()));
                    } else {
                        throw new Exception(Craft::t('Unknown Element error occurred.'));
                    }
                }
            } else {
                if ($element->getErrors()) {
                    throw new Exception(json_encode($element->getErrors()));
                } else {
                    throw new Exception(Craft::t('Unknown Element error occurred.'));
                }
            }

            return false;
        } else {
            return craft()->calendar_events->saveEvent($element);
        }
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

    private function _prepareAuthorForElement($author)
    {
        if (!is_numeric($author)) {
            $criteria = craft()->elements->getCriteria(ElementType::User);
            $criteria->search = $author;
            $authorUser = $criteria->first();
            
            if ($authorUser) {
                $author = $authorUser->id;
            } else {
                $user = craft()->users->getUserByUsernameOrEmail($author);
                $author = $user ? $user->id : 1;
            }
        }

        return $author;
    }

    private function _parseRfcString($string)
    {
        $parts = array();

        $string = trim($string);

        foreach ( explode("\n", $string) as $line ) {
            $line = trim($line);

            if ( strpos($line,':') === false ) {
                $property_name = 'RRULE';
                $property_value = $line;
            } else {
                list($property_name,$property_value) = explode(':',$line);
            }

            $tmp = explode(';',$property_name);
            $property_name = $tmp[0];
            $property_params = array();
            array_splice($tmp,0,1);

            foreach ( $tmp as $pair ) {
                list($key,$value) = explode('=',$pair);
                $property_params[$key] = $value;
            }

            foreach ( explode(';',$property_value) as $pair ) {
                list($key, $value) = explode('=', $pair);

                if ( $key === 'UNTIL' ) {
                    $value = new \DateTime($value);
                }

                $parts[$key] = $value;
            }
        }

        return $parts;
    }
}