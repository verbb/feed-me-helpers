<?php
namespace Craft;

class VzAddressHelperPlugin extends BasePlugin
{
    // =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('Vz Address - Feed Me Helper');
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
		Craft::import('plugins.vzaddresshelper.integrations.feedme.fields.VzAddressFeedMeFieldType');
	}

	// =========================================================================== //
	// For compatibility with Feed Me plugin (v2.x)

	public function registerFeedMeFieldTypes()
	{
		return array(
			new VzAddressFeedMeFieldType(),
		);
	}
}
