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

class Medium extends Base
{
    protected $columnName = 'utm_medium';
    protected $columnType = 'VARCHAR(255) NULL';

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('utm_medium');
        $segment->setName('CampaignDetailed_Medium');
        $segment->setAcceptedValues('Encoded%20Medium, medium');
        $this->addSegment($segment);
    }

    public function getName()
    {
        return Piwik::translate('CampaignDetailed_Medium');
    }

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
    	$information = $this->getCampaignInformationFromRequest($request);
        if (!empty($information['utm_medium'])) {
            return substr($information['utm_medium'], 0, 255);
        }

        return $information['utm_medium'];
    }
}
