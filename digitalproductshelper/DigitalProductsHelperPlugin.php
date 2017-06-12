<?php
namespace Craft;

class DigitalProductsHelperPlugin extends BasePlugin
{
    // =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('Digital Products - Feed Me Helper');
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
        return 'Argentum Webware';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.argentumwebware.com';
    }

    public function getPluginUrl()
    {
        return 'https://github.com/engram-design/FeedMe-Helpers';
    }

    public function init()
    {
        Craft::import('plugins.digitalproductshelper.integrations.feedme.elementtypes.DigitalProducts_ProductFeedMeElementType');
    }

    // =========================================================================== //
    // For compatibility with Feed Me plugin (v2.x)

    public function registerFeedMeElementTypes()
    {
        return array(
            new DigitalProducts_ProductFeedMeElementType(),
        );
    }
}
