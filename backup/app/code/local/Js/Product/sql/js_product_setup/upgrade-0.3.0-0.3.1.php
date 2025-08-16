<?php

$this->startSetup();
// Remove Product Attributes
$this->removeAttribute('catalog_product', 'price_high_net');
$this->removeAttribute('catalog_product', 'price_low_net');
$this->removeAttribute('catalog_product', 'price_high_trophy');
$this->removeAttribute('catalog_product', 'price_low_trophy');
$this->endSetup();