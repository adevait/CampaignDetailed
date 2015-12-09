<?php 

namespace Piwik\Plugins\CampaignDetailed;

use Piwik\DataTable;
use Piwik\Metrics;
use Piwik\DataTable\Row;
use Piwik\DataArray;
use Piwik\Config;

class Archiver extends \Piwik\Plugin\Archiver
{
    const CAMPAIGN_RECORD_NAME = 'CampaignDetailed_Campaign';
    const KEYWORD_RECORD_NAME = 'CampaignDetailed_Term';
    const MEDIUM_RECORD_NAME = 'CampaignDetailed_Medium';
    const SOURCE_RECORD_NAME = 'CampaignDetailed_Source';
    const CONTENT_RECORD_NAME = 'CampaignDetailed_Content';
    const METRIC_DISTINCT_CAMPAIGN_RECORD_NAME = 'CampaignDetailed_distinctCampaign';
    const METRIC_DISTINCT_KEYWORD_RECORD_NAME = 'CampaignDetailed_distinctTerm';
    const METRIC_DISTINCT_MEDIUM_RECORD_NAME = 'CampaignDetailed_distinctMedium';
    const METRIC_DISTINCT_SOURCE_RECORD_NAME = 'CampaignDetailed_distinctSource';
    const METRIC_DISTINCT_CONTENT_RECORD_NAME = 'CampaignDetailed_distinctContent';
  
    protected $arrays = array();
    protected $columnToSortByBeforeTruncation;
    protected $maximumRowsInDataTableLevelZero;
    protected $maximumRowsInSubDataTable;

    public function __construct($processor)
    {
        parent::__construct($processor);
        $this->columnToSortByBeforeTruncation = Metrics::INDEX_NB_VISITS;
        $this->maximumRowsInDataTableLevelZero = @Config::getInstance()->General['datatable_archiving_maximum_rows_referers'];
        $this->maximumRowsInSubDataTable = @Config::getInstance()->General['datatable_archiving_maximum_rows_subtable_referers'];
        if (empty($this->maximumRowsInDataTableLevelZero)) {
            $this->maximumRowsInDataTableLevelZero = Config::getInstance()->General['datatable_archiving_maximum_rows_referrers'];
            $this->maximumRowsInSubDataTable = Config::getInstance()->General['datatable_archiving_maximum_rows_subtable_referrers'];
        }
    }

    public function aggregateDayReport()
    {
        foreach ($this->getRecordNames() as $record) {
            $this->arrays[$record] = new DataArray();
        }
        $this->aggregateFromVisits(array("utm_campaign", "utm_term", "utm_medium", "utm_content", "utm_source"));
        $this->insertDayReports();
    }

    public function aggregateMultipleReports()
    {
        $dataTableToSum = $this->getRecordNames();
        $columnsAggregationOperation = null;
        $nameToCount = $this->getProcessor()->aggregateDataTableRecords(
            $dataTableToSum,
            $this->maximumRowsInDataTableLevelZero,
            $this->maximumRowsInSubDataTable,
            $this->columnToSortByBeforeTruncation,
            $columnsAggregationOperation,
            $columnsToRenameAfterAggregation = array('nb_uniq_visitors'=>'sum_daily_nb_uniq_visitors'),
            $countRowsRecursive = null
        );

        $mappingFromArchiveName = array(
            self::METRIC_DISTINCT_CAMPAIGN_RECORD_NAME =>
                array('typeCountToUse' => 'level0',
                      'nameTableToUse' => self::CAMPAIGN_RECORD_NAME,
                ),
            self::METRIC_DISTINCT_KEYWORD_RECORD_NAME       =>
                array('typeCountToUse' => 'level0',
                      'nameTableToUse' => self::KEYWORD_RECORD_NAME,
                ),
            self::METRIC_DISTINCT_MEDIUM_RECORD_NAME      =>
                array('typeCountToUse' => 'level0',
                      'nameTableToUse' => self::MEDIUM_RECORD_NAME,
                ),
            self::METRIC_DISTINCT_SOURCE_RECORD_NAME      =>
                array('typeCountToUse' => 'level0',
                      'nameTableToUse' => self::SOURCE_RECORD_NAME,
                ),
            self::METRIC_DISTINCT_CONTENT_RECORD_NAME         =>
                array('typeCountToUse' => 'level0',
                      'nameTableToUse' => self::CONTENT_RECORD_NAME,
                ),
        );

        foreach ($mappingFromArchiveName as $name => $infoMapping) {
            $nameTableToUse = $infoMapping['nameTableToUse'];
            $countValue = $nameToCount[$nameTableToUse]['level0'];
            $this->getProcessor()->insertNumericRecord($name, $countValue);
        }
    }

