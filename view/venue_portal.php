<?php
session_start();
// Force login to view this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_to_list_venue");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Venue - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- GOOGLE MAPS API (FOR AUTOCOMPLETE, GEOLOCATION, AND MAP) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>
    
    <!-- ALPINE.JS (Lightweight interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        #venue-map {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @media (min-width: 1024px) { #venue-map { height: 350px; } }
        /* Custom file input styling */
        .custom-file-input {
            opacity: 0;
            position: absolute;
            z-index: 10;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <nav class="absolute top-0 w-full p-6 flex justify-between items-center z-50">
        <h1 class="text-2xl font-black tracking-tighter text-white">
            HAAAH<span class="text-brand-purple text-base font-normal tracking-widest ml-1">VENUES</span>
        </h1>
        <a href="index.php" class="text-sm font-bold hover:text-brand-accent">Back to App</a>
    </nav>

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- Left Side: Sales Pitch (Marketing Content) - STATIC TOP ALIGNED -->
        <!-- Fixed: Using py-20 and standard flow to ensure content starts high without complex flex rules -->
        <div class="lg:w-1/2 bg-brand-card relative p-12 pt-24 lg:p-20 order-1 lg:order-1">
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1522778119026-b6d47f0565c6a?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-20 grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-card to-transparent"></div>
            </div>
            <div class="relative z-10 max-w-lg lg:mx-auto"> 
                <h1 class="text-5xl font-black mb-6 leading-tight">
                    Empty Pitch? <br><span class="text-brand-purple">Lost Revenue.</span>
                </h1>
                <p class="text-gray-400 text-lg mb-8">
                    Join Ghana's largest network of sports facilities. We bring the players, handle the payments, and ensure your slots are filled.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Guaranteed automated payments. No cash handling.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Dashboard to manage bookings and maintenance.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Marketing to 10,000+ local players.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form (Includes Map) -->
        <div class="lg:w-1/2 bg-brand-dark flex items-start justify-center p-12 pt-24 order-2 lg:order-2">
            <div class="max-w-md w-full">
                <h2 class="text-2xl font-bold mb-2">List your venue</h2>
                <p class="text-gray-500 mb-8 text-sm">Submit details for admin verification.</p>

                <form action="../actions/create_venue_action.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Venue Name</label>
                        <input type="text" name="venue_name" required placeholder="e.g. Osu Community Park" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                    </div>

                    <!-- Address Input (Map Sync) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address (Start typing or drag map pin)</label>
                        <div class="relative">
                            <input type="text" name="venue_address" id="address_input" required placeholder="Street address..." class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none pr-12">
                            <button type="button" onclick="useCurrentLocation()" class="absolute right-1 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-brand-purple transition-colors rounded-lg" title="Use my current location">
                                <i data-lucide="crosshair" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- MAP INTEGRATION -->
                    <div class="pt-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-2">Confirm Pitch Location</h3>
                        <div id="venue-map" class="w-full"></div>
                        <p class="text-xs text-gray-500 mt-2">Drag the marker to pinpoint the exact location.</p>
                    </div>

                    <!-- Hidden fields to store Lat/Lng -->
                    <input type="hidden" name="lat" id="lat_input" value=""> 
                    <input type="hidden" name="lng" id="lng_input" value="">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cost per Hour (GHS)</label>
                            <input type="number" name="cost_per_hour" required value="150" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Max Player Capacity</label>
                            <input type="number" name="capacity" value="20" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                    </div>
                    
                    <!-- NEW: DIMENSIONS INPUTS -->
                    <div class="space-y-2 col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pitch Dimensions (For Verification)</label>
                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <input type="number" name="pitch_length" placeholder="Length (m)" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                            </div>
                            <div>
                                <input type="number" name="pitch_width" placeholder="Width (m)" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 pt-1">Standard 5v5: 35x25m | Standard 11v11: 100x64m</p>
                    </div>


                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none"></textarea>
                    </div>

                    <!-- AMENITIES (Updated) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Key Amenities</label>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-400">
                            <label class="flex items-center gap-2"><input type="checkbox" name="amenities[]" value="Floodlights" class="accent-brand-purple"> Floodlights</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="amenities[]" value="Changing Rooms" class="accent-brand-purple"> Changing Rooms</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="amenities[]" value="Parking" class="accent-brand-purple"> Parking</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="amenities[]" value="Water/Restrooms" class="accent-brand-purple"> Water/Restrooms</label>
                        </div>
                    </div>
                    
                    <!-- CUSTOM AMENITIES -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Other Amenities (comma separated)</label>
                        <input type="text" name="custom_amenities" placeholder="e.g. Free Wi-Fi, Juice Bar" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                    </div>

                    <!-- Contact -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone</label>
                            <input type="tel" name="phone" required class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                            <input type="email" name="email" required class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                    </div>

                    <!-- Image Upload (Consolidated) -->
                    <div x-data="{ 
                        images: [], maxFiles: 4, 
                        previewFiles(event) { 
                            this.images = [];
                            const files = Array.from(event.target.files);
                            for (let i = 0; i < Math.min(files.length, this.maxFiles); i++) {
                                const reader = new FileReader();
                                reader.onload = (e) => { this.images.push(e.target.result); };
                                reader.readAsDataURL(files[i]);
                            }
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Venue Images (<span class="text-brand-accent">At least 1 required</span>: Exterior & Pitch View)</label>
                        
                        <!-- Image Preview Grid -->
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <template x-for="(image, index) in Array(maxFiles).fill(null)" :key="index">
                                <div class="relative w-full h-16 bg-white/5 rounded-md flex items-center justify-center overflow-hidden border border-white/10" :class="{'p-0': images[index]}">
                                    <img x-show="images[index]" :src="images[index]" class="w-full h-full object-cover" alt="Preview">
                                    <i x-show="!images[index]" data-lucide="image" class="w-5 h-5 text-gray-500"></i>
                                    <span x-show="!images[index]" class="absolute bottom-1 text-[8px] text-gray-500" x-text="index === 0 ? 'Main' : index === 1 ? 'Pitch' : index + 1"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Single File Input with required attribute -->
                        <div class="relative flex items-center justify-center p-3 border-2 border-dashed border-white/10 hover:border-brand-purple/50 rounded-lg transition-colors cursor-pointer">
                            <input type="file" name="venue_images[]" accept="image/*" multiple required x-on:change="previewFiles($event)" class="custom-file-input">
                            <span class="text-sm text-gray-400 flex items-center gap-2">
                                <i data-lucide="upload-cloud" class="w-4 h-4"></i> Click to select up to 4 images
                            </span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="submit_venue" class="w-full bg-brand-purple hover:bg-purple-600 text-white font-bold py-4 rounded-xl transition-colors">
                            List Venue Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    <script>
        lucide.createIcons();

        let map;
        let marker;
        let geocoder;

        function initMapAndAutocomplete() {
            const addressInput = document.getElementById('address_input');
            const latInput = document.getElementById('lat_input');
            const lngInput = document.getElementById('lng_input');

            // --- 1. Map Setup (Default to Accra) ---
            const initialCenter = { lat: 5.6037, lng: -0.1870 }; // Accra, Ghana
            geocoder = new google.maps.Geocoder();

            const darkMapStyle = [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] }
            ];

            map = new google.maps.Map(document.getElementById('venue-map'), {
                zoom: 13,
                center: initialCenter,
                styles: darkMapStyle,
                mapTypeControl: false,
                streetViewControl: false
            });

            // --- 2. Marker Setup (Draggable) ---
            marker = new google.maps.Marker({
                map: map,
                position: initialCenter,
                draggable: true,
                title: "Venue Location"
            });
            
            // Set initial hidden fields
            latInput.value = initialCenter.lat;
            lngInput.value = initialCenter.lng;
            
            // --- 3. Autocomplete Listener (Text Input -> Map) ---
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address'],
                componentRestrictions: { country: 'gh' } 
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    const pos = place.geometry.location;
                    map.setCenter(pos);
                    marker.setPosition(pos);
                    updateCoordinates(pos.lat(), pos.lng());
                }
            });

            // --- 4. Drag Listener (Map -> Text Input) ---
            marker.addListener('dragend', function() {
                const pos = marker.getPosition();
                map.setCenter(pos);
                updateCoordinates(pos.lat(), pos.lng());
                reverseGeocode(pos);
            });
            
            // --- NEW: Geolocation Functionality ---
            window.useCurrentLocation = function() {
                if (!navigator.geolocation) {
                    alert("Geolocation is not supported by your browser.");
                    return;
                }
                
                // Show locating feedback
                addressInput.value = "Locating...";
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const pos = { lat: lat, lng: lng };
                        
                        map.setCenter(pos);
                        map.setZoom(17); // Zoom in close for pitch location
                        marker.setPosition(pos);
                        updateCoordinates(lat, lng);
                        reverseGeocode(pos); // Get the street address
                    },
                    (error) => {
                        console.error("Geolocation failed:", error);
                        addressInput.value = "Failed to find location. Please type manually.";
                    }
                );
            }
            
            // --- Helper Functions ---
            
            function updateCoordinates(lat, lng) {
                latInput.value = lat;
                lngInput.value = lng;
                console.log("Coordinates Updated:", lat, lng);
            }
            
            function reverseGeocode(latLng) {
                geocoder.geocode({ 'location': latLng }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        // Fill address input with readable address
                        addressInput.value = results[0].formatted_address;
                        addressInput.classList.remove('is-empty');
                    } else {
                        // Keep user-entered text but log error
                        console.error('Reverse Geocode failed: ' + status);
                    }
                });
            }
        }

        // Initialize map once the Google Maps script is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                initMapAndAutocomplete();
            } else {
                setTimeout(() => {
                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        initMapAndAutocomplete();
                    }
                }, 1000);
            }
        });

    </script>
</body>
</html>