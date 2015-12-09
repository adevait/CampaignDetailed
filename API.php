<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CampaignDetailed;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Archive;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Piwik;

/**
 * API for plugin CampaignDetailed
 *
 * @method static \Piwik\Plugins\CampaignDetailed\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Function for returning a data table based on the passed selection
     * Used to generate the reports for all utm parameters
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded   
     * @param  int|null     $idSubtable 
     * @return DataTable
     */
    private function getDataTable($name, $idSite, $period, $date, $segment, $expanded = false, $idSubtable = null) 
    {
        Piwik::checkUserHasViewAccess($idSite);
        $dataTable = Archive::createDataTableFromArchive($name, $idSite, $period, $date, $segment,$expanded,$idSubtable);
        return $dataTable;
    }
    
    /**
     * Function for returning metric report for a given time period
     * Used for the dashboard graphs
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment
     * @return DataTable
     */
    private function getNumeric($name, $idSite, $period, $date, $segment)
    {
        Piwik::checkUserHasViewAccess($idSite);
        $archive = Archive::build($idSite, $period, $date, $segment);
        return $archive->getDataTableFromNumeric($name);
    }

    /**
     * Function for returning the campaign report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded 
     * @return DataTable
     */
    public function getCampaignReport($idSite, $period, $date, $segment = false, $expanded = false)
    {
        return $this->getDataTable(Archiver::CAMPAIGN_RECORD_NAME,$idSite, $period, $date, $segment,$expanded);
    }

    /**
     * Function for returning the keyword report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded 
     * @return DataTable
     */
    public function getKeywordReport($idSite, $period, $date, $segment = false, $expanded = false)
    {
        return $this->getDataTable(Archiver::KEYWORD_RECORD_NAME,$idSite, $period, $date, $segment,$expanded);
    }

    /**
     * Function for returning the medium report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded 
     * @return DataTable
     */
    public function getMediumReport($idSite, $period, $date, $segment = false, $expanded = false)
    {
        return $this->getDataTable(Archiver::MEDIUM_RECORD_NAME,$idSite, $period, $date, $segment,$expanded);
    }

    /**
     * Function for returning the source report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded 
     * @return DataTable
     */
    public function getSourceReport($idSite, $period, $date, $segment = false, $expanded = false)
    {
        return $this->getDataTable(Archiver::SOURCE_RECORD_NAME,$idSite, $period, $date, $segment,$expanded);
    }

    /**
     * Function for returning the content report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @param  boolean      $expanded 
     * @return DataTable
     */
    public function getContentReport($idSite, $period, $date, $segment = false, $expanded = false)
    {
        return $this->getDataTable(Archiver::CONTENT_RECORD_NAME,$idSite, $period, $date, $segment,$expanded);
    }

    /**
     * Function for returning the distinct keyword report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @return DataTable
     */
    public function getNumberOfDistinctKeywords($idSite, $period, $date, $segment = false)
    {
        return $this->getNumeric(Archiver::METRIC_DISTINCT_KEYWORD_RECORD_NAME, $idSite, $period, $date, $segment);
    }

    /**
     * Function for returning the distinct campaign report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @return DataTable
     */
    public function getNumberOfDistinctCampaigns($idSite, $period, $date, $segment = false)
    {
        return $this->getNumeric(Archiver::METRIC_DISTINCT_CAMPAIGN_RECORD_NAME, $idSite, $period, $date, $segment);
    }

    /**
     * Function for returning the distinct medium report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @return DataTable
     */
    public function getNumberOfDistinctMediums($idSite, $period, $date, $segment = false)
    {
        return $this->getNumeric(Archiver::METRIC_DISTINCT_MEDIUM_RECORD_NAME, $idSite, $period, $date, $segment);
    }

    /**
     * Function for returning the dostinct source report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @return DataTable
     */
    public function getNumberOfDistinctSources($idSite, $period, $date, $segment = false)
    {
        return $this->getNumeric(Archiver::METRIC_DISTINCT_SOURCE_RECORD_NAME, $idSite, $period, $date, $segment);
    }

    /**
     * Function for returning the distinct content report
     * @param  string       $name       
     * @param  int          $idSite     
     * @param  string       $period     
     * @param  string|Date  $date       
     * @param  string       $segment    
     * @return DataTable
     */
    public function getNumberOfDistinctContent($idSite, $period, $date, $segment = false)
    {
        return $this->getNumeric(Archiver::METRIC_DISTINCT_CONTENT_RECORD_NAME, $idSite, $period, $date, $segment);
    }

}
