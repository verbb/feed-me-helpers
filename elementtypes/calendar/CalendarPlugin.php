<?php
namespace Craft;

class CalendarPlugin extends BasePlugin
{
    public function init()
    {
        Craft::import('plugins.calendar.integrations.feedme.elementtypes.Calendar_EventFeedMeElementType');
    }

    // =========================================================================== //
    // For compatibility with Feed Me plugin (v2.x)

    public function registerFeedMeElementTypes()
    {
        return array(
            new Calendar_EventFeedMeElementType(),
        );
    }
}
