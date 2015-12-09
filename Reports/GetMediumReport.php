<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CampaignDetailed\Reports;

use Piwik\Piwik;
use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\CampaignDetailed\Columns\Medium;

class GetMediumReport extends Base
{
    protected function init()
    {
        parent::init();
        $this->dimension     = new Medium();
        $this->name          = Piwik::translate('CampaignDetailed_Medium');
        $this->documentation = Piwik::translate('CampaignDetailed_MediumReportDocumentation');
        $this->metrics       = array('nb_visits','nb_uniq_visitors','sum_visit_length','nb_actions','max_actions','bounce_count');
        $this->order = 4;
        $this->menuTitle    = Piwik::translate('CampaignDetailed_Medium');
        $this->widgetTitle  = 'CampaignDetailed_Medium';
    }

    public function configureView(ViewDataTable $view)
    {
        $view->config->show_search = false;
        $view->config->show_exclude_low_population = false;
        $view->config->addTranslation('label', $this->dimension->getName());
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
    }


}
