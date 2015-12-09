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
use Piwik\Plugins\CampaignDetailed\Columns\Campaign;
use Piwik\View;

class GetCampaignReport extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = Piwik::translate('CampaignDetailed_CampaignReport');
        $this->dimension     = new Campaign();
        $this->documentation = Piwik::translate('CampaignDetailed_CampaignReportDocumentation');
        $this->order = 2;
        $this->metrics       = array('nb_visits','nb_uniq_visitors','sum_visit_length','nb_actions','max_actions','bounce_count');
        $this->hasGoalMetrics = true;
        $this->menuTitle    = Piwik::translate('CampaignDetailed_Campaign');
        $this->widgetTitle  = 'CampaignDetailed_CampaignReport';
    }

    public function configureView(ViewDataTable $view)
    {
        $view->requestConfig->filter_limit = 25;
        if (!empty($this->dimension)) {
            $view->config->addTranslations(array('label' => $this->dimension->getName()));
        }
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
    }
}
