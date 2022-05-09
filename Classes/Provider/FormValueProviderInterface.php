<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Provider;

interface FormValueProviderInterface
{
    public function getStoredValue(string $pluginName, string $name);
}
