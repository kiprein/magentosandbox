<?php
 
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
 
-- DROP TABLE IF EXISTS {$this->getTable('testimonials')};
CREATE TABLE IF NOT EXISTS {$this->getTable('testimonials')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `rating` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `video` text NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 
");
 
$installer->endSetup();
