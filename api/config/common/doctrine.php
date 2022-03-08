<?php

declare(strict_types=1);

use App\Auth\Entity\User\EmailType;
use App\Auth\Entity\User\IdType;
use App\Auth\Entity\User\RoleType;
use App\Auth\Entity\User\StatusType;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use function App\env;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *      metadata_dirs:array,
         *      dev_mode:bool,
         *      proxy_dir:string,
         *      cache_dir:?string,
         *      subscribers:string[],
         *      types:array<string,class-string<Type>>,
         *      connection:array<string, string>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir']
                ? DoctrineProvider::wrap(new FilesystemAdapter('', 0, $settings['cache_dir']))
                : DoctrineProvider::wrap(new ArrayAdapter()),
            false
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        $eventManager = new EventManager();

        foreach ($settings['subscribers'] as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }

        return EntityManager::create($settings['connection'], $config, $eventManager);
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => env('DB_HOST'),
                'user' => env('DB_USER'),
                'password' => env('DB_PASSWORD'),
                'dbname' => env('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'subscribers' => [],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity',
                __DIR__ . '/../../src/OAuth/Entity',
            ],
            'types' => [
                IdType::NAME => IdType::class,
                EmailType::NAME => EmailType::class,
                StatusType::NAME => StatusType::class,
                RoleType::NAME => RoleType::class,
            ],
        ],
    ],
];
