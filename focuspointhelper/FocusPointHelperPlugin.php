<?php
namespace Craft;

class FocusPointHelperPlugin extends BasePlugin
{
	// =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('Focus Point - Feed Me Helper');
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper()
    {
        return 'S. Group';
    }

    public function getDeveloperUrl()
    {
        return 'http://sgroup.com.au';
    }

    public function getPluginUrl()
    {
        return 'https://github.com/engram-design/FeedMe-Helpers';
    }

	public function init()
	{
		Craft::import('plugins.focuspointhelper.integrations.feedme.fields.FocusPoint_FocusPointFeedMeFieldType');
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
