<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Sonata bundles
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),

            // Topo bundles
            new Topo\AdminBundle\TopoAdminBundle(),
        ];

        if ('dev' === $this->getEnvironment()) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/var/cache/', dirname(__DIR__), $this->getEnvironment());
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/var/logs/', dirname(__DIR__), $this->getEnvironment());
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config.yml', $this->getRootDir()));

        // loads the config file if exists
        if (is_file($file = sprintf(sprintf('%s/config/config.local.yml', $this->getRootDir())))) {
            $loader->load($file);
        }

        $environment = $this->getEnvironment();

        $loader->load(function (ContainerBuilder $container) use ($environment) {
            // calls the configuration container method depending on the current environment
            // for example in dev environment: configureDevContainer
            $method = sprintf('configure%sContainer', ucfirst(strtolower($environment)));
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $container);
            }
        });
    }

    /**
     * Configures the dev environment container.
     *
     * @param ContainerBuilder $container
     */
    private function configureDevContainer(ContainerBuilder $container)
    {
        // framework configuration
        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => '%kernel.root_dir%/config/routing_dev.yml',
                'strict_requirements' => true,
            ],
            'profiler' => [
                'only_exceptions' => false,
            ],
        ]);

        // web profile configuration
        $container->loadFromExtension('web_profiler', [
            'toolbar' => true,
            'intercept_redirects' => false,
        ]);

        // check if there is a default delivery address
        if ($container->hasParameter('mailer_delivery_address')) {
            $mailerDeliveryAddress = $container->getParameter('mailer_delivery_address');
            if (filter_var($mailerDeliveryAddress, FILTER_VALIDATE_EMAIL)) {
                $container->loadFromExtension('swiftmailer', [
                    'delivery_address' => $mailerDeliveryAddress,
                ]);
            }
        }
    }
}
