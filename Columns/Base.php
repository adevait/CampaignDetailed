<?php 
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CampaignDetailed\Columns;

use Piwik\Tracker\Request;
use Piwik\Tracker\PageUrl;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Tracker\Visitor;
use Piwik\Common;
use Piwik\UrlHelper;

abstract class Base extends VisitDimension
{
    protected $currentUrlParse;
    private static $cachedReferrer = array();
    
    /**
     * Returns the extracted utm details from the url
     * @param  string  $currentUrl 
     * @param  int     $idSite     
     * @param  Request $request    
     * @return array            
     */
    private function getCampaignInformation($currentUrl, $idSite, Request $request)
    {
        $cacheKey = $currentUrl . $idSite;
        if (isset(self::$cachedReferrer[$cacheKey])) {
            return self::$cachedReferrer[$cacheKey];
        }

        $currentUrl = PageUrl::cleanupUrl($currentUrl);
        $this->currentUrlParse = @parse_url($currentUrl);
        $utmInformation = $this->extractUtmDetailsFromUrl();

        self::$cachedReferrer[$cacheKey] = $utmInformation;
        return $utmInformation;
    }

    /**
     * Returns the campaign information, called from the child classes
     * @param  Request $request 
     * @return array           
     */
    protected function getCampaignInformationFromRequest(Request $request)
    {
        $currentUrl  = $request->getParam('url');
        return $this->getCampaignInformation($currentUrl, $request->getIdSite(), $request);
    }

    /**
     * Extracts the utm query parameters into array
     * @return array 
     */
    private function extractUtmDetailsFromUrl()
    {
        $query = $this->currentUrlParse['query'];
        $params = UrlHelper::getArrayFromQueryString($query);
        return array('utm_campaign' => (isset($params['utm_campaign']) ? $params['utm_campaign'] : ''),
                     'utm_term'     => (isset($params['utm_term']) ? $params['utm_term'] : ''),
                     'utm_medium'   => (isset($params['utm_medium']) ? $params['utm_medium'] : ''),
                     'utm_content'  => (isset($params['utm_content']) ? $params['utm_content'] : ''),
                     'utm_source'   => (isset($params['utm_source']) ? $params['utm_source'] : ''));
    }
}
