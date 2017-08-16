<?php
namespace Craft;

class CommentsHelperPlugin extends BasePlugin
{
    // =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('Comments - Feed Me Helper');
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
        Craft::import('plugins.commentshelper.integrations.feedme.elementtypes.Comments_CommentFeedMeElementType');
    }

    // =========================================================================== //
    // For compatibility with Feed Me plugin (v2.x)

    public function registerFeedMeElementTypes()
    {
        return array(
            new Comments_CommentFeedMeElementType(),
        );
    }
}