    /**
     * Returns an array of all report names
     * @return array
     */
    protected function getRecordNames()
    {
        return array(
          self::CAMPAIGN_RECORD_NAME,
          self::KEYWORD_RECORD_NAME,
          self::MEDIUM_RECORD_NAME,
          self::SOURCE_RECORD_NAME,
          self::CONTENT_RECORD_NAME
      );
    }

    /**
     * [aggregateVisitRow description]
     * @param  [type] $row [description]
     * @return [type]      [description]
     */
    protected function aggregateVisitRow($row)
    {
        $this->getDataArray(self::CAMPAIGN_RECORD_NAME)->sumMetricsVisits($row['utm_campaign'], $row);
        $this->getDataArray(self::KEYWORD_RECORD_NAME)->sumMetricsVisits($row['utm_term'], $row);
        $this->getDataArray(self::MEDIUM_RECORD_NAME)->sumMetricsVisits($row['utm_medium'], $row);
        $this->getDataArray(self::SOURCE_RECORD_NAME)->sumMetricsVisits($row['utm_source'], $row);
        $this->getDataArray(self::CONTENT_RECORD_NAME)->sumMetricsVisits($row['utm_content'], $row);
    }

    /**
     * @param  string $name 
     * @return DataTable
     */
    protected function getDataArray($name)
    {
        return $this->arrays[$name];
    }

    /**
     * Aggregate data from visits
     * @param  array $fields 
     */
    private function aggregateFromVisits($fields)
    {
        $query = $this->getLogAggregator()->queryVisitsByDimension($fields);
        while ($row = $query->fetch()) {
            $this->aggregateVisitRow($row);
        }
    }

    /**
     * Records the daily stats into the archive tables.
    */
    protected function insertDayReports()
    {
        $this->insertDayNumericMetrics();
        foreach ($this->getRecordNames() as $recordName) {
            $blob = $this->getDataArray($recordName)->asDataTable()->getSerialized($this->maximumRowsInDataTableLevelZero, $this->maximumRowsInSubDataTable, $this->columnToSortByBeforeTruncation);
            $this->getProcessor()->insertBlobRecord($recordName, $blob);
        }
    }

    /**
     * Record the numeric data for the reports
     */
    protected function insertDayNumericMetrics()
    {
        $numericRecords = array(
            self::METRIC_DISTINCT_CAMPAIGN_RECORD_NAME => count($this->getDataArray(self::CAMPAIGN_RECORD_NAME)->getDataArray()),
            self::METRIC_DISTINCT_KEYWORD_RECORD_NAME  => count($this->getDataArray(self::KEYWORD_RECORD_NAME)->getDataArray()),
            self::METRIC_DISTINCT_MEDIUM_RECORD_NAME   => count($this->getDataArray(self::MEDIUM_RECORD_NAME)->getDataArray()),
            self::METRIC_DISTINCT_SOURCE_RECORD_NAME   => count($this->getDataArray(self::SOURCE_RECORD_NAME)->getDataArray()),
            self::METRIC_DISTINCT_CONTENT_RECORD_NAME  => count($this->getDataArray(self::CONTENT_RECORD_NAME)->getDataArray()),
        );

        $this->getProcessor()->insertNumericRecords($numericRecords);
    }
}
