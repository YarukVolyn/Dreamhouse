<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DomRiaParser;
use App\Service\OlxParser;
use App\Service\SiteHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class OlxTestController extends AbstractController
{
    private SiteHelperService $siteHelperService;
    private TranslatorInterface $translator;
    private DomRiaParser $domRiaParser;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator, DomRiaParser $domRiaParser)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
        $this->domRiaParser = $domRiaParser;
    }

    /**
     * @Route("/{_locale<en|uk>}/olx/test", name="app_olx_test")
     */
    public function index(Request $request): Response
    {
//        $parser = new Parser();
//
//        // Parsing a single ad
//        $parser->setUrl('https://www.olx.ua/d/uk/obyavlenie/zdam-2h-lutsk-kravchuka-6000grn-IDOjkud.html');
//        $result = $parser->run();
//        dd($result);
//
//        /**
//         * array(1) {
//         * [0]=> array(7) {
//         * ["name"]=> string(43) "Кошечка Шанель ищет дом"
//         * ["amount"]=> string(18) "Бесплатно"
//         * ["description"]=> string(738) "Скромная и интеллигентная Шанель с пепельно-голубоватой шерсткой и белыми носочками ищет самых лучших хозяев. Не шкодная, спокойная, лоточек с древесным наполнителем знает. Кушает кашку с куриным фаршем и корм Клуб 4 лапы. Желательно в спокойную семью без маленьких детей. Девочке 1,5 месяца, первичную обработку от паразитов прошла. Будет среднешерстной (короткая очень густая шерсть) Привезем по городу"
//         * ["img"]=> string(80) "https://ireland.apollo.olxcdn.com:443/v1/files/pjq2uknv0fca2-UA/image;s=1000x700"
//         * ["date"]=> string(45) "Добавлено: в 22:31, 9 июня 2020"
//         * ["author"]=> string(10) "Ольга"
//         * ["address"]=> string(85) "Запорожье, Запорожская область, Шевченковский"
//         * }
//         * }.
//         */
//
//        // Parsing multiple ads
//        $url_array = [
//            'https://www.olx.ua/d/uk/obyavlenie/zdam-2h-lutsk-kravchuka-6000grn-IDOjkud.html',
//            'https://www.olx.ua/d/uk/obyavlenie/orenda-2h-kmnatno-kvartiri-IDOMfBB.html',
//        ];
//        $parser->setMultiUrl($url_array);
//        $result = $parser->run();
//        var_dump($result);
//
//        /**
//         * array(2) {
//         * [0]=> array(7) {
//         * ["name"]=> string(10) "Intel Core"
//         * ["amount"]=> string(13) "1 000 грн."
//         * ["description"]=> string(307) "Процесори різних моделей, всі у робочому стані. Якщо зацікавило пишіть. Для уточнення вартості моделі яка зацікавила, пишіть. Також є інші комплектуючі для комп'ютерів."
//         * ["img"]=> string(80) "https://ireland.apollo.olxcdn.com:443/v1/files/pk95fbpvt4z53-UA/image;s=1000x700"
//         * ["date"]=> string(75) "Опубликовано с мобильного в 12:03, 10 июня 2020"
//         * ["author"]=> string(16) "Катерина"
//         * ["address"]=> string(61) "Тернополь, Тернопольская область"
//         * }
//         * [1]=> array(7) {
//         * ["name"]=> string(46) "Монитор Samsung диагональ 21,5"
//         * ["amount"]=> string(13) "1 800 грн."
//         * ["description"]=> string(710) "Монитор в идеальном состоянии. Хотел сделать из него телевизор и подключить к IPtv через HDMI разъем. Но поскольку к нему нужно отдельно еще и звук делать решил не заморачиваться и купить просто телевизор. Монитор оказался не нужен. Продаю по полной предоплате на карту Приват банка или доставка ОЛХ. При необходимости лучше не звоните а пишите сообщение в ОЛХе. Я дам другой телефон для связи."
//         * ["img"]=> string(80) "https://ireland.apollo.olxcdn.com:443/v1/files/9qfvni8xxix32-UA/image;s=1000x700"
//         * ["date"]=> string(75) "Опубликовано с мобильного в 12:02, 10 июня 2020"
//         * ["author"]=> string(8) "Ігор"
//         * ["address"]=> string(98) "Прогресс, Кировоградская область, Гайворонский район"
//         * }
//         * }.
//         */
//        // Remove invalid URLs from the array
//        $url_array = [
//            'https://www.olx.ua/d/uk/obyavlenie/orenda-2h-kmnatno-kvartiri-IDOMfBB.html',
//            'undefined',
//        ];
//        $clear_url_array = $parser->clearUrl($url_array);
//        var_dump($clear_url_array);
//
//        /**
//         * array(1) {
//         * [0]=> string(73) "https://www.olx.ua/obyavlenie/intel-core-IDG8Pu3.html#f9f01ff837;promoted"
//         * }.
//         */
//        // Check URL for correctness
//        $url = 'https://www.olx.ua/d/uk/obyavlenie/orenda-2h-kmnatno-kvartiri-IDOMfBB.html';
//        var_dump($parser->validationUrl($url)); // bool (true)
//
//        $url = 'https://www.avito.ru/krasnodar/ohota_i_rybalka/flyaga_brezent_5_l_1913328391';
//        var_dump($parser->validationUrl($url)); // bool (false)

        $ad = $this->domRiaParser->getAdById(23592716);
        $i = 1;
        $parameters = $this->siteHelperService->getBaseParameters($request);
        $parameters['page_title'] = $this->translator->trans('About Us');
        $parameters['controller_name'] = 'OlxTestController';

        return $this->render('content/olx_test/index.html.twig', $parameters);
    }
}
