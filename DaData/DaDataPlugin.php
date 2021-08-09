<?php declare(strict_types=1);

namespace Plugin\DaData;

use App\Domain\AbstractPlugin;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class DaDataPlugin extends AbstractPlugin
{
    const NAME = 'DaDataPlugin';
    const TITLE = 'DaData';
    const DESCRIPTION = 'Data cleansing, enrichment and suggestions';
    const AUTHOR = 'Aleksey Ilyin';
    const AUTHOR_SITE = 'https://getwebspace.org';
    const VERSION = '1.0';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->addSettingsField([
            'label' => 'Token',
            'type' => 'text',
            'name' => 'token',
        ]);
        $this->addSettingsField([
            'label' => 'Secret',
            'type' => 'text',
            'name' => 'secret',
        ]);

        // api for plugin config
        $this
            ->map([
                'methods' => ['get'],
                'pattern' => '/api/dadata/{method}',
                'handler' => function (Request $req, Response $res, $args) use ($container) {
                    $json = [];

                    switch ($args['method']) {
                        case 'balance':
                            $json = $this->getBalance();
                            break;

                        case 'clean':
                            $json = $this->cleanRecord(
                                $req->getParam('name'),
                                $req->getParam('value'),
                            );
                            break;

                        case 'cleanRecord':
                            $json = $this->cleanRecord(
                                $req->getParam('structure'),
                                $req->getParam('record'),
                            );
                            break;

                        case 'findAffiliated':
                            $json = $this->findAffiliated(
                                $req->getParam('query'),
                                $req->getParam('count', 5),
                                $req->getParam('kwargs', []),
                            );
                            break;

                        case 'findById':
                            $json = $this->findById(
                                $req->getParam('name'),
                                $req->getParam('query'),
                                $req->getParam('count', 5),
                                $req->getParam('kwargs', []),
                            );
                            break;

                        case 'geolocate':
                            $json = $this->geolocate(
                                $req->getParam('name'),
                                $req->getParam('lat'),
                                $req->getParam('lon'),
                                $req->getParam('radiusMeters'),
                                $req->getParam('count', 5),
                                $req->getParam('kwargs', []),
                            );
                            break;

                        case 'suggest':
                            $json = $this->suggest(
                                $req->getParam('name'),
                                $req->getParam('query'),
                                $req->getParam('count', 5),
                                $req->getParam('kwargs', []),
                            );
                            break;
                    }

                    return $res->withJson($json, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                },
            ])
            ->setName('api:dd');
    }

    public function getDaDataClient(): \Dadata\DadataClient
    {
        static $client;

        if (!$client) {
            require_once __DIR__ . '/vendor/autoload.php';

            $client = new \Dadata\DadataClient(
                $this->parameter('DaDataPlugin_token'),
                $this->parameter('DaDataPlugin_secret')
            );
        }

        return $client;
    }

    public function getBalance()
    {
        return $this->getDaDataClient()->getBalance();
    }

    public function clean($name, $value)
    {
        return $this->getDaDataClient()->clean($name, $value);
    }

    public function cleanRecord($structure, $record)
    {
        return $this->getDaDataClient()->cleanRecord($structure, $record);
    }

    public function findAffiliated($query, $count = 5, $kwargs = [])
    {
        return $this->getDaDataClient()->findAffiliated($query, $count, $kwargs);
    }

    public function findById($name, $query, $count = 5, $kwargs = [])
    {
        return $this->getDaDataClient()->findById($name, $query, $count, $kwargs);
    }

    public function geolocate($name, $lat, $lon, $radiusMeters = 100, $count = 5, $kwargs = [])
    {
        return $this->getDaDataClient()->geolocate($name, $lat, $lon, $radiusMeters, $count, $kwargs);
    }

    public function suggest($name, $query, $count = 5, $kwargs = [])
    {
        return $this->getDaDataClient()->suggest($name, $query, $count, $kwargs);
    }
}
