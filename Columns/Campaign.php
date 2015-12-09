<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CampaignDetailed\Columns;

use Piwik\Piwik;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;
use Piwik\Plugins\CampaignDetailed\Segment;

class Campaign extends Base
{
    protected $columnName = 'utm_campaign';
    protected $columnType = 'VARCHAR(255) NULL';

    public function getName()
    {
        return Piwik::translate('CampaignDetailed_Campaign');
    }

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('utm_campaign');
        $segment->setName('CampaignDetailed_Campaign');
        $segment->setAcceptedValues('Encoded%20Campaign, campaign');
        $this->addSegment($segment);
    }

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $information = $this->getCampaignInformationFromRequest($request);
        if (!empty($information['utm_campaign'])) {
            return substr($information['utm_campaign'], 0, 255);
        }

        return $information['utm_campaign'];
    }
}
