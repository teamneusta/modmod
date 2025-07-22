<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Provider;

interface FormValueProviderInterface
{
    /**
     * @return mixed
     */
    public function getStoredValue(string $pluginName, string $name);
}
