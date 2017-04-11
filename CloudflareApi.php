<?php
/**
 * @version 0.0.1
 * @author Biozahard
 * @link https://github.com/biozahard/yii2-cloudflare
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace biozahard\cloudflare;

use yii\base\Component;

/**
 * Class CloudflareApi
 * @package biozahard\cloudflare
 *
 * @property mixed $listZones
 * @property array $activeZones
 */
class CloudflareApi extends Component
{
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_PATCH  = 'PATCH';
    const HTTP_METHOD_DELETE = 'DELETE';
    public $apiurl = 'https://api.cloudflare.com/client/v4/';
    public $authkey;
    public $authemail;
    public $sites;

    /**
     * @return mixed
     */
    public function getListZones()
    {
        return $this->makeRequest('zones');
    }

    /**
     * Performs request to the server and gets the answer
     *
     * @param   string $sURL
     * @param   array $aData
     * @param   string $httpMethod
     *
     * @return  mixed
     */
    private function makeRequest($sURL, $aData = [], $httpMethod = self::HTTP_METHOD_GET)
    {
        $aFields = [
            'query'    => json_encode($aData),
            'status'   => 0,
            'response' => '',
        ];

        $rCURL = curl_init();
        curl_setopt($rCURL, CURLOPT_URL, "{$this->apiendpoint}{$sURL}");
        curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rCURL, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($rCURL, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($rCURL, CURLOPT_CUSTOMREQUEST, $httpMethod);

        if (!empty($aData)) {
            if ($httpMethod == self::HTTP_METHOD_GET) {
                $sQueryParams = http_build_query($aData);
                curl_setopt($rCURL, CURLOPT_URL, "{$this->apiendpoint}{$sURL}?$sQueryParams");
            } else {
                curl_setopt($rCURL, CURLOPT_POSTFIELDS, json_encode($aData));
            }
        }

        curl_setopt($rCURL, CURLOPT_HTTPHEADER, [
            'X-Auth-Email: ' . $this->authemail,
            'X-Auth-Key: ' . $this->authkey,
            'Content-Type: application/json',
        ]);

        $sResponse = $aFields['response'] = curl_exec($rCURL);
        curl_close($rCURL);
        $aResponse = json_decode($sResponse, true);

        return $aResponse;
    }

    /**
     * Clear the cache for the specified zone
     *
     * @param string $site domain name of zone, without 'http://' and 'www.'
     *
     * @return mixed
     */
    public function purgeCache($site = '')
    {
        $url       = 'purge_cache';
        $zonesList = $this->getActiveZones();
        if ($site === '') {
            $site = $this->sites[0];
        }
        $url = isset($zonesList[$site]) ? $zonesList[$site] . '/' . $url : $url;

        return $this->makeRequest('zones/' . $url, [
            'purge_everything' => true,
        ], self::HTTP_METHOD_DELETE);
    }

    /**
     * @return array
     */
    private function getActiveZones()
    {
        $result = [];
        $list   = $this->makeRequest('zones');
        foreach ($list['result'] as $item) {
            if ($item['status'] == 'active') {
                $result[$item['name']] = $item['id'];
            }
        }

        return $result;
    }
}
