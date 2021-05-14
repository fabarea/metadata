<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Metadata extraction',
    'description' => 'PHP-based metadata extraction. The extension relies on the new Metadata Extractor API introduced in TYPO3 CMS 6.2.',
    'category' => 'service',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien.udriot@typo3.org',
    'author_company' => 'Ecodev',
    'state' => 'stable',
    'version' => '4.1.0-dev',
    'constraints' =>
        [
            'depends' =>
                [
                    'php' => '7.0.0-',
                    'typo3' => '8.7.0-10.4.99',
                    'filemetadata' => '8.7.0-10.4.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ],
];
