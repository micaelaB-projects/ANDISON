<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_services_page_defaults')) {
    function andison_services_page_defaults(): array
    {
        return [
            [
                'slug' => 'consultation',
                'sort_order' => 0,
                'badge' => 'Expert Assistance',
                'title' => 'Technical Consultation',
                'description' => 'Expert guidance from our team of experienced industrial specialists. We provide comprehensive consultation on equipment selection, process optimization, and technical specifications to ensure you have the solid support and processes for your application.',
                'details' => 'Our consultation services include process optimization, equipment selection assistance, facility design and compliance audit support customized to support successful project implementation.',
                'icon' => 'bi-gear',
                'image_url' => '',
                'is_teal' => false,
                'is_reverse' => false,
            ],
            [
                'slug' => 'training',
                'sort_order' => 1,
                'badge' => 'Skill Development',
                'title' => 'Training Programs',
                'description' => 'Comprehensive training programs designed to enhance your team\'s capabilities with advanced welding equipment and safety protocols. Our certified instructors provide hands-on training covering operator qualification, preventive procedures, and technical troubleshooting.',
                'details' => 'Training includes onsite training sessions, certification programs, hands and angle verification procedures, and comprehensive documentation to ensure technician competency and adherence to safety standards.',
                'icon' => 'bi-book',
                'image_url' => '',
                'is_teal' => true,
                'is_reverse' => true,
            ],
            [
                'slug' => 'maintenance',
                'sort_order' => 2,
                'badge' => 'Full Performance',
                'title' => 'Equipment Maintenance',
                'description' => 'Preventive maintenance and repair service to keep equipment operating at peak performance, maximize uptime and equipment lifespan. Our field service team ensures all critical equipment is properly maintained and emergency repairs are addressed immediately.',
                'details' => 'We maintain extensive preventive care upon the full inspection maintenance contract schedules such repairs, quick emergency support and emergency repairs. Also maintain complete spare parts inventory to minimize downtime and extend equipment lifespan.',
                'icon' => 'bi-tools',
                'image_url' => '',
                'is_teal' => false,
                'is_reverse' => false,
            ],
            [
                'slug' => 'support',
                'sort_order' => 3,
                'badge' => 'Ongoing Support',
                'title' => 'After-Sales Support',
                'description' => 'Dedicated support team to assist with troubleshooting, spare part procurement and application-specific guidance. We provide prompt response to ensure minimal disruption to your operations.',
                'details' => 'Our after-sales support includes company standardization technical documentation, software updates for remote systems and dispatch our integrated specialized team for comprehensive technical support and immediate availability.',
                'icon' => 'bi-headset',
                'image_url' => '',
                'is_teal' => true,
                'is_reverse' => true,
            ],
        ];
    }
}

if (!function_exists('andison_services_page_normalize_row')) {
    function andison_services_page_normalize_row(array $row): array
    {
        return [
            'slug' => trim((string)($row['slug'] ?? '')),
            'sort_order' => (int)($row['sort_order'] ?? 0),
            'badge' => trim((string)($row['badge'] ?? '')),
            'title' => trim((string)($row['title'] ?? '')),
            'description' => trim((string)($row['description'] ?? '')),
            'details' => trim((string)($row['details'] ?? '')),
            'icon' => trim((string)($row['icon'] ?? '')),
            'image_url' => trim((string)($row['image_url'] ?? '')),
            'is_teal' => filter_var($row['is_teal'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_reverse' => filter_var($row['is_reverse'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }
}

if (!function_exists('andison_get_services_page_content')) {
    function andison_get_services_page_content(): array
    {
        $defaults = andison_services_page_defaults();
        $rows = andison_sb_select('services_page_content', 'order=sort_order.asc&limit=100');

        if (empty($rows)) {
            return $defaults;
        }

        $out = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalized = andison_services_page_normalize_row($row);
            if ($normalized['slug'] === '' || $normalized['title'] === '') {
                continue;
            }
            $out[] = $normalized;
        }

        return !empty($out) ? $out : $defaults;
    }
}

if (!function_exists('andison_save_services_page_content')) {
    function andison_save_services_page_content(array $items): bool
    {
        if (empty($items)) {
            return false;
        }

        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $normalized = andison_services_page_normalize_row($item);
            if ($normalized['slug'] === '' || $normalized['title'] === '') {
                continue;
            }
            $rows[] = [
                'slug' => $normalized['slug'],
                'sort_order' => $normalized['sort_order'],
                'badge' => $normalized['badge'],
                'title' => $normalized['title'],
                'description' => $normalized['description'],
                'details' => $normalized['details'],
                'icon' => $normalized['icon'],
                'image_url' => $normalized['image_url'],
                'is_teal' => $normalized['is_teal'],
                'is_reverse' => $normalized['is_reverse'],
            ];
        }

        if (empty($rows)) {
            return false;
        }

        andison_sb_truncate('services_page_content');
        return andison_sb_insert('services_page_content', $rows);
    }
}
