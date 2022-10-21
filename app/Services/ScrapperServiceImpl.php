<?php

namespace App\Services;

use Goutte\Client;
use Illuminate\Support\Facades\Http;

class ScrapperServiceImpl implements Interfaces\IScrapperService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function process()
    {
        // 'https://www.slav-avto.com.ua/site/login'
        $firstSource = $this->firstSource();

        //'https://pitstop.rv.ua/s/catalog?Search=1641742'
        $secondSource = $this->secondSource();

        //'http://e-shop.fastera.com.ua/'
        $thirdSource = $this->thirdSource();

        //'http://concord-shop.com/ua'
        $fourthSource = $this->fourthSource();

        //'https://my.omega.page/app/pages/login/login.html?v=l92tpnqh'
        $fifthSource = $this->fifthSource();
    }

    public function firstSource(): array
    {
        $crawler = $this->client->request('GET', 'https://www.slav-avto.com.ua/site/login');
        $form = $crawler->selectButton('login-button')->form();
        $crawler = $this->client->submit($form, array('LoginForm[username]' => 'rviparts@gmail.com', 'LoginForm[password]' => 'danidani'));

        $crawlerPrice = $this->client->request('GET', 'https://www.slav-avto.com.ua/ru/catalog/default/index?ProductSearch%5Bsearch_keyword%5D=1641742&ProductSearch%5Bonly_available%5D=0&ProductSearch%5Bonly_available%5D=1&ProductSearch%5BsortOrder%5D=price-asc&ProductSearch%5Bbrand_id%5D=&currency=');

        // we can parse different block from front, for instanse: prices
        $prices = $crawlerPrice->filter('.recently-addet-info-bottom-second')->each(function($node) {
            return $node->text();
        });

        return [
            'prices' => $prices
        ];
    }

    public function secondSource(): array
    {
        $data = Http::get('https://pitstop.rv.ua/s/catalog?Search=1641742')->json();

        // we can parse ready json response
        $details = [];
        foreach ($data as $item) {
            $details[] = [
                'name' => $item['Name'],
                'price' => $item['Price'],
                'CurrencyName' => $item['CurrencyName'],
                'Stock' => $item['Stock'],
                'allStocks' => $item['allStocks']
            ];
        }

        return $details;
    }

    public function thirdSource(): array
    {
        $result = [];
        $crawler = $this->client->request('GET', 'http://e-shop.fastera.com.ua/');
        $form = $crawler->filter('.log-form-in')->first()->form();
        $crawler = $this->client->submit($form, array('USER_LOGIN' => 'carlife2012', 'USER_PASSWORD' => 'Fd558tq'));

        // need to parse response html
        $crawler = $this->client->request('GET', 'http://e-shop.fastera.com.ua/search/?puser_id=43');

        return $result;
    }

    public function fourthSource(): array
    {
        $result = [];
        $crawler = $this->client->request('GET', 'http://concord-shop.com/ua');
        $form = $crawler->filter('#loginform')->first()->form();
        $crawler = $this->client->submit($form, array('login' => 'BRI00504', 'password' => 'poa220622'));

        // cannot authorize by crawler
        return $result;
    }

    public function fifthSource(): array
    {
        $result = [];
        $crawler = $this->client->request('GET', 'https://my.omega.page/app/pages/login/login.html?v=l92tpnqh');
        $form = $crawler->filter('#loginform')->first()->form();
        $crawler = $this->client->submit($form, array('loginInputEmail' => 'renault470@gmail.com', 'loginInputPassword' => 'Magnum470'));

        // need to take info from html
        $resHtml = $crawler->html();

        return $result;
    }
}
