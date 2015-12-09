<?php
namespace Piwik\Plugins\CampaignDetailed;

/**
 * Referrers segment base class.
 *
 */
class Segment extends \Piwik\Plugin\Segment
{
    protected  function init()
    {
        $this->setCategory('CampaignDetailed_Adwords');
    }
}
