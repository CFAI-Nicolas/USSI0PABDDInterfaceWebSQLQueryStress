<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container7bIFub1\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container7bIFub1/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container7bIFub1.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container7bIFub1\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container7bIFub1\App_KernelDevDebugContainer([
    'container.build_hash' => '7bIFub1',
    'container.build_id' => '66b6af36',
    'container.build_time' => 1748870189,
    'container.runtime_mode' => \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ? 'web=0' : 'web=1',
], __DIR__.\DIRECTORY_SEPARATOR.'Container7bIFub1');
