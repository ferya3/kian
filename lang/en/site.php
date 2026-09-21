<?php

/*
| UI strings. Content — product names, step descriptions, articles — lives
| in the database and is translated from the admin panel, not here.
|
| A missing key falls back to Persian, so a partial translation never
| breaks a page.
|
| Keys follow the site's layout rather than the kind of string: whatever is
| in the header is under nav, whatever is in the footer under footer.
| Finding a string you saw in the browser should not mean reading the file.
*/

return [

    // Brand and contact details. Keys absent here fall back to config/kian.php.
    'brand' => [
        'name' => 'Kian Behsaz',
        'legal_name' => 'Kian Behsaz Co.',
        'tagline' => 'Born of Earth. Engineered for the Future.',
    ],

    'contact' => [
        'address' => 'Mobarakeh Industrial Estate, Sanat 12 St., Isfahan — Kian Behsaz plant',
        'working_hours' => 'Sat–Wed 8:00–17:00 — Thu 8:00–13:00',
    ],

    'language' => [
        'label' => 'Language',
        'switch' => 'Change site language',
        'current' => 'Current language: :name',
    ],

    // --------------------------------------------------------- Navigation --

    'nav' => [
        'skip' => 'Skip to main content',
        'menu' => 'Open menu',
        'close' => 'Close menu',
        'search' => 'Search',
        'home' => 'Home',
        'main' => 'Main navigation',
        'footer' => 'Footer navigation',
        'mobile' => 'Mobile navigation',
        'panel' => 'Main menu',
        'breadcrumb' => 'Breadcrumb',
        'brand_home' => ':name — home',

        'items' => [
            'products_index' => 'Products',
            'shop_index' => 'Shop',
            'solutions_index' => 'Solutions',
            'projects_index' => 'Projects',
            'technology' => 'Technology',
            'factory' => 'The factory',
            'sustainability' => 'Sustainability',
            'technical_index' => 'Technical',
            'technical_downloads' => 'Datasheets, CAD and BIM',
            'technical_installation' => 'Installation guide',
            'technical_certificates' => 'Certificates and standards',
            'technical_faq' => 'FAQ',
            'articles_index' => 'Articles',
            'about' => 'About us',
            'distributors' => 'Distributors',
            'contact' => 'Contact',
        ],

        'sub' => [
            'technology' => 'From clay to structure',
            'technical_index' => 'Technical centre',
            'about' => 'The company',
        ],
    ],

    'topbar' => [
        'datasheets' => 'Datasheets, CAD and BIM',
        'distributors' => 'Distributors',
    ],

    // ------------------------------------------------------------ Actions --

    'actions' => [
        'quote' => 'Request a quote',
        'products' => 'View products',
        'factory' => 'Visit the factory',
        'more' => 'More',
        'download' => 'Download',
        'call' => 'Call',
        'call_sales' => 'Call the sales desk',
        'consult' => 'Request technical advice',
        'view_product' => 'View product',
        'all_products' => 'See all products',
        'finder' => 'Find a product',
        'finder_long' => 'Find the right product for your project',
        'finder_mobile' => 'Find the block for my project',
        'price_enquiry' => 'Price enquiry',
    ],

    // ------------------------------------------------------------- Search --

    'search' => [
        'dialog' => 'Search the site',
        'term' => 'Search term',
        'placeholder' => 'Product name, thickness, project or technical file…',
        'placeholder_short' => 'Search products or technical files…',
        'close' => 'Close search',
        'popular' => 'Popular searches:',
        'hints' => ['Ceramic block 20', 'Insulating block', 'Datasheet', 'BIM file', 'Installation'],
    ],

    // ------------------------------------------------------------- Footer --

    'footer' => [
        'eyebrow' => 'Talk to an engineer',
        'title' => 'Talk your project through with our engineers',
        'lead' => 'Wall thermal resistance, the right thickness for your climate, and a materials estimate — at no cost.',
        'tagline' => ':tagline — engineered ceramic blocks since :year.',
        'rights' => 'All rights reserved.',

        'products' => 'Products',
        'technical' => 'Technical centre',
        'company' => 'Company',
        'sales' => 'Sales',

        'finder' => 'Find a product →',
        'datasheets' => 'Product datasheets',
        'cad' => 'CAD files',
        'bim' => 'BIM objects',
        'installation' => 'Installation guide',
        'certificates' => 'Certificates',
        'faq' => 'FAQ',
        'about' => 'About us',
        'factory' => 'The factory',
        'technology' => 'Production technology',
        'sustainability' => 'Sustainability',
        'projects' => 'Projects',
        'articles' => 'Technical articles',
        'quote' => 'Request a quote',
        'distributors' => 'Distributors',
        'become_distributor' => 'Become a distributor',
        'support' => 'Technical support',
    ],

    // ---------------------------------------------------------- Mega menu --

    'mega' => [
        'eyebrow' => 'Product System',
        'title' => 'The ceramic block system',
        'lead' => 'From the 7 cm partition to the 30 cm insulating block — one modular family with dimensions that line up.',
        'featured' => 'Featured',
        'featured_title' => 'Insulating block 25',
        'featured_lead' => 'A single-leaf external wall with no added insulation. λ of 0.21.',
        'downloads' => 'Datasheets and technical files',
    ],

    // --------------------------------------------------------- Pagination --

    'pagination' => [
        'label' => 'Pagination',
        'previous' => 'Previous page',
        'next' => 'Next page',
    ],

    // ------------------------------------------------- Product card/table --

    'card' => [
        'featured' => 'Popular',
        'cm' => 'cm',
        'weight' => 'Weight',
        'strength' => 'Strength',
    ],

    'spec' => [
        'caption' => 'Technical specifications for :name',
        'feature' => 'Property',
        'value' => 'Value',
    ],

    // ------------------------------------------------- Product finder --
    // label/hint overlay onto config/kian.php; structure stays there.

    'finder' => [
        'project_types' => [
            'residential' => ['label' => 'Residential', 'hint' => 'Apartments, villas, complexes'],
            'commercial' => ['label' => 'Commercial and offices', 'hint' => 'Retail complexes, office towers'],
            'industrial' => ['label' => 'Industrial', 'hint' => 'Sheds, plants, warehouses'],
            'mass' => ['label' => 'Mass housing', 'hint' => 'Public housing and urban development'],
        ],

        'wall_types' => [
            'exterior' => ['label' => 'External wall', 'hint' => 'Facade and thermal envelope'],
            'interior' => ['label' => 'Internal wall', 'hint' => 'Separating the main spaces'],
            'partition' => ['label' => 'Partition', 'hint' => 'Light non-loadbearing partition'],
            'infill' => ['label' => 'Infill wall', 'hint' => 'Filling a concrete or steel frame'],
            'roof' => ['label' => 'Roof', 'hint' => 'Joist-and-block roofing'],
        ],

        'insulation_levels' => [
            'low' => ['label' => 'Low', 'hint' => 'Internal space, no thermal requirement'],
            'medium' => ['label' => 'Medium', 'hint' => 'Meets the code minimum'],
            'high' => ['label' => 'High', 'hint' => 'Cold or hot climate, energy saving'],
            'very_high' => ['label' => 'Very high', 'hint' => 'Low-energy building, energy rating'],
        ],

        'field' => [
            'project_type' => 'Project type',
            'wall_type' => 'Wall type',
            'thickness' => 'Thickness needed',
            'insulation' => 'Thermal insulation needed',
        ],

        'choose' => 'Choose…',
        'any' => 'No preference',
        'hint' => 'Pick at least two options and we can be more precise.',
        'ready' => 'Ready for a recommendation',
        'clear' => 'Clear',
        'submit' => 'See recommended products',
        'loading' => 'Calculating…',
        'results_region' => 'Product finder results',
        'noscript' => 'The button above takes you to the results page.',

        'reason' => [
            'project_fit' => 'Suited to :label projects',
            'wall_fit' => 'Designed for :label',
            'thickness_exact' => 'Thickness matches your requirement exactly (:value cm)',
            'insulation_exact' => 'Insulation level matches your requirement exactly',
            'insulation_higher' => 'Insulation above the level you asked for',
        ],

        'gap' => [
            'project_fit' => 'For :label we have a better fit as well',
            'wall_fit' => 'This product is not mainly for :label',
            'thickness_near' => ':value cm instead of :wanted',
            'insulation_lower' => 'For more insulation, look at a thicker block',
        ],

        'criteria' => [
            'insulation' => ':label insulation',
        ],
    ],

    // ------------------------------------------------------- Audiences --

    'audiences' => [
        'customer' => [
            'label' => 'Client and buyer',
            'question' => 'Which product suits my project?',
            'cta' => 'Find a product',
        ],
        'engineer' => [
            'label' => 'Engineer and architect',
            'question' => 'Where are the specs and working files?',
            'cta' => 'Technical centre',
        ],
        'contractor' => [
            'label' => 'Contractor and installer',
            'question' => 'What is the correct way to lay it?',
            'cta' => 'Installation guide',
        ],
    ],

    // ------------------------------------------------------- Home page --

    'home' => [
        'swipe' => 'Swipe to see the rest',

        'finder' => [
            'eyebrow' => 'Find your block',
            'title' => 'Find the right product for your project',
            'lead' => 'Four questions, three recommendations. Instead of browsing the catalogue, start from what the project actually needs: the structure, where the wall sits in the plan, the thickness you can afford and the thermal performance you expect.',
        ],

        'products' => [
            'title' => 'One family, dimensions that line up',
            'lead' => 'Our products are modular: partition, wall and insulating blocks share a coursing module, so no extra cutting is needed.',
            'all' => 'All products',
            'list' => 'Popular products',
        ],

        'carousel' => 'carousel',
        'stats' => 'Factory figures',

        'hero' => [
            'eyebrow' => ':name — the factory',
            'scroll' => 'Go to the product finder',
        ],

        'why' => [
            'title' => 'Why ceramic?',
            'lead' => 'Six properties, none of them added on — all of them come from the material itself and the geometry of the block.',
            'interactive' => 'Interactive',
            'view' => 'Block view',
            'solid' => 'Solid',
            'section' => 'Section',
            'drag' => 'Drag to rotate — or use the arrow keys',
            'pick_cavity' => 'Click the highlighted points on the section to see the block’s internal structure.',
            'cards' => 'Ceramic block properties',

            'thermal' => [
                'title' => 'Thermal insulation',
                'en' => '',
                'text' => 'Still air trapped in multiple rows of cavities is insulation the material makes for itself. The more rows, and the longer the path heat has to travel, the lower the λ.',
                'metric' => 'λ from 0.17',
                'metric_label' => 'W/m·K',
            ],
            'acoustic' => [
                'title' => 'Acoustic performance',
                'en' => '',
                'text' => 'Ceramic mass combined with air cavities damps airborne sound, and does it without adding load to the structure.',
                'metric' => 'up to 55',
                'metric_label' => 'dB sound reduction',
            ],
            'fire' => [
                'title' => 'Fire resistance',
                'en' => '',
                'text' => 'Ceramic is fired at nine hundred degrees; there is nothing left in it to burn. It is non-combustible and releases no toxic gas in a fire.',
                'metric' => 'up to 240',
                'metric_label' => 'minutes fire resistance',
            ],
            'weight' => [
                'title' => 'Lower dead load',
                'en' => '',
                'text' => 'Seismic lateral force is proportional to the mass of the structure. A lighter wall means lower base shear — and when adding a storey or retrofitting, those few percent decide it.',
                'metric' => 'up to 28%',
                'metric_label' => 'less wall weight',
            ],
            'durability' => [
                'title' => 'Durability',
                'en' => '',
                'text' => 'No long-term shrinkage, no rot, and stable against moisture and frost. It lasts as long as the building does.',
                'metric' => '50+',
                'metric_label' => 'years of service life',
            ],
            'natural' => [
                'title' => 'A natural material',
                'en' => '',
                'text' => 'Earth, water, fire. No persistent chemical additives, no volatile emissions — and at the end of the building’s life it can be crushed and returned to the cycle.',
                'metric' => '100%',
                'metric_label' => 'mineral raw material',
            ],
        ],

        'technical' => [
            'title' => 'For engineers and architects',
            'lead' => 'Everything you need to design, model and build — no sign-up, no call to the sales desk.',
            'missing' => 'Didn’t find the file you were after? Our engineers will prepare project-specific detail for you, from a construction detail to a thermal calculation.',
            'request' => 'Request a file or a calculation',
            'tile' => [
                'datasheet' => 'Product datasheets',
                'catalog' => 'Technical catalogue',
                'cad' => 'CAD files',
                'bim' => 'BIM objects',
                'installation' => 'Installation guide',
                'certificates' => 'Certificates and standards',
            ],
        ],

        'process' => [
            'title' => 'From earth to structure',
            'lead' => 'Nine stages, from quarried clay to a shrink-wrapped pallet ready to load.',
            'steps' => 'Production stages',
            'technology' => 'Technology in detail',
            'visit' => 'Visit the factory',
            'outcome' => [
                'title' => 'And then, the building',
                'summary' => 'Every pallet leaving this line carries a production batch code — traceable ten years later.',
                'alt' => 'A building completed with Kian ceramic block',
            ],
        ],
        'factory' => [
            'title' => 'The factory, not a photo of one',
            'lead' => 'Click any area to see exactly what happens there, and at what capacity.',
            'autoplay' => 'Touring automatically — click to take control',
            'keys' => 'You can also move with the arrow keys',
            'sections' => 'Factory areas',
            'full' => 'Full factory page',
            'visit' => 'Request a visit',
        ],

        'projects' => [
            'title' => 'Completed projects',
            'lead' => 'From a twenty-two storey office tower to twelve hundred public housing units — every project posed a different problem.',
            'all' => 'All projects',
            'show' => 'Show the :title project',
        ],

        'sustainability' => [
            'title' => 'Clay, product, building',
            'lead' => 'Ceramic runs a closed cycle with nothing foreign entering it: taken from the ground, fired, fifty years of service, and earth again at the end.',
            'report' => 'Sustainability report',
            'cycle' => [
                'clay' => ['title' => 'Clay', 'en' => 'Extraction', 'text' => 'Quarried from our own site, with a restoration plan for the ground we take it from.'],
                'product' => ['title' => 'Product', 'en' => 'Production', 'text' => 'Fired with recovered heat; every bit of green waste goes back into the line.'],
                'building' => ['title' => 'Building', 'en' => 'In use', 'text' => 'Fifty years of performance with no decline, and a permanent cut in the building’s energy use.'],
            ],
        ],

        'solutions' => [
            'title' => 'A solution, not just a product',
            'lead' => 'Tell us the problem in the project and we will propose the right combination of products.',
            'all' => 'All solutions',
            'view' => 'View solution',
        ],
    ],

    'gallery' => [
        'title' => 'Gallery',
    ],

];
