<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_industries_page_defaults')) {
    function andison_industries_page_defaults(): array
    {
        return [
            [
                'slug' => 'motor-vehicle',
                'sort_order' => 0,
                'title' => 'Motor Vehicle Industry',
                'summary' => 'This industry manufactures automobiles, motorcycles, buses, and truck vans. They have a growing presence in the Philippine market, especially with the high market for motorcycles. We offer a wide assortment of welding equipment and consumables necessary to produce world-class products.',
                'details' => 'Top multinational and domestic automotive companies choose our Panasonic Welding Systems to significantly improve weld quality and boost efficiency while reducing production costs. We provide consultation, training, maintenance, and reliable after-sales service to satisfy our customers\' expectations.',
                'products_list' => "Arc Welding Robot with Power Source\nArc Welding Equipment and Filler Metals\nPower Tools and Hand Tools\nPersonal Protective Equipment (PPEs)",
                'image_url' => 'assets/HOME/MOTOR VEHICLE.jpg',
            ],
            [
                'slug' => 'metal-fabrication',
                'sort_order' => 1,
                'title' => 'Metal Fabrication and Industrial',
                'summary' => 'Bridges, railways, refineries, shipyards, transmission lines, and other large-scale projects require steel frames and other metals to support the large infrastructures. Workers in the metal fabrication industry do welding, metal cutting, and fastening to assemble metal parts.',
                'details' => 'We supply our clients with equipment that makes quality welds in a short time. Our safety products protect workers from hazards such as working from heights, sparks, glaring lights, and hazardous gases.',
                'products_list' => "Arc Welding Equipment and Filler Metals\nPlate Cutting and Beveling Equipment\nGas Welding and Cutting Equipment\nPower Tools and Hand Tools\nPersonal Protective Equipment (PPEs)",
                'image_url' => 'assets/HOME/METAL FABRICATION.jpg',
            ],
            [
                'slug' => 'power-generation',
                'sort_order' => 2,
                'title' => 'Power Generation',
                'summary' => 'The Power Generation Industry is vital in a country\'s growth. They must be a reliable partner in meeting the Philippine Energy Market\'s ever-growing demands.',
                'details' => 'From plant maintenance, shutdown, building power transmission lines, and other infrastructures, we work closely with our clients and supply them finish their projects on schedule.',
                'products_list' => "Arc Welding Equipment and Filler Metals\nPower Tools and Hand Tools\nGrinders, Maintenance Tools and Equipment\nBearings, Maintenance Tools and Equipment\nHeight Protection Equipment and other PPEs",
                'image_url' => 'assets/HOME/POWER GENERATION.jpg',
            ],
            [
                'slug' => 'oil-petrochemical',
                'sort_order' => 3,
                'title' => 'Oil and Petrochemical Industry',
                'summary' => 'Oil refineries use fractional distillation and other methods to process crude oil into more useful products like petroleum, gasoline, and other fuels. During the distillation, heavier by-products settle at the bottom. Petrochemical plants crack the by-products and further process them into more useful chemicals. Other industries use these petrochemicals to create different products.',
                'details' => 'Oil and petrochemical industries regularly perform industrial works (projects) that require maintenance, shutdowns, and expanding facilities and pipelines. We provide our clients with safety products, equipment and consumables for maintaining the facilities and building industrial projects.',
                'products_list' => "Arc Welding Equipment and Filler Metals\nPortable Gas Detectors\nAir Movers and Industrial Ventilators\nBearings, Maintenance Tools and Equipment\nPipe Cutting and Beveling Machine\nPower Tools and Hand Tools\nPersonal Protective Equipment (PPEs)",
                'image_url' => 'assets/HOME/OIL AND PETROCHEMICAL.jpg',
            ],
            [
                'slug' => 'mining',
                'sort_order' => 4,
                'title' => 'Mining Industry',
                'summary' => 'This industry extracts coal, oil, metals, and other raw materials from the earth. These resources are processed by other industries to create products such as fuel, jewelry, construction materials, and everyday items. Mining is vital to the economy.',
                'details' => 'However, digging deep into the ground could pose a safety risk to workers without the proper equipment. We at Andison promote safety by providing high-quality PPEs. Our portfolio includes various single and multi-gas detectors including maintenance-free gas detection. We provide clients with training on the proper use of the equipment to fully use its functions and ensure a safe working environment. We also do recalibration for the gas detection.',
                'products_list' => "Portable and Multi-Gas Detectors\nPPEs and other Safety Products\nAir Movers and Ventilation Equipment\nBearings, Maintenance Tools and Equipment\nCordless Power Tools\nFloodlights and other Light Sources",
                'image_url' => 'assets/HOME/MINING.jpg',
            ],
            [
                'slug' => 'shipyard',
                'sort_order' => 5,
                'title' => 'Shipyard',
                'summary' => 'World trade relies heavily on freight ships because it offers a high capacity at a low cost in transporting goods. Being an archipelago, the Philippines also uses ships to ferry people to the country\'s many islands. Shipyards play a critical role in maintaining ships, ensuring they are seaworthy and safe.',
                'details' => 'Metal fabrication is an integral part of the shipbuilding industry. Andison has a wide product catalog for working with metal fabrication, providing clients with equipment ready for the job.',
                'products_list' => "Arc Welding Equipment and Filler Metals\nGas Welding and Cutting Equipment\nAir Movers and Industrial Ventilators\nPower Tools and Hand Tools\nPipe Cutting and Beveling Machine\nPersonal Protective Equipment (PPEs)\nPortable Gas Detectors",
                'image_url' => 'assets/HOME/shipyard.jpg',
            ],
        ];
    }
}

if (!function_exists('andison_industries_page_normalize_row')) {
    function andison_industries_page_normalize_row(array $row): array
    {
        return [
            'slug' => trim((string)($row['slug'] ?? '')),
            'sort_order' => (int)($row['sort_order'] ?? 0),
            'title' => trim((string)($row['title'] ?? '')),
            'summary' => trim((string)($row['summary'] ?? '')),
            'details' => trim((string)($row['details'] ?? '')),
            'products_list' => trim((string)($row['products_list'] ?? '')),
            'image_url' => trim((string)($row['image_url'] ?? '')),
        ];
    }
}

if (!function_exists('andison_get_industries_page_content')) {
    function andison_get_industries_page_content(): array
    {
        $defaults = andison_industries_page_defaults();
        $rows = andison_sb_select('industries_page_content', 'order=sort_order.asc&limit=100');

        if (empty($rows)) {
            return $defaults;
        }

        $out = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalized = andison_industries_page_normalize_row($row);
            if ($normalized['slug'] === '' || $normalized['title'] === '') {
                continue;
            }
            $out[] = $normalized;
        }

        return !empty($out) ? $out : $defaults;
    }
}

if (!function_exists('andison_save_industries_page_content')) {
    function andison_save_industries_page_content(array $items): bool
    {
        if (empty($items)) {
            return false;
        }

        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $normalized = andison_industries_page_normalize_row($item);
            if ($normalized['slug'] === '' || $normalized['title'] === '') {
                continue;
            }
            $rows[] = [
                'slug' => $normalized['slug'],
                'sort_order' => $normalized['sort_order'],
                'title' => $normalized['title'],
                'summary' => $normalized['summary'],
                'details' => $normalized['details'],
                'products_list' => $normalized['products_list'],
                'image_url' => $normalized['image_url'],
            ];
        }

        if (empty($rows)) {
            return false;
        }

        andison_sb_truncate('industries_page_content');
        return andison_sb_insert('industries_page_content', $rows);
    }
}
