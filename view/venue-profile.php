<?php
// session_start();
// // Enable Error Reporting for debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// 1. Settings
require_once __DIR__ . '/../settings/core.php';

// 2. Load Controller (Using Relative Path - Matches Debug Script)
$controller_path = __DIR__ . '/../controllers/venue_controller.php';
if (file_exists($controller_path)) {
    require_once $controller_path;
} else {
    die("Critical Error: Controller not found at " . htmlspecialchars($controller_path));
}

// 3. Get Venue ID
if (!isset($_GET['id'])) {
    die('<div style="color: white; background: #0f0f13; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif;">Error: No Venue ID provided. Please access this page from the venue list.</div>');
}
$venue_id = intval($_GET['id']);

// 4. Fetch Data
if (!function_exists('get_venue_details_ctr')) {
    die("Error: Function 'get_venue_details_ctr' missing. Ensure controllers/venue_controller.php is the FUNCTIONAL version.");
}

$venue = get_venue_details_ctr($venue_id);

if (!$venue) {
    die('<div style="color: white; background: #0f0f13; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif;">Venue not found in database.</div>');
}

// 5. Prepare Data
$name = htmlspecialchars($venue['name']);
$address = htmlspecialchars($venue['address']);
$cost = number_format(floatval($venue['cost_per_hour']), 2);
$rating = isset($venue['rating']) ? floatval($venue['rating']) : 0;

// Handle Images: Ensure it's an array. If DB has `["url1", "url2"]`, the controller has already decoded it.
$images = is_array($venue['image_urls']) ? $venue['image_urls'] : [];

// Handle Amenities
$amenities = is_array($venue['amenities']) ? $venue['amenities'] : [];

$lat = $venue['latitude'] ?? null;
$lng = $venue['longitude'] ?? null;

