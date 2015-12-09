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

class Content extends Base
{
    protected $columnName = 'utm_content';

    protected $columnType = 'VARCHAR(255) NOT NULL';

    public function getName()
    {
        return Piwik::translate('CampaignDetailed_Content');
    }

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('utm_content');
        $segment->setName('CampaignDetailed_Content');
        $segment->setAcceptedValues('Encoded%20Content, content');
        $this->addSegment($segment);
    }

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $information = $this->getCampaignInformationFromRequest($request);
        if (!empty($information['utm_content'])) {
            return substr($information['utm_content'], 0, 255);
        }

        return $information['utm_content'];
    }
}