<?php

return [
    // Site Information
    'site' => [
        'name' => 'Sapphire Events & Decorations',
        'tagline' => 'Adding glitz & glam to your events',
        'description' => 'Professional event planning and decoration services in Tallinn, Estonia',
        'email' => 'Sapphireeventsglitz@gmail.com',
        'phone' => '+372-5160427',
        'address' => 'Laki 14a, room 502, 10621 Tallinn, Estonia',
        'registration_code' => '16666563',
    ],

    // Social Links
    'social' => [
        'instagram' => 'https://www.instagram.com/sapphire_events_decorations',
        'facebook' => 'https://www.facebook.com/rararestperfumes',
        'tiktok' => 'https://www.tiktok.com/@sapphire_events__',
        'whatsapp' => 'https://www.whatsapp.com/catalog/3725160427/',
    ],

    // SEO
    'seo' => [
        'title' => 'Sapphire Events & Decorations',
        'description' => 'Professional event planning and decoration services. Weddings, proposals, birthdays, corporate events.',
        'keywords' => 'event planning Tallinn, decoration Tallinn, weddings Tallinn, proposals, birthdays Tallinn, corporate events Tallinn, Tallinn, Estonia, Sapphire Event, Event planner in Tallinn, Ã¼rituste dekoreerija Tallinn, peodekoratsioonid Tallinn, pulmade dekoratsioonid Tallinn,proposal dekoratsioonid Tallinn, romantiline Ã¼llatus Tallinn, baby shower dekoratsioonid
Tallinn,«ärituste kaunistamine Tallinn -Proposal Tallinn,decorator tallinn, Proposal Tallinn',
    ],

    // Colors
    'colors' => [
        'primary' => '#0F3D3E',    // Emerald
        'accent' => '#C8A951',      // Gold
        'light' => '#F8F5F2',       // Off-white
        'dark' => '#1C1C1C',        // Charcoal
    ],

    // Image Settings
    'images' => [
        'thumbnail_width' => 400,
        'medium_width' => 800,
        'max_upload_size' => 5242880, // 5MB
        'allowed_formats' => ['jpeg', 'png', 'webp'],
    ],

    // Pagination
    'pagination' => [
        'gallery_per_page' => 12,
        'testimonials_featured' => 3,
        'gallery_featured' => 6,
    ],

    // Admin
    'admin' => [
        'login_attempts' => 5,
        'login_timeout' => 900, // 15 minutes
        'session_lifetime' => 120, // minutes
    ],
];
