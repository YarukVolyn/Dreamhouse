<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class Parser.
 */
class OlxParser
{
    /**
     * User agent.
     */
    private string $user_agent;

    /**
     * Ad url.
     */
    private string $url;

    /**
     * Array of ad urls.
     */
    private array $url_array = [];

    /**
     * Allowed URLs.
     */
    private array $allowed_url = [
        'www.olx.ua', 'www.olx.com', 'www.olx.pl', 'www.olx.pt', 'www.olx.ua', 'www.olx.ro',
        'www.olx.uz', 'www.olx.uz', 'www.olx.bg', 'www.olx.kz', 'olx.ua', 'olx.com',
        'olx.pl', 'olx.pt', 'olx.ua', 'olx.ro', 'olx.uz', 'olx.uz', 'olx.bg', 'olx.kz',
        'm.olx.ua', 'm.olx.com', 'm.olx.pl', 'm.olx.pt', 'm.olx.ua', 'm.olx.ro',
        'm.olx.uz', 'm.olx.uz', 'm.olx.bg', 'm.olx.kz',
    ];

    /**
     * Parser constructor.
     */
    public function __construct(string $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36')
    {
        $this->user_agent = $user_agent;
    }

    /**
     * Get User Agent.
     */
    public function getUserAgent(): string
    {
        return $this->user_agent;
    }

    /**
     * Set User Agent.
     *
     * @param string $user_agent User Agent
     */
    public function setUserAgent(string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }

    /**
     * Get ad url.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get array of ad urls.
     */
    public function getMultiUrl(): array
    {
        return $this->url_array;
    }

    /**
     * Set ad url.
     */
    public function setUrl(string $url): void
    {
        if ($this->validationUrl($url) === true) {
            $this->url = $url;
        }
    }

    /**
     * Set array of ad urls.
     */
    public function setMultiUrl(array $url_array): void
    {
        $this->url_array = $this->clearUrl($url_array);
    }

    public function run(): array
    {
        $result = [];
        $client = HttpClient::create(['headers' => ['User-Agent' => $this->user_agent]]);
        if (isset($this->url)) {
            $this->url_array[] = $this->url;
        }
        if (\count($this->url_array) === 0) {
            return ['error' => 'URL not specified', 'code' => 1];
        }
        foreach ($this->url_array as $item => $url) {
            try {
                $response = $client->request('GET', $url);
            } catch (TransportExceptionInterface $e) {
                return ['error' => 'Request failed', 'code' => 2];
            }
            if ($response->getStatusCode() !== 200) {
                return ['error' => 'Request failed', 'code' => 2];
            }
            $crawler = new Crawler($response->getContent());
            $name = $crawler->filter('h1[data-cy="ad_title"]')->text();
            $amount = $crawler->filter('div[data-testid="ad-price-container"]')->text();
            $description = $crawler->filter('div[data-cy="ad_description"]')->text();
            $link = $crawler->filter('div[data-cy="adPhotos-swiperSlide"]')->filter('img')->attr('src');
//            $date = $crawler->filter('span.ad-posted-at')->text();
//            $author = $crawler->filter('div[data-cy="seller_card"] > a')->text();
//            $address = $crawler->filter('address')->text();
            $result[] = [
                'name' => $name,
                'amount' => $amount,
                'description' => $description,
                'img' => $link,
//                'date' => $date,
//                'author' => $author,
//                'address' => $address,
            ];
        }
        $this->url_array = [];
        $this->url = '';

        return $result;
    }

    /**
     * URL Validation.
     *
     * @param string $url - ad url
     */
    public function validationUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $parse_url = parse_url($url);
        if (\in_array($parse_url['host'], $this->allowed_url, true)) {
            return true;
        }

        return false;
    }

    /**
     * Remove invalid ad URLs from the array.
     */
    public function clearUrl(array $url_array): array
    {
        $clear_url_array = [];
        foreach ($url_array as $item => $url) {
            if ($this->validationUrl($url) === true) {
                $clear_url_array[] = $url;
            }
        }

        return $clear_url_array;
    }
}
