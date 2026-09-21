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
        'results_for' => 'Results for “:term”',
        'start' => 'Type something in the box above to begin.',
        'none' => 'Nothing found',
        'none_hint' => 'Try a different spelling, or use the product finder.',
        'count' => ':count results',
        'files' => 'Technical files',
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
        'units' => 'pcs',
        'litres_sqm' => 'litres/m²',
        'row' => [
            'dimensions' => 'Nominal dimensions',
            'thickness' => 'Wall thickness',
            'weight' => 'Weight each',
            'strength' => 'Compressive strength',
            'lambda' => 'Thermal conductivity (λ)',
            'r_value' => 'Thermal resistance (R)',
            'absorption' => 'Water absorption',
            'acoustic' => 'Sound reduction',
            'fire' => 'Fire resistance',
            'void' => 'Void ratio',
            'per_sqm' => 'Units per m²',
            'per_pallet' => 'Units per pallet',
            'mortar' => 'Mortar used',
        ],
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
        'matches' => ':count products for your project',
        'best_match' => 'Best match',
        'score' => 'Match',
        'score_of' => 'Match score for :name',
        'disclaimer' => 'This recommendation follows the criteria you gave. For an exact thermal calculation, talk to our engineers.',
        'free_advice' => 'Free technical advice',
        'page_lead' => 'Four questions about the project, and three recommendations with reasons. If you do not know an answer, leave it blank — the finder works with partial information.',
        'results_title' => 'Products recommended for your project',
        'based_on' => 'Based on: :criteria',
        'based_on_chosen' => 'Based on the criteria you chose',
        'change' => 'Want to change the criteria?',

        'tip' => [
            'thickness' => ['title' => 'Take the thickness from the project requirement', 'text' => 'External wall thickness usually comes out of the thermal calculation, and internal wall thickness out of the acoustic requirement.'],
            'insulation' => ['title' => 'Insulation is a running cost', 'text' => 'Each step up in insulation raises the initial cost a little and lowers energy use for fifty years.'],
            'weight' => ['title' => 'Weight is a structural question', 'text' => 'When adding a storey or retrofitting, a lighter wall can matter more than a better-insulated one.'],
        ],
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

    // ------------------------------------------------- Product pages --

    'products' => [
        'title' => 'Products',
        'lead' => 'A modular family of ceramic blocks: partition, wall, insulating, lightweight, roofing and accessories. Their dimensions line up, so coursing runs without extra cutting.',
        'category' => 'Category:',
        'all' => 'All',
        'count' => ':count products',
        'unsure' => 'Not sure which?',
        'empty' => 'No product matches this filter',
        'empty_hint' => 'Clear the filter, or use the product finder.',
        'compare' => 'Comparison table',
        'compare_lead' => 'Every figure at a glance. Drag the table sideways to see more columns.',
        'compare_caption' => 'Comparison of product specifications',
        'drag_table' => 'Drag the table sideways',

        'column' => [
            'product' => 'Product',
            'dimensions' => 'Size (cm)',
            'weight' => 'Weight (kg)',
            'strength' => 'Strength (MPa)',
            'lambda' => 'λ (W/m·K)',
            'sound' => 'Sound (dB)',
            'fire' => 'Fire (min)',
            'per_sqm' => 'Units per m²',
        ],
    ],

    'product' => [
        'view' => 'Product view',
        'solid' => 'Solid',
        'section' => 'Section',
        'drag' => 'Drag to rotate — or use the arrow keys',
        'tap_points' => 'Click the points on the section',
        'cavity_hint' => 'The highlighted points on the section explain the block’s internal structure.',
        'minutes' => 'min',
        'specs' => 'Specifications',
        'datasheet' => 'Download datasheet',
        'datasheet_short' => 'Datasheet',
        'advice' => 'Technical advice',
        'gallery' => 'Product images',
        'why' => 'Why this product?',
        'features' => 'Features',
        'applications' => 'Applications',
        'in_solutions' => 'Solutions this product is used in',
        'files' => 'Technical files for this product',
        'files_lead' => 'Datasheet, DWG section and BIM objects with thermal and acoustic parameters.',
        'download_centre' => 'Download centre',
        'install' => 'How it is laid',
        'install_lead' => 'The five points that matter most to how the finished wall performs.',
        'install_full' => 'Full installation guide',
        'used_in' => 'Projects built with this product',
        'related' => 'Related products',

        'benefit' => [
            'thermal' => 'Thermal insulation',
            'acoustic' => 'Sound insulation',
            'fire' => 'Fire resistance',
        ],

        'step' => [
            'level' => ['title' => 'Level the bed', 'text' => 'The first course goes on a perfectly level mortar bed. Every millimetre out of true in the first course multiplies up the height of the wall.'],
            'damp' => ['title' => 'Damp the block, do not soak it', 'text' => 'The surface should be damp so it does not draw water out of the mortar. A soaked block does the opposite and destroys adhesion.'],
            'joint' => ['title' => 'Fill the bed joint completely', 'text' => 'A partial joint lowers strength and opens a path for sound and heat. With tongue-and-groove, the perpend joint disappears.'],
            'lintel' => ['title' => 'No opening without a lintel', 'text' => 'Even small openings need a lintel. A ceramic lintel also keeps the thermal continuity of the facade.'],
            'height' => ['title' => 'A metre and a half a day, at most', 'text' => 'Going higher in one go compresses the fresh mortar in the courses below and throws the wall out of plumb.'],
        ],
    ],

    // ------------------------------- Solutions, projects and articles --

    'solutions' => [
        'title' => 'Solutions',
        'lead' => 'A product is one component; a solution is the right combination of components for the particular problem in your project.',
        'detail' => 'Solution detail',
        'benefits' => 'Benefits',
        'products' => 'Products',
        'its_benefits' => 'What this solution gives you',
        'suggested' => 'Products recommended for this solution',
        'advice' => 'Advice on this solution',
    ],

    'projects' => [
        'lead' => 'Every project posed a different problem: one needed an energy rating, one needed speed, one needed less dead load. Here is what was chosen, and why.',
        'filter' => 'Filter by project type',
        'gallery' => 'Project gallery',
        'problem' => 'The problem and the answer',
        'info' => 'Project details',
        'products_used' => 'Products used',
        'similar' => 'Similar projects',
        'similar_cta' => 'Have a project like this?',
        'location' => 'Location',
        'year_built' => 'Year built',
        'year' => 'Year',
        'area' => 'Floor area',
        'blocks' => 'Blocks used',
        'client' => 'Client',
        'architect' => 'Architect',
        'city' => 'City',
        'sqm' => ':value m²',
        'units' => ':value units',
    ],

    'articles' => [
        'title' => 'Technical knowledge',
        'lead' => 'Articles written by our engineers: honest comparisons between materials, code calculations, and what most often goes wrong on site.',
        'reading' => ':minutes min read',
        'minutes' => ':minutes min',
        'question' => 'A question about this piece?',
        'question_lead' => 'Our engineers will run a calculation for your project at no cost.',
        'more' => 'More reading',
    ],

    // ------------------------------------- Contact and distributors --

    'contact' => [
        'address' => 'Mobarakeh Industrial Estate, Sanat 12 St., Isfahan — Kian Behsaz plant',
        'working_hours' => 'Sat–Wed 8:00–17:00 — Thu 8:00–13:00',
        'lead' => 'Fill in the form or call us directly. We answer within one working day at most.',
        'fix' => 'Please correct the following:',
        'phone_format' => 'Enter the phone number as 09121234567 or 02188123456.',
        'invalid' => 'That request is not valid.',
        'attribute' => [
            'type' => 'subject',
            'name' => 'full name',
            'company' => 'company',
            'email' => 'email',
            'phone' => 'phone',
            'city' => 'city',
            'subject' => 'title',
            'message' => 'message',
        ],
        'website' => 'Website',
        'subject' => 'What is this about?',
        'none' => 'Not selected',
        'phone_placeholder' => '09121234567',
        'message_placeholder' => 'Project area, city, when you need it, and anything else that helps us answer precisely.',
        'required_note' => 'Fields marked with a star are required.',
        'submit' => 'Send request',
        'direct' => 'Direct contact',
        'head_office' => 'Head office',
        'sales' => 'Sales',
        'technical' => 'Engineering',
        'plant' => 'Plant',
        'hours' => 'Opening hours',
        'faster' => 'This may be quicker',

        'type' => [
            'quote' => ['label' => 'Request a quote', 'hint' => 'Proforma invoice and delivery terms'],
            'technical' => ['label' => 'Technical advice', 'hint' => 'Calculations, product choice, technical files'],
            'distributor' => ['label' => 'Become a distributor', 'hint' => 'Partnering on distribution'],
            'general' => ['label' => 'Something else', 'hint' => 'A visit, a partnership, a general question'],
        ],

        'field' => [
            'name' => 'Full name',
            'company' => 'Company or project',
            'phone' => 'Phone',
            'email' => 'Email',
            'city' => 'City',
            'product' => 'Product of interest (optional)',
            'message' => 'Your message',
        ],

        'shortcut' => [
            'finder' => 'Find the right product for a project',
            'downloads' => 'Download datasheets and technical files',
            'faq' => 'Frequently asked questions',
            'distributor' => 'Nearest distributor',
        ],
    ],

    'distributors' => [
        'lead' => 'For anything less than a full truckload, your nearest distributor is faster and cheaper than delivery straight from the plant.',
        'province' => 'Province:',
        'none_title' => 'No distributor in your province?',
        'none_lead' => 'If you work in building materials and have storage and a distribution network, we will look at the terms with you.',
    ],

    // -------------------------------------------------- Company pages --

    'about' => [
        'story' => 'From one kiln to two extrusion lines',
        'p1' => 'It began in :year with a traditional kiln and five people. At the time the market still did not see ceramic block as a replacement for brick, and most orders came from contractors who had tried it once.',
        'p2' => 'The turn came in :year: a vacuum-controlled extrusion line and a tunnel kiln replaced the old method. Dimensional tolerance went from several millimetres to under two — and that single figure was what opened the door to large projects.',
        'p3' => 'Today we run two parallel lines, a capacity of a hundred and twenty thousand tonnes a year, and a distributor network across more than forty provinces. What has not changed since day one: every production batch is tested before it is loaded.',
        'principles' => 'Three things we do not negotiate on',
        'principles_lead' => 'These are not slogans; if we broke them, a customer would not buy from us twice.',
        'approvals' => 'Approvals',
        'certificates_page' => 'Certificates page',

        'principle' => [
            'tolerance' => ['title' => 'Dimensional tolerance', 'text' => 'If block dimensions are not uniform, laying speed and mortar consumption both go out of control. Ours is held under two millimetres — and the evidence is in every batch report.'],
            'honesty' => ['title' => 'Honest numbers', 'text' => 'We do not round a figure to make it look better. Our datasheets are test results, not marketing claims.'],
            'support' => ['title' => 'Support after the sale', 'text' => 'If something goes wrong on site, our engineer comes out — whether it is our fault or not.'],
        ],
    ],

    'factory' => [
        'lead' => 'Mobarakeh Industrial Estate: two parallel extrusion lines, a hundred-and-ten-metre tunnel kiln, and a laboratory that signs off every batch before it is loaded.',
        'certificates' => 'Certificates and approvals',
        'certificates_lead' => 'The figures on this page mean nothing if nobody has verified them.',
        'download_certificates' => 'Download certificates',
        'visit' => 'Request a factory visit',
    ],

    'technology' => [
        'lead' => 'Making ceramic comes down to three simple things: earth, water and fire. What separates a modern plant from a traditional kiln is precise control of all three.',
        'stages' => 'Nine stages, from quarry to pallet',
        'stages_lead' => 'Each stage has one controlled variable that ruins the finished product if it leaves the permitted window.',
        'duration' => 'Duration',
        'why_900' => 'Why exactly nine hundred degrees?',
        'why_900_p1' => 'Below eight hundred, the mineralogical conversion of the clay does not complete and the product stays vulnerable to moisture. Above a thousand, deformation and local melting begin and dimensional tolerance is lost.',
        'why_900_p2' => 'The working window is narrow. A tunnel kiln with eighteen independently controlled temperature zones exists precisely to stay inside it — and its exhaust heat goes back to the dryer instead of being wasted.',
        'why_900_p3' => 'That one decision cut the energy used per tonne of product by around thirty-eight percent compared with ten years ago.',
        'visit_title' => 'Want to see the line for yourself?',
        'visit_lead' => 'Guided visits for consulting engineers, clients and students can be arranged in advance.',
        'map' => 'Factory map',
    ],

    'sustainability' => [
        'title' => 'Earth → product → building',
        'lead' => 'Ceramic runs a closed cycle with nothing foreign entering it. This page says what we have done — and what we have not done yet.',
        'lifecycle' => 'The life of a block',
        'unsolved' => 'What is still unsolved',
        'unsolved_text' => 'Firing ceramic is inherently energy-intensive. Kiln heat recovery solves part of the problem, not all of it. Replacing a share of the fossil fuel and improving the kiln shell insulation is our plan for the next three years. Every figure written on this page comes from our internal monitoring report and can be shown to a client.',
        'request_report' => 'Request the monitoring report',

        'phase' => [
            'extraction' => ['title' => 'Extraction', 'text' => 'Clay is quarried from our own site eighteen kilometres from the plant. The short haul means lower transport emissions. Every working face carries a mandatory restoration plan.'],
            'production' => ['title' => 'Production', 'text' => 'Kiln exhaust heat returns to the dryer. Green waste from before firing goes back into the line in full; fired waste is crushed and used as sub-base material.'],
            'use' => ['title' => 'In service', 'text' => 'This is where the largest effect happens: the energy saved over fifty years of service is several times the energy used to make the product.'],
            'end_of_life' => ['title' => 'End of life', 'text' => 'Ceramic is an inert mineral material. At demolition it can be crushed and used as fill or sub-base — with no soil contamination.'],
        ],
    ],

    // ------------------------------------ Technical centre and errors --

    'technical' => [
        'lead' => 'Datasheets, catalogues, CAD files, BIM objects, the installation guide and certificates — no sign-up, no call to the sales desk.',
        'files' => ':count files',
        'see_all' => 'See all :count :label files',
        'per_product' => 'Files by product',
        'per_product_lead' => 'If you already know which product you want, this is quicker.',
    ],

    'downloads' => [
        'lead' => 'Every technical file on one page, filtered by kind, audience and format.',
        'name_placeholder' => 'File name…',
        'kind' => 'File kind',
        'audience' => 'Audience',
        'format' => 'Format',
        'clear' => 'Clear filters',
        'apply' => 'Apply filters',
        'empty' => 'No file matches these filters',
        'empty_hint' => 'Clear the filters, or ask our engineers for it.',
        'request' => 'Request a file',
    ],

    'installation' => [
        'lead' => 'Even the best block loses its performance if it is laid wrongly. This page is written for the person laying it, not for the archive.',
        'stages' => 'Laying a ceramic wall, stage by stage',
        'faq' => 'What installers ask most',
        'files' => 'Installation guide files',
        'on_site' => 'Technical support on site',
        'on_site_lead' => 'For projects over a thousand square metres, our engineer comes to the site at the start and briefs the crew.',
        'request' => 'Request an engineer',

        'stage' => [
            'bed' => ['title' => 'Prepare the bed', 'text' => 'The surface under the first course must be clean, level and free of dust. Check the first course with a straightedge and a laser level; an error here is amplified up the height of the wall.'],
            'mortar' => ['title' => 'The right mortar', 'text' => 'A 1:5 sand–cement mortar or a ready-mixed masonry mortar. It should be workable but not sloppy. Consumption for each product is on its datasheet.'],
            'damp' => ['title' => 'Damp the block', 'text' => 'Damp on the surface, not soaked. A dry block draws the water out of the mortar; a wet one destroys adhesion.'],
            'coursing' => ['title' => 'Coursing', 'text' => 'A full, even bed joint. With tongue-and-groove the perpend joint disappears. Check plumb and level every third course.'],
            'lintel' => ['title' => 'Lintels', 'text' => 'No opening is built without a lintel. A ceramic lintel acts as permanent formwork and keeps the thermal continuity of the facade.'],
            'frame' => ['title' => 'Tying into the frame', 'text' => 'An infill wall must be restrained to the frame with posts and ties per the seismic requirements. This is the stage inspections pick up most often.'],
            'chasing' => ['title' => 'Chasing for services', 'text' => 'Vertical chases are allowed. A horizontal chase must not exceed one third of the wall thickness and must be cut with a disc, not knocked out.'],
            'finish' => ['title' => 'Finishing', 'text' => 'Give the wall at least forty-eight hours before plastering. The block’s grooved face provides the key for the render.'],
        ],
    ],

    'certificates' => [
        'lead' => 'Every figure in our datasheets has a test behind it. This page lists those tests.',
        'company' => 'Company certificates',
        'number' => 'No. :number',
        'standards' => 'Reference codes and standards',
        'report' => 'Need a test report of your own?',
        'report_lead' => 'For large projects we provide a test report for that project’s own production batch, from our laboratory or an accredited one.',
        'report_cta' => 'Request a test report',
    ],

    'faq' => [
        'lead' => 'Short, direct answers from our engineers to the questions we hear most.',
        'groups' => 'Question groups',
        'not_found' => 'Didn’t find your answer?',
        'not_found_lead' => 'Our engineers answer within one working day at most.',
        'ask' => 'Send us your question',
        'group' => [
            'technical' => 'Specifications',
            'installation' => 'On site',
            'order' => 'Ordering and delivery',
            'general' => 'General',
        ],
    ],

    'error' => [
        '404' => [
            'title' => 'This page was not found',
            'lead' => 'The address may have changed, or the product may have left the catalogue. You can carry on from here:',
            'search' => 'What were you looking for?',
        ],
        '500' => [
            'title' => 'Something went wrong on the server',
            'lead' => 'This is our problem and we are on it. Please try again in a few minutes, or contact us directly.',
        ],
        '503' => [
            'title' => 'The site is briefly unavailable',
            'lead' => 'We are updating the site. Come back in a few minutes, or contact us directly.',
        ],
    ],

    // ---------------------------------------- Shop and accessibility --

    'shop' => [
        'lead' => 'Several vendors supply each product. Compare their prices side by side and buy from whichever one suits your project.',
        'empty' => 'Nothing is offered for sale yet.',
        'from' => 'from',
        'added' => '“:name” was added to the basket.',
        'removed' => 'The line was removed from the basket.',
        'vendors' => ':count vendors',
        'full_specs' => 'Full specifications',
        'offers_lead' => ':count vendors supply this product — cheapest first.',
        'per_unit' => 'per :unit',
        'min_order' => 'Minimum order: :value :unit',
        'stock' => 'In stock: :value',
        'lead_time' => 'Lead time: :days days',
        'quantity' => 'Quantity',
        'add' => 'Add to basket',
        'cart' => 'Basket',
        'cart_empty' => 'Your basket is empty.',
        'go' => 'Go to the shop',
        'update' => 'Update',
        'remove' => 'Remove “:name” from the basket',
        'total' => 'Total',
        'checkout' => 'Checkout',
        'checkout_lead' => 'There is no online payment. Your order is recorded and the vendor calls you to agree quantity, haulage and delivery time.',
        'place' => 'Place order',
        'summary' => 'Order summary',
        'placed' => 'Order placed',
        'placed_lead' => 'Your order number is :number. Bookmark this page — the order status updates here.',
        'status' => 'Status',
        'back' => 'Back to the shop',

        'currency' => [
            'تومان' => 'toman',
        ],

        'status_label' => [
            'new' => 'Placed',
            'confirmed' => 'Confirmed',
            'shipped' => 'Shipped',
            'done' => 'Delivered',
            'canceled' => 'Cancelled',
        ],

        'field' => [
            'name' => 'Full name',
            'phone' => 'Mobile',
            'email' => 'Email (optional)',
            'province' => 'Province',
            'city' => 'City',
            'address' => 'Delivery address',
            'note' => 'Note (optional)',
        ],
    ],

    /*
    | Labels only a screen reader reads.
    |
    | They are never seen, but for someone using a screen reader they are the
    | only text that element has — so leaving them untranslated means that
    | part of the site is, for that person, not translated at all.
    */
    'a11y' => [
        'block_3d' => '3D view of :name, :size cm — rotate with the arrow keys',
        'block_section' => 'Horizontal section of :name — :rows rows and :cols columns of cavities',
        'score' => ':label :value out of 100',
        'factory_plan' => 'Aerial view of the factory: the production line, tunnel kiln, dryer, laboratory, warehouse and packing',
    ],

    // ---------------------------------------- Page titles and meta (SEO) --
    //
    // These land in <title> and in the search snippet, so each language
    // needs its own version — not a word-for-word translation, but what
    // someone searching in that language actually types.

    'seo' => [
        'default' => [
            'title' => 'Ceramic blocks and building materials',
            'description' => 'Ceramic blocks and building materials made for durability, insulation and engineered performance — with datasheets, CAD files and BIM objects for engineers and architects.',
        ],
        'crumb_result' => 'Result',
        'crumb_downloads' => 'Downloads',

        'about' => ['title' => 'About us', 'description' => 'Over two decades of engineered ceramic block: from a single traditional kiln to a fully automated line producing a hundred and twenty thousand tonnes a year.'],
        'articles' => ['title' => 'Technical knowledge — articles on ceramic and building', 'description' => 'Technical articles on thermal insulation, building codes, material comparisons, laying ceramic walls and saving energy.'],
        'contact' => ['title' => 'Contact us and request a quote', 'description' => 'Request a proforma invoice, technical advice, a distribution partnership or a factory visit.'],
        'distributors' => ['title' => 'Distributors nationwide', 'description' => 'Official ceramic block distributors by province, with phone numbers and addresses.'],
        'factory' => ['title' => 'The factory — line, kiln and laboratory', 'description' => 'The whole plant: production line, tunnel kiln, dryer, quality control laboratory, warehouse and packing.'],
        'products' => ['title' => 'Products — ceramic blocks and building materials', 'description' => 'The full catalogue of wall, partition, insulating and roofing ceramic blocks, with specifications, dimensions, compressive strength and thermal conductivity.'],
        'product' => ['title' => ':name — specifications and datasheet'],
        'finder' => ['title' => 'Find the right product for your project', 'description' => 'In four simple steps, find the ceramic block that matches your project type, wall type, thickness and insulation requirement.'],
        'finder_results' => ['title' => 'Product finder result', 'description' => 'Products recommended from your project details.'],
        'projects' => ['title' => 'Projects built with ceramic block', 'description' => 'Residential, commercial, industrial and mass-housing projects built with our ceramic blocks.'],
        'search' => ['title' => 'Search: :term'],
        'solutions' => ['title' => 'Building solutions', 'description' => 'Solutions for external walls, internal partitions, roofing and thermal insulation using ceramic block systems.'],
        'sustainability' => ['title' => 'Sustainability — from earth to building and back', 'description' => 'Ceramic is an inert, recyclable mineral. Life-cycle performance, kiln heat recovery and waste management at the plant.'],
        'technical' => ['title' => 'Technical centre — datasheets, CAD, BIM and code', 'description' => 'Everything an engineer, architect or contractor needs: product datasheets, the technical catalogue, DWG and IFC files, Revit objects, the installation guide and certificates.'],
        'downloads' => ['title' => 'Technical file download centre', 'description' => 'Download datasheets, catalogues, CAD and BIM files, the installation guide and product certificates.'],
        'installation' => ['title' => 'Installation guide — laying ceramic block correctly', 'description' => 'Laying a ceramic wall step by step: bed preparation, mortar, coursing, lintels, tying into the frame and the contractor’s check points.'],
        'certificates' => ['title' => 'Certificates and standards', 'description' => 'National standards, building code requirements, ISO 9001 and test reports from accredited laboratories.'],
        'faq' => ['title' => 'Technical FAQ', 'description' => 'Our engineers answer the common questions about ceramic block, insulation, laying and ordering.'],
        'technology' => ['title' => 'From earth to structure — production technology', 'description' => 'The nine stages of making a ceramic block, from quarried clay to quality control and packing, with firing temperature, dryer time and the figures for each stage.'],
        'shop' => ['description' => 'Buy ceramic block direct from vendors — compare several vendors’ prices for each product.'],
        'shop_product' => ['title' => ':name — buy', 'description' => 'Price and terms for “:name” from :count vendors.'],
        'order' => ['title' => 'Order :number'],
    ],

    'document' => [
        'category' => [
            'datasheet' => 'Product datasheet',
            'catalog' => 'Technical catalogue',
            'cad' => 'CAD file',
            'bim' => 'BIM object',
            'installation' => 'Installation guide',
            'certificate' => 'Certificate',
            'standard' => 'Standards and code',
        ],
        'audience' => [
            'customer' => 'Client and buyer',
            'engineer' => 'Engineer and architect',
            'contractor' => 'Contractor and installer',
        ],
    ],

    'gallery' => [
        'title' => 'Gallery',
    ],

];