// Background Image (First image or fallback)
$hero_bg = !empty($images) ? $images[0] : 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name; ?> - Venue Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="pb-20">

    <!-- Navbar -->
    <nav class="absolute top-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-gradient-to-b from-black/80 to-transparent">
        <a href="venue-portal.php" class="p-2 bg-black/40 hover:bg-black/60 backdrop-blur-md rounded-full text-white transition-colors">
            <i data-lucide="arrow-left" size="20"></i>
        </a>
        <div class="flex gap-3">
            <button class="p-2 bg-black/40 hover:bg-black/60 backdrop-blur-md rounded-full text-white"><i data-lucide="share-2" size="20"></i></button>
            <button class="p-2 bg-black/40 hover:bg-black/60 backdrop-blur-md rounded-full text-white"><i data-lucide="heart" size="20"></i></button>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative h-[50vh] w-full bg-brand-card">
        <img id="hero-image" src="<?php echo htmlspecialchars($hero_bg); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/20 to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 w-full p-6 lg:p-10">
            <div class="max-w-7xl mx-auto">
                <span class="inline-block px-3 py-1 bg-brand-accent text-black text-xs font-bold rounded-full mb-3 uppercase tracking-wider">Available Now</span>
                <h1 class="text-4xl md:text-5xl font-black mb-2 text-white shadow-sm"><?php echo $name; ?></h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300">
                    <span class="flex items-center gap-1"><i data-lucide="map-pin" size="14" class="text-brand-accent"></i> <?php echo $address; ?></span>
                    <?php if($rating > 0): ?>
                        <span class="flex items-center gap-1"><i data-lucide="star" size="14" class="text-yellow-500 fill-yellow-500"></i> <?php echo $rating; ?> (120 reviews)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 lg:px-8 -mt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Gallery: Handles Multiple Images -->
                <?php if(!empty($images)): ?>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x">
                    <?php foreach($images as $index => $img): ?>
                        <img 
                            src="<?php echo htmlspecialchars($img); ?>" 
                            onclick="openLightbox(<?php echo $index; ?>)"
                            class="h-32 w-48 object-cover rounded-xl border-2 border-white/5 hover:border-brand-accent transition-all cursor-pointer flex-shrink-0 snap-center"
                        >
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Amenities -->
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                    <h3 class="font-bold text-lg mb-4 text-white">Amenities</h3>
                    <?php if(!empty($amenities)): ?>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach($amenities as $amenity): ?>
                                <span class="px-4 py-2 bg-white/5 rounded-lg text-sm text-gray-300 flex items-center gap-2 border border-white/5">
                                    <i data-lucide="check-circle" size="14" class="text-brand-accent"></i> <?php echo htmlspecialchars($amenity); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No specific amenities listed.</p>
                    <?php endif; ?>
                </div>

                <!-- Map -->
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                    <h3 class="font-bold text-lg mb-4 text-white">Location</h3>
                    <div id="map" class="w-full h-64 rounded-xl bg-gray-800"></div>
                </div>

            </div>

            <!-- Right Column: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5 shadow-2xl sticky top-24">
                    <div class="flex justify-between items-end mb-6">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase">Price per hour</p>
                            <h2 class="text-3xl font-black text-brand-accent">GHS <?php echo $cost; ?></h2>
                        </div>
                        <div class="text-right">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-green-500 font-bold ml-1">Open today</span>
                        </div>
                    </div>

                    <hr class="border-white/10 mb-6">

                    <div class="space-y-4">
                        <a href="create_event.php?venue_id=<?php echo $venue_id; ?>" class="block w-full py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-center rounded-xl transition-transform hover:scale-[1.02] shadow-lg shadow-brand-accent/20">
                            Book This Venue
                        </a>
                        <button class="block w-full py-4 bg-white/5 hover:bg-white/10 text-white font-bold text-center rounded-xl transition-colors border border-white/10">
                            Contact Manager
                        </button>
                    </div>

                    <p class="text-[10px] text-center text-gray-500 mt-4">
                        <i data-lucide="shield-check" size="10" class="inline"></i> Verified Venue Partner
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 z-[100] bg-black/95 hidden flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <!-- Close Button -->
        <button onclick="closeLightbox()" class="absolute top-6 right-6 p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-full transition-colors z-50">
            <i data-lucide="x" size="32"></i>
        </button>
        
        <!-- Prev Button -->
        <button onclick="navigateLightbox(-1)" class="absolute left-4 md:left-8 p-3 text-white/50 hover:text-white hover:bg-white/10 rounded-full transition-colors z-50">
            <i data-lucide="chevron-left" size="48"></i>
        </button>
        
        <!-- Image Container -->
        <div class="relative w-full h-full flex items-center justify-center p-4 md:p-12">
            <img id="lightbox-img" src="" class="max-h-full max-w-full object-contain rounded-lg shadow-2xl scale-95 transition-all duration-300 ease-out">
        </div>
        
        <!-- Next Button -->
        <button onclick="navigateLightbox(1)" class="absolute right-4 md:right-8 p-3 text-white/50 hover:text-white hover:bg-white/10 rounded-full transition-colors z-50">
            <i data-lucide="chevron-right" size="48"></i>
        </button>
        
        <!-- Counter -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/50 text-sm font-mono bg-black/50 px-4 py-1 rounded-full border border-white/10">
            <span id="lightbox-counter">1</span> / <span id="lightbox-total">1</span>
        </div>
    </div>

    <!-- Configuration for JS -->
    <script>
        // Pass PHP data to Global JS Scope for Map
        window.VENUE_DATA = {
            lat: <?php echo $lat ? $lat : 'null'; ?>,
            lng: <?php echo $lng ? $lng : 'null'; ?>,
            name: "<?php echo $name; ?>",
            address: "<?php echo $address; ?>"
        };
        // Pass Images for Lightbox
        window.GALLERY_IMAGES = <?php echo json_encode($images); ?>;
    </script>

    <!-- INLINE JS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
            
            // Check Google Maps
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                loadVenueData();
            } else {
                window.addEventListener('load', loadVenueData);
            }

            // Keyboard Navigation for Lightbox
            document.addEventListener('keydown', (e) => {
                if (document.getElementById('lightbox').classList.contains('hidden')) return;
                
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') navigateLightbox(-1);
                if (e.key === 'ArrowRight') navigateLightbox(1);
            });
        });

        // --- Lightbox Logic ---
        let currentImageIndex = 0;

        function openLightbox(index) {
            if (!window.GALLERY_IMAGES || window.GALLERY_IMAGES.length === 0) return;
            
            currentImageIndex = index;
            updateLightboxImage();
            
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('hidden');
            // Small delay to allow display:flex to apply before opacity transition
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                const img = document.getElementById('lightbox-img');
                img.classList.remove('scale-95');
                img.classList.add('scale-100');
            }, 10);
            
            document.body.style.overflow = 'hidden'; // Prevent scrolling background
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            
            lightbox.classList.add('opacity-0');
            img.classList.add('scale-95');
            img.classList.remove('scale-100');
            
            setTimeout(() => {
                lightbox.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
            }, 300);
        }

        function navigateLightbox(step) {
            const total = window.GALLERY_IMAGES.length;
            currentImageIndex = (currentImageIndex + step + total) % total;
            updateLightboxImage();
        }

        function updateLightboxImage() {
            const img = document.getElementById('lightbox-img');
            const counter = document.getElementById('lightbox-counter');
            const total = document.getElementById('lightbox-total');
            
            // Fade out slightly during swap
            img.style.opacity = '0.5';
            setTimeout(() => {
                img.src = window.GALLERY_IMAGES[currentImageIndex];
                img.style.opacity = '1';
            }, 150);

            counter.textContent = currentImageIndex + 1;
            total.textContent = window.GALLERY_IMAGES.length;
        }

        // --- Map Logic ---
        function loadVenueData() {
            if (typeof window.VENUE_DATA !== 'undefined' && window.VENUE_DATA) {
                initVenueMap(window.VENUE_DATA);
            }
        }

        function initVenueMap(data) {
            const mapEl = document.getElementById("map");
            if (!mapEl) return;

            const venueLat = parseFloat(data.lat);
            const venueLng = parseFloat(data.lng);
            const venueName = data.name || "Venue";
            const venueAddr = data.address || "Accra, Ghana";

            const darkMapStyle = [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] }
            ];

            const mapOptions = {
                zoom: 15,
                center: { lat: 5.6037, lng: -0.1870 },
                styles: darkMapStyle,
                disableDefaultUI: false
            };

            const map = new google.maps.Map(mapEl, mapOptions);

            if (!isNaN(venueLat) && !isNaN(venueLng)) {
                const pos = { lat: venueLat, lng: venueLng };
                map.setCenter(pos);
                new google.maps.Marker({ position: pos, map: map, title: venueName });
            } else if (venueAddr) {
                const geocoder = new google.maps.Geocoder();
                const searchAddr = venueAddr.toLowerCase().includes('accra') ? venueAddr : venueAddr + ", Accra";
                
                geocoder.geocode({ 'address': searchAddr }, function(results, status) {
                    if (status === 'OK') {
                        map.setCenter(results[0].geometry.location);
                        new google.maps.Marker({ map: map, position: results[0].geometry.location, title: venueName });
                    }
                });
            }
        }
    </script>
</body>
</html>