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

class Keyword extends Base
{
    protected $columnName = 'utm_term';

    protected $columnType = 'VARCHAR(255) NOT NULL';

    public function getName()
    {
        return Piwik::translate('CampaignDetailed_Keyword');
    }

    protected function configureSegments()
    {
        $segment = new Segment();
        $segment->setSegment('utm_term');
        $segment->setName('CampaignDetailed_Keyword');
        $segment->setAcceptedValues('Encoded%20Term, term');
        $this->addSegment($segment);
    }

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $information = $this->getCampaignInformationFromRequest($request);
        if (!empty($information['utm_term'])) {
            return substr($information['utm_term'], 0, 255);
        }

        return $information['utm_term'];
    }
}