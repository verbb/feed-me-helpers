<?php
namespace Craft;

class SimpleMapPlugin extends BasePlugin
{
	public function init()
	{
		Craft::import('plugins.simplemap.integrations.feedme.fields.SimpleMap_MapFeedMeFieldType');
	}

	// =========================================================================== //
	// For compatibility with Feed Me plugin (v2.x)

	public function registerFeedMeFieldTypes()
	{
		return array(
			new SimpleMap_MapFeedMeFieldType(),
		);
	}
}
