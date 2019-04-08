<?php
/**
 * Created by PhpStorm.
 * User: Yongzhen Ye
 * Date: 2019/3/27
 * Time: 18:00
 */
namespace Yyz\Weather;

use GuzzleHttp\Client;
use Yyz\Weather\Exceptions\HttpException;
use Yyz\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    /**
     * 高德开放平台 key
     * @var string
     */
    protected $key;

    /**
     * Guzzle 实例参数数组
     *
     * @var array
     */
    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * 返回 guzzle 实例
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 自定义 guzzle 实例的参数，如超时时间
     *
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 获取天气
     *
     * @param $city // 城市名/adcode
     * @param string $type  // 返回内容类型，base:实况天气；all:预报天气
     * @param string $format // 输出的数据格式，默认 json，output=xml时，返回 xml
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getWeather($city, $type = 'base', $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => \strtolower($format),
            'extensions' =>  \strtolower($type),
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'json' === $format ? $response : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 获取实时天气
     *
     * @param $city
     * @param string $format
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    /**
     * 获取天气预报
     *
     * @param $city
     * @param string $format
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }
}