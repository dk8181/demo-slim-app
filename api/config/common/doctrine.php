<?php

declare(strict_types=1);

use App\Auth;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

return [
    EntityManagerInterface::class => function (ContainerInterface $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *   paths:array<string,string>,
         *   dev_mode:bool,
         *   proxy_dir:string,
         *   proxy_namespace:string,
         *   types:array<string,string>,
         *   connection:array
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        if ($settings['dev_mode']) {
            $queryCache = new ArrayAdapter();
            $metadataCache = new ArrayAdapter();
        }

        if (! $settings['dev_mode']) {
            $queryCache = new PhpFilesAdapter('doctrine_queries');
            $metadataCache = new PhpFilesAdapter('doctrine_metadata');
        }

        $config = new Configuration();
        $config->setMetadataCache($metadataCache);
        $driverImpl = $config->newDefaultAnnotationDriver($settings['paths'], false);

        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCache($queryCache);
        $config->setProxyDir($settings['proxy_dir']);

        $config->setProxyNamespace($settings['proxy_namespace']);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (! Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        if ($settings['dev_mode']) {
            $config->setAutoGenerateProxyClasses(true);
        }

        if (! $settings['dev_mode']) {
            $config->setAutoGenerateProxyClasses(false);
        }

        return EntityManager::create($settings['connection'], $config);
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'paths' => [
                __DIR__ . '/../../src/Auth/Entity',
            ],
            'proxy_dir' =>  __DIR__ . '/../../var/cache/doctrine/proxy',
            'proxy_namespace' => 'App\Proxies',
            'types' => [
                Auth\Entity\User\IdType::NAME => Auth\Entity\User\IdType::class,
                Auth\Entity\User\EmailType::NAME => Auth\Entity\User\EmailType::class,
                Auth\Entity\User\RoleType::NAME => Auth\Entity\User\RoleType::class,
                Auth\Entity\User\StatusType::NAME => Auth\Entity\User\StatusType::class,
            ],
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'UTF-8',
            ],
        ],
    ],
];
