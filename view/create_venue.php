<?php
session_start();
require_once __DIR__ . '/../settings/core.php';

// 1. Force Login
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
    
    <!-- GOOGLE MAPS API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>

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
            height: 350px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f0f13; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <!-- Nav -->
    <nav class="absolute top-0 w-full p-6 flex justify-between items-center z-50 bg-gradient-to-b from-black/80 to-transparent">
        <h1 class="text-2xl font-black tracking-tighter text-white">
            HAAAH<span class="text-brand-purple text-base font-normal tracking-widest ml-1">PARTNER</span>
        </h1>
        <a href="venue-portal.php" class="flex items-center gap-2 text-sm font-bold text-gray-300 hover:text-white transition-colors">
            <i data-lucide="arrow-left" size="16"></i> Cancel
        </a>
    </nav>

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- LEFT: Marketing Side -->
        <div class="lg:w-5/12 bg-brand-card relative p-8 pt-24 lg:p-16 flex flex-col justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1522778119026-b6d47f0565c6a?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-20 grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-card via-brand-card/80 to-transparent"></div>
            </div>

            <div class="relative z-10">
                <span class="inline-block px-3 py-1 rounded-full bg-brand-purple/20 text-brand-purple text-xs font-bold uppercase tracking-wider mb-6 border border-brand-purple/20">Venue Partner Program</span>
                <h1 class="text-4xl lg:text-5xl font-black mb-6 leading-tight text-white">
                    Monetize Your <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-brand-purple">Pitch Today.</span>
                </h1>
                <p class="text-gray-400 text-lg mb-8 leading-relaxed">
                    Join Ghana's premier sports network. We handle the bookings, payments, and marketing so you can focus on maintaining the turf.
                </p>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-brand-accent shrink-0 border border-white/5">
                            <i data-lucide="credit-card" size="24"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white">Automated Revenue</h4>
                            <p class="text-sm text-gray-500 mt-1">Payments are processed instantly and secured via Paystack.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-brand-purple shrink-0 border border-white/5">
                            <i data-lucide="users" size="24"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white">Community Access</h4>
                            <p class="text-sm text-gray-500 mt-1">Connect with 10,000+ local players looking for a game.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Form Side -->
        <div class="lg:w-7/12 bg-brand-dark flex flex-col p-6 pt-24 lg:p-16 overflow-y-auto">
            <div class="max-w-2xl mx-auto w-full">
                
                <form action="../actions/create_venue_action.php" method="POST" enctype="multipart/form-data" class="space-y-8">
                    
                    <!-- Section 1: Basic Info -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold flex items-center gap-2 pb-2 border-b border-white/10">
                            <i data-lucide="info" size="20" class="text-gray-500"></i> Basic Details
                        </h3>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Venue Name</label>
                            <input type="text" name="venue_name" required placeholder="e.g. Osu Community Astro Turf" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-white focus:border-brand-accent focus:outline-none transition-colors">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hourly Rate (GHS)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">₵</span>
                                    <input type="number" name="cost_per_hour" required value="150" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 pl-10 text-white font-mono font-bold focus:border-brand-accent focus:outline-none transition-colors">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Max Capacity</label>
                                <input type="number" name="capacity" value="20" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-white focus:border-brand-accent focus:outline-none transition-colors">
                            </div>
                        </div>

                        <!-- Pitch Dimensions (Updated) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pitch Dimensions (Meters)</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <input type="number" name="pitch_length" placeholder="Length" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 pr-10 text-white focus:border-brand-accent focus:outline-none transition-colors">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-xs">m</span>
                                </div>
                                <div class="relative">
                                    <input type="number" name="pitch_width" placeholder="Width" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 pr-10 text-white focus:border-brand-accent focus:outline-none transition-colors">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-xs">m</span>
                                </div>
                            </div>
                            <!-- UPDATED DIMENSIONS HINT -->
                            <p class="text-[10px] text-gray-500 mt-2 flex flex-wrap gap-2">
                                <i data-lucide="info" size="16"></i> Common Sizes:
                                <span class="bg-white/5 px-2 py-1 rounded">5v5: ~35x25m</span>
                                <span class="bg-white/5 px-2 py-1 rounded">7v7: ~55x35m</span>
                                <span class="bg-white/5 px-2 py-1 rounded">11v11: ~100x64m</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                            <textarea name="description" rows="3" placeholder="Describe the turf quality, lighting, and vibe..." class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-white focus:border-brand-accent focus:outline-none transition-colors"></textarea>
                        </div>
                    </div>

                    <!-- Section 2: Location -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold flex items-center gap-2 pb-2 border-b border-white/10">
                            <i data-lucide="map-pin" size="20" class="text-gray-500"></i> Location
                        </h3>

                        <div class="relative">
                            <input type="text" name="venue_address" id="address_input" required placeholder="Start typing address..." class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 pr-12 text-white focus:border-brand-accent focus:outline-none transition-colors">
                            <button type="button" onclick="useCurrentLocation()" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-white bg-white/5 rounded-lg transition-colors" title="Use GPS">
                                <i data-lucide="crosshair" size="18"></i>
                            </button>
                        </div>

                        <!-- Map Container -->
                        <div id="venue-map"></div>
                        <p class="text-xs text-brand-accent flex items-center gap-1">
                            <i data-lucide="info" size="12"></i> Drag the marker to pin the exact entrance location.
                        </p>

                        <!-- Hidden Lat/Lng -->
                        <input type="hidden" name="lat" id="lat_input">
                        <input type="hidden" name="lng" id="lng_input">
                    </div>

                    <!-- Section 3: Details & Media -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold flex items-center gap-2 pb-2 border-b border-white/10">
                            <i data-lucide="layers" size="20" class="text-gray-500"></i> Amenities & Media
                        </h3>

                        <!-- Amenities -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="amenities[]" value="Floodlights" class="peer hidden">
                                <div class="px-3 py-2 rounded-lg bg-[#1a1a23] border border-white/10 text-gray-400 text-sm peer-checked:bg-brand-accent peer-checked:text-black peer-checked:font-bold transition-all text-center">
                                    Floodlights
                                </div>
                            </label>
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="amenities[]" value="Changing Rooms" class="peer hidden">
                                <div class="px-3 py-2 rounded-lg bg-[#1a1a23] border border-white/10 text-gray-400 text-sm peer-checked:bg-brand-accent peer-checked:text-black peer-checked:font-bold transition-all text-center">
                                    Changing
                                </div>
                            </label>
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="amenities[]" value="Parking" class="peer hidden">
                                <div class="px-3 py-2 rounded-lg bg-[#1a1a23] border border-white/10 text-gray-400 text-sm peer-checked:bg-brand-accent peer-checked:text-black peer-checked:font-bold transition-all text-center">
                                    Parking
                                </div>
                            </label>
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="amenities[]" value="Restrooms" class="peer hidden">
                                <div class="px-3 py-2 rounded-lg bg-[#1a1a23] border border-white/10 text-gray-400 text-sm peer-checked:bg-brand-accent peer-checked:text-black peer-checked:font-bold transition-all text-center">
                                    Restrooms
                                </div>
                            </label>
                        </div>
                        
                        <input type="text" name="custom_amenities" placeholder="Other amenities (e.g. Wi-Fi, Water Stand)..." class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-sm text-white focus:border-brand-accent focus:outline-none transition-colors">

                        <!-- Contact -->
                        <div class="grid grid-cols-2 gap-4">
                            <input type="tel" name="phone" required placeholder="Manager Phone" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-sm text-white focus:border-brand-accent focus:outline-none">
                            <input type="email" name="email" required placeholder="Business Email" class="w-full bg-[#1a1a23] border border-white/10 rounded-xl p-4 text-sm text-white focus:border-brand-accent focus:outline-none">
                        </div>

                        <!-- Image Upload (Drag & Drop + Delete) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Venue Photos (Max 4)</label>
                            
                            <!-- Drop Zone -->
                            <div id="drop-zone" class="border-2 border-dashed border-white/10 hover:border-brand-purple hover:bg-brand-purple/5 rounded-xl p-6 transition-all text-center relative group min-h-[160px] flex flex-col items-center justify-center">
                                
                                <!-- Hidden Input -->
                                <input type="file" name="venue_images[]" id="imageInput" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <!-- Empty State / Add More Button -->
                                <div id="upload-placeholder" class="pointer-events-none">
                                    <div class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-purple group-hover:text-white transition-colors text-gray-500">
                                        <i data-lucide="image-plus" size="24"></i>
                                    </div>
                                    <p id="placeholder-text" class="text-sm text-gray-400 font-bold">Drag photos here or click</p>
                                    <p class="text-xs text-gray-600 mt-1">JPG, PNG, WEBP up to 5MB</p>
                                </div>

                                <!-- Preview Container -->
                                <div id="preview-container" class="hidden grid-cols-2 sm:grid-cols-4 gap-3 w-full mt-2 relative z-20">
                                    <!-- Previews injected via JS -->
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-2 text-right" id="file-counter">0/4 photos</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 border-t border-white/10">
                        <button type="submit" name="submit_venue" class="w-full py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-lg rounded-xl shadow-lg shadow-brand-accent/20 transition-all transform hover:scale-[1.01]">
                            Launch Venue
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-4">By clicking Launch, you agree to our Partner Terms & Conditions.</p>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        lucide.createIcons();

        // --- GOOGLE MAPS LOGIC ---
        let map, marker, geocoder;

        function initMap() {
            const initialPos = { lat: 5.6037, lng: -0.1870 }; // Accra
            const addressInput = document.getElementById('address_input');
            const latInput = document.getElementById('lat_input');
            const lngInput = document.getElementById('lng_input');

            // 1. Setup Map
            geocoder = new google.maps.Geocoder();
            map = new google.maps.Map(document.getElementById('venue-map'), {
                zoom: 13,
                center: initialPos,
                styles: [
                    { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                    { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                    { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                    { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] }
                ],
                disableDefaultUI: true,
                zoomControl: true
            });

            // 2. Setup Marker
            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                title: "Drag me!"
            });

            // Set initial hidden values
            latInput.value = initialPos.lat;
            lngInput.value = initialPos.lng;

            // 3. Autocomplete
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address'],
                componentRestrictions: { country: 'gh' }
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    marker.setPosition(place.geometry.location);
                    updateCoords(place.geometry.location);
                }
            });

            // 4. Drag Listener
            marker.addListener('dragend', () => {
                const pos = marker.getPosition();
                map.setCenter(pos);
                updateCoords(pos);
                // Reverse Geocode
                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        addressInput.value = results[0].formatted_address;
                    }
                });
            });

            // Helper
            function updateCoords(pos) {
                latInput.value = pos.lat();
                lngInput.value = pos.lng();
            }

            // 5. Geolocation
            window.useCurrentLocation = function() {
                if (navigator.geolocation) {
                    addressInput.placeholder = "Locating...";
                    navigator.geolocation.getCurrentPosition((position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        map.setZoom(17);
                        marker.setPosition(pos);
                        updateCoords(new google.maps.LatLng(pos.lat, pos.lng));
                        
                        geocoder.geocode({ location: pos }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                addressInput.value = results[0].formatted_address;
                            }
                        });
                    }, () => {
                        alert("Error: Location access denied.");
                    });
                }
            };
        }

        // Wait for Google API
        window.addEventListener('load', () => {
            if(typeof google !== 'undefined') initMap();
        });

        // --- IMAGE UPLOAD LOGIC (Drag & Drop + Delete + Accumulate) ---
        (function() {
            let allFiles = [];
            const maxFiles = 4;
            const fileInput = document.getElementById('imageInput');
            const dropZone = document.getElementById('drop-zone');
            const previewContainer = document.getElementById('preview-container');
            const placeholder = document.getElementById('upload-placeholder');
            const placeholderText = document.getElementById('placeholder-text');
            const counter = document.getElementById('file-counter');

            function handleFiles(newFiles) {
                const remainingSlots = maxFiles - allFiles.length;
                if (remainingSlots <= 0) {
                    alert("Maximum 4 images allowed.");
                    return;
                }
                
                // Add new files to our array
                const filesToAdd = Array.from(newFiles).slice(0, remainingSlots);
                allFiles = [...allFiles, ...filesToAdd];
                
                updateFileInput();
                renderPreviews();
            }

            // Syncs the custom array back to the HTML input for form submission
            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                allFiles.forEach(file => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;
                counter.textContent = `${allFiles.length}/${maxFiles} photos`;
            }

            function renderPreviews() {
                previewContainer.innerHTML = ''; // Clear current previews
                
                // --- VISIBILITY LOGIC ---
                if (allFiles.length > 0) {
                    // Show previews
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('grid');

                    // Render each file
                    allFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = "relative h-24 rounded-lg overflow-hidden border border-white/20 group/item bg-black/50";
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                ${index === 0 ? '<span class="absolute bottom-0 left-0 w-full bg-brand-accent/90 text-black text-[9px] font-bold text-center py-1">COVER IMAGE</span>' : ''}
                                <button type="button" onclick="removeUploadedFile(${index})" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover/item:opacity-100 transition-opacity shadow-md pointer-events-auto cursor-pointer z-30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    // No files: Hide preview container
                    previewContainer.classList.add('hidden');
                    previewContainer.classList.remove('grid');
                }

                // --- PLACEHOLDER (ADD BUTTON) LOGIC ---
                // Only hide the placeholder/button if we are FULL (4/4)
                if (allFiles.length >= maxFiles) {
                    placeholder.classList.add('hidden');
                } else {
                    placeholder.classList.remove('hidden');
                    
                    // Optional: Change text if we have some files but not full
                    if (allFiles.length > 0) {
                        placeholderText.textContent = "Click to add more";
                    } else {
                        placeholderText.textContent = "Drag photos here or click";
                    }
                }
            }

            // Expose remove function globally
            window.removeUploadedFile = function(index) {
                // Remove file from array
                allFiles.splice(index, 1);
                // Update input and UI
                updateFileInput();
                renderPreviews();
                
                // Prevent bubbling to file picker click
                event.preventDefault();
                event.stopPropagation();
            }

            // Input Change (Click Upload)
            fileInput.addEventListener('change', (e) => {
                if(e.target.files.length > 0) {
                    handleFiles(e.target.files);
                }
            });

            // Drag & Drop Events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-brand-purple', 'bg-brand-purple/5');
            });
            
            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-brand-purple', 'bg-brand-purple/5');
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-brand-purple', 'bg-brand-purple/5');
                handleFiles(e.dataTransfer.files);
            });
        })();
    </script>
</body>
</html>