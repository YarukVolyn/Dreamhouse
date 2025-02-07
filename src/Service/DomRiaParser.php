<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DomRiaParser
{
    private HttpClientInterface $client;

    private string $base_url = 'https://developers.ria.com/dom';

    private LoggerInterface $logger;

    private string $domRiaApiKey = 'NrT7BCcMI0piJ5wMwgMnXzMXJ20MrTsfdVeavp91';

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $uri, array $options): array|bool
    {
        $options['query']['api_key'] = $this->domRiaApiKey;
        $options['query']['lang_id'] = 4;
        $response = $this->client->request(
            $method,
            $this->base_url.$uri,
            $options,
        );

        try {
            return $response->toArray();
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->logger->error('Error:'.$e);
        }

        return false;
    }

    /**
     * @param int $category
     *                            Type of object. One or multiple elements from array:
     *                            [
     *                            1 => 'Квартири',
     *                            4 => 'Будинки',
     *                            13 => 'Комерція',
     *                            10 => 'Офіси',
     *                            24 => 'Ділянка',
     *                            30 => 'Гаражи'
     *                            ]
     * @param int $realty_type
     *                            Type of real estate. One or multiple elements from array:
     *                            [
     *                            2 => 'Квартира',
     *                            3 => 'Кімнаата',
     *                            5 => 'Будинок',
     *                            6 => 'Частина будинку',
     *                            7 => 'Дача',
     *                            11 => 'Офісне приміщення',
     *                            12 => 'Офісна будівля',
     *                            14 => 'Площі',
     *                            15 => 'Склади',
     *                            16 => 'Виробництво',
     *                            17 => 'Ресторани',
     *                            18 => 'Об'єкт',
     *                            19 => 'Готель',
     *                            20 => 'Пансіонати',
     *                            21 => 'Приміщення',
     *                            22 => 'Бізнес',
     *                            25 => 'Під будівництво',
     *                            26 => 'Комерційні',
     *                            27 => 'Сільськогосподарські',
     *                            28 => 'Рекреаційні',
     *                            29 => 'Природні',
     *                            31 => 'Бокс',
     *                            32 => 'Паркінг',
     *                            33 => 'Кооператив',
     *                            34 => 'Гараж',
     *                            35 => 'Стоянка',
     *                            ]
     * @param int $operation_type
     *                            Type of operation. One or multiple elements from array:
     *                            [
     *                            0 => 'Будь-яка операція',
     *                            1 => 'Продаж',
     *                            3 => 'Довгострокова оренда',
     *                            4 => 'Подобова оренда',
     *                            ]
     *
     * @throws TransportExceptionInterface
     */
    public function getDomRiaOptions(int $category, int $realty_type, int $operation_type): array
    {
        return $this->request('GET', '/options', [
            'query' => [
                'category' => $category,
                'realty_type' => $realty_type,
                'operation_type' => $operation_type,
            ],
        ]);
    }

    /**
     * @return array
     *
     * @throws TransportExceptionInterface
     */
    //    public function getDomRiaOptionByName(): array
    //    {
    //        return $this->request('GET', '/options', [
    //            'query' => [
    //                'category' => 1,
    //                'realty_type' => 2,
    //                'operation_type' => 1,
    //            ],
    //        ]);
    //    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getAdById(int|string $id): array|bool
    {
        if (!empty($id)) {
            return $this->request('GET', '/info/'.$id, []);
        }

        $this->logger->alert('Missing required param "id"');

        return false;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function searchAds(array $params): array|bool
    {
        return $this->request('GET', '/search', [
            'query' => $params,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getAveragePrice(array $params): array|bool
    {
        if (isset($params['category'], $params['sub_category'], $params['operation'])) {
            return $this->request('GET', '/average_price', [
                'query' => $params,
            ]);
        }

        $this->logger->alert('Missing one of required params "category" or "sub_category" or "operation"');

        return false;
    }
}
