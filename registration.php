<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Yuukoo_Luckycart',
    __DIR__
);

require_once(__DIR__."/lib/luckycart/luckycart.php");