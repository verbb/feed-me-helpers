<?php
namespace Craft;

class FocusPointPlugin extends BasePlugin
{
	public function init()
	{
		Craft::import('plugins.focuspoint.integrations.feedme.fields.FocusPoint_FocusPointFeedMeFieldType');
	}

	// =========================================================================== //
	// For compatibility with Feed Me plugin (v2.x)

	public function registerFeedMeFieldTypes()
	{
		return array(
            new FocusPoint_FocusPointFeedMeFieldType(),
		);
	}
}
