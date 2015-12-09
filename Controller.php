<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CampaignDetailed;

use Piwik\View;
use Piwik\Common;
use Piwik\API\Request;
use Piwik\Translation\Translator;
use Piwik\Piwik;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Pie;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Bar;
use Piwik\ViewDataTable\Factory as ViewDataTableFactory;

class Controller extends \Piwik\Plugin\Controller
{
    public function __construct(Translator $translator)
    {
        parent::__construct();
    }

    /**
     * Generate the reports for the dashboard
     * @return View
     */
    public function index()
    {
        $view = new View('@CampaignDetailed/index');
        $view->graphEvolutionCampaigns = $this->getCampaignEvolutionGraph();
        $view->graphPieCampaigns = $this->getCampaignPieReport();
        $view->graphKeywords = $this->getKeywordPieReport();
        $view->graphMediums = $this->getMediumBarReport();
        $view->graphContents = $this->getContentBarReport();
        $view->graphSources = $this->getSourcePieReport();
        $distinctMetrics = $this->getDistinctUtmMetrics();
        foreach ($distinctMetrics as $name => $value) {
            $view->$name = $value;
        }
        $view->sparklineCampaign = $this->getUrlSparkline('getNumberOfDistinctCampaignSparkline');
        $view->sparklineKeyword = $this->getUrlSparkline('getNumberOfDistinctKeywordSparkline');
        $view->sparklineMedium = $this->getUrlSparkline('getNumberOfDistinctMediumSparkline');
        $view->sparklineContent = $this->getUrlSparkline('getNumberOfDistinctContentSparkline');
        $view->sparklineSource = $this->getUrlSparkline('getNumberOfDistinctSourceSparkline');
        return $view->render();
    }

    /**
     * Returns the distinct report values
     * @param  boolean|Date $date 
     * @return array
     */
    private function getDistinctUtmMetrics($date = false)
    {
        $propertyToAccessorMapping = array(
            'numberDistinctKeywords'     => 'getNumberOfDistinctKeywords',
            'numberDistinctCampaigns'    => 'getNumberOfDistinctCampaigns',
            'numberDistinctMediums'      => 'getNumberOfDistinctMediums',
            'numberDistinctContent'      => 'getNumberOfDistinctContent',
            'numberDistinctSources'      => 'getNumberOfDistinctSources',
        );

        $result = array();
        foreach ($propertyToAccessorMapping as $property => $method) {
            $result[$property] = $this->getNumericValue('CampaignDetailed.' . $method, $date);
        }
        return $result;
    }

    /**
     * Generates a graphic report based on the given parameters
     * @param  string $type             
     * @param  string $apiMethod        
     * @param  string $controllerMethod 
     * @param  array  $selectable       
     * @param  array  $to_display       
     * @return View                   
     */
    private function getReportGraph($type, $apiMethod, $controllerMethod, $selectable = array(), $to_display = array())
    {
        $view = ViewDataTableFactory::build(
            $type, $apiMethod, $controllerMethod, $forceDefault = true);
        $view->config->show_goals = false;
        if (empty($selectable)) {
            if (Common::getRequestVar('period', false) == 'day') {
                $selectable = array('nb_visits', 'nb_uniq_visitors', 'nb_actions');
            } else {
                $selectable = array('nb_visits', 'nb_actions');
            }
        }
        if (empty($to_display)) {
            $to_display = Common::getRequestVar('columns', false);
            if (false !== $to_display) {
                $to_display = Piwik::getArrayFromApiParameter($columns);
            }
        }
        if (false !== $to_display) {
            $to_display = !is_array($to_display) ? array($to_display) : $to_display;
        } else {
            $to_display = $selectable;
        }
        $view->config->selectable_columns = $selectable;
        $view->config->columns_to_display = $to_display;
        $view->config->show_footer_icons = false;
        return $this->renderView($view);
    }

    /**
     * Returns the campaign evolution graph
     * @return View
     */
    public function getCampaignEvolutionGraph()
    {
        return $this->getReportGraph(Evolution::ID, 'CampaignDetailed.getCampaignReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns campaign pie graph
     * @return View
     */
    public function getCampaignPieReport()
    {
        return $this->getReportGraph(Pie::ID, 'CampaignDetailed.getCampaignReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns keyword pie graph
     * @return View
     */
    public function getKeywordPieReport()
    {
        return $this->getReportGraph(Pie::ID, 'CampaignDetailed.getKeywordReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns medium bar graph
     * @return View
     */
    public function getMediumBarReport()
    {
        return $this->getReportGraph(Bar::ID, 'CampaignDetailed.getMediumReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns content bar graph
     * @return View
     */
    public function getContentBarReport()
    {
        return $this->getReportGraph(Bar::ID, 'CampaignDetailed.getContentReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns source pie report
     * @return View
     */
    public function getSourcePieReport()
    {
        return $this->getReportGraph(Pie::ID, 'CampaignDetailed.getSourceReport', $this->pluginName.'.'. __FUNCTION__);
    }

    /**
     * Returns distinct campaign evolution graph
     * Used for sparkline image
     * @return View
     */
    public function getNumberOfDistinctCampaignSparkline()
    {
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, "CampaignDetailed.getNumberOfDistinctCampaigns");
        return $this->renderView($view);
    }

    /**
     * Returns distinct keyword evolution graph
     * Used for sparkline image
     * @return View
     */
    public function getNumberOfDistinctKeywordSparkline()
    {
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, "CampaignDetailed.getNumberOfDistinctKeywords");
        return $this->renderView($view);
    }

    /**
     * Returns distinct medium evolution graph
     * Used for sparkline image
     * @return View
     */
    public function getNumberOfDistinctMediumSparkline()
    {
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, "CampaignDetailed.getNumberOfDistinctMediums");
        return $this->renderView($view);
    }

    /**
     * Returns distinct source evolution graph
     * Used for sparkline image
     * @return View
     */
    public function getNumberOfDistinctSourceSparkline()
    {
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, "CampaignDetailed.getNumberOfDistinctSources");
        return $this->renderView($view);
    }

    /**
     * Return distinct content evolution graph
     * Used for sparkline image
     * @return View
     */
    public function getNumberOfDistinctContentSparkline()
    {
        $view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, "CampaignDetailed.getNumberOfDistinctContent");
        return $this->renderView($view);
    }
}
