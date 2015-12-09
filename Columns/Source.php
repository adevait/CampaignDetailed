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
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;
use Piwik\Plugins\CampaignDetailed\Segment;

class Source extends Base
{
    protected $columnName = 'utm_source';

    protected $columnType = 'VARCHAR(255) NOT NULL';

    public function getName()
    {
        return Piwik::translate('CampaignDetailed_Source');
    }

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('utm_source');
        $segment->setName('CampaignDetailed_Source');
        $segment->setAcceptedValues('Encoded%20Source, source');
        $this->addSegment($segment);
    }

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $information = $this->getCampaignInformationFromRequest($request);
        if (!empty($information['utm_source'])) {
            return substr($information['utm_source'], 0, 255);
        }

        return $information['utm_source'];
    }
}