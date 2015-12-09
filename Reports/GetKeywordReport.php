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
use Piwik\Plugins\CampaignDetailed\Columns\Keyword;

class GetKeywordReport extends Base
{
    protected function init()
    {
        parent::init();
        $this->dimension     = new Keyword();
        $this->name          = Piwik::translate('CampaignDetailed_Keyword');
        $this->documentation = Piwik::translate('CampaignDetailed_KeywordReportDocumentation');
        $this->order = 3;
        $this->metrics       = array('nb_visits','nb_uniq_visitors','sum_visit_length','nb_actions','max_actions','bounce_count');
        $this->menuTitle    = Piwik::translate('CampaignDetailed_Keyword');
        $this->widgetTitle  = 'CampaignDetailed_Keyword';
    }

    public function configureView(ViewDataTable $view)
    {
        $view->config->show_search = false;
        $view->config->show_exclude_low_population = false;
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
        $view->config->addTranslation('label', Piwik::translate('CampaignDetailed_Keyword'));
    }
}
