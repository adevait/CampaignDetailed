<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CampaignDetailed\Reports;

use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;

use Piwik\View;

class GetGeneralOverview extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = Piwik::translate('CampaignDetailed_GeneralOverview');
        $this->dimension     = null;
        $this->documentation = Piwik::translate('CampaignDetailed_OverviewReportDocumentation');
        $this->order = 1;
        $this->widgetTitle  = 'CampaignDetailed_GeneralOverview';
    }

    public function configureView(ViewDataTable $view)
    {
        if (!empty($this->dimension)) {
            $view->config->addTranslations(array('label' => $this->dimension->getName()));
        }
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
    }
}
