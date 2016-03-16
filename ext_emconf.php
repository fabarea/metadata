<?php

$EM_CONF[$_EXTKEY] = [
  'title' => 'Metadata extraction',
  'description' => 'PHP-based metadata extraction. The extension relies on the new Metadata Extractor API introduced in TYPO3 CMS 6.2.',
  'category' => 'service',
  'author' => 'Fabien Udriot',
  'author_email' => 'fabien.udriot@typo3.org',
  'author_company' => 'Ecodev',
  'state' => 'stable',
  'version' => '2.2.4',
  'constraints' =>
  [
    'depends' =>
    [
      'cms' => '',
      'typo3' => '6.2.0-7.99.99',
      'filemetadata' => '',
    ],
    'conflicts' =>
    [
    ],
    'suggests' =>
    [
    ],
  ],
];
