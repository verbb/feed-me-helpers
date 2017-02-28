<?php
namespace Craft;

class SmartMapPlugin extends BasePlugin
{
	public function init()
	{
		Craft::import('plugins.smartmap.integrations.feedme.fields.SmartMap_AddressFeedMeFieldType');
	}

	// =========================================================================== //
	// For compatibility with Feed Me plugin (v2.0.0)

	public function registerFeedMeFieldTypes()
	{
		return array(
			new SmartMap_AddressFeedMeFieldType(),
		);
	}
}
