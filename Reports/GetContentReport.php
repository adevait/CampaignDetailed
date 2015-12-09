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
use Piwik\Plugins\CampaignDetailed\Columns\Content;
use Piwik\View;

class GetContentReport extends Base
{
    protected function init()
    {
        parent::init();

        $this->dimension     = new Content();
        $this->name          = Piwik::translate('CampaignDetailed_ContentReport');
        $this->documentation = Piwik::translate('CampaignDetailed_ContentReportDocumentation');
        $this->order = 6;
        $this->metrics       = array('nb_visits','nb_uniq_visitors','sum_visit_length','nb_actions','max_actions','bounce_count');
        $this->menuTitle    = Piwik::translate('CampaignDetailed_Content');
        $this->widgetTitle  = 'CampaignDetailed_Content';
    }

    public function configureView(ViewDataTable $view)
    {
        if (!empty($this->dimension)) {
            $view->config->addTranslations(array('label' => $this->dimension->getName()));
        }
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);
    }
}
