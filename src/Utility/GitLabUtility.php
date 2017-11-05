<?php
declare(strict_types=1);

namespace Z3\T3build\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as HttpRequest;

use GuzzleHttp\Psr7\Uri;
use Z3\T3build\Service\Config;

class GitLabUtility
{

    /**
     * @param string $token
     * @param string $projectName
     * @param int $issueNumber
     * @return string
     */
    public static function getIssueTitle(string $token, string $projectName, int $issueNumber): string
    {
        $url = 'https://git.z3.ag/api/v4/projects/' . urlencode($projectName) . '/issues/' . $issueNumber;
        $issue = self::getData($token, $url);
        return $issue['title'];
    }

    /**
     * @param string $host
     * @return string
     */
    public static function getGitLabToken(string $host): string
    {
        return Config::getUserConfiguration()->getConfigurationString('git/gitlab/' . $host . '/token', '');
    }

    /**
     * @param string $token
     * @param string $url
     * @param array $queryParameters
     * @return array
     */
    private static function getData(string $token, string $url, array $queryParameters = []): array
    {
        $uri = new Uri($url);
        foreach ($queryParameters as $key => $value) {
            $uri = Uri::withQueryValue($uri, $key, $value);
        }

        $client = new Client();

        $request = new HttpRequest('GET', $uri, [
            'PRIVATE-TOKEN' => $token,
            'Accept' => 'application/json',
        ]);

        $response = $client->send($request);
        return  \GuzzleHttp\json_decode($response->getBody(), true);
    }

    /**
     * @param string $token
     * @param string $projectName
     * @param string $key
     * @param string $value
     */
    public static function writeVariable(string $token, string $projectName, string $key, string $value, bool $protected, string $environment)
    {
        $url = 'https://git.z3.ag/api/v4/projects/' . urlencode($projectName) . '/variables';

        $client = new Client();
        $uri = new Uri($url);

        $variableArray['key'] = $key;
        $variableArray['value'] = $value;
        $variableArray['protected'] = $protected;
        $variableArray['protected'] = $protected;
        $variableArray['environment_scope'] = $environment;

        $request = new HttpRequest('POST', $uri, [
            'PRIVATE-TOKEN' => $token,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);

        $body = \GuzzleHttp\Psr7\stream_for(http_build_query($variableArray));
        $request = $request->withBody($body);

        $client->send($request);
    }
}
