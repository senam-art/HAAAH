<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

// 1. Check Login & ID
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: manage_venues.php");
    exit();
}

$venue_id = intval($_GET['id']);
$venue = get_venue_details_ctr($venue_id);

// 2. Verify Ownership (Crucial Security Check)
if (!$venue || $venue['owner_id'] != $_SESSION['user_id']) {
    die("Access Denied: You do not own this venue.");
}

// 3. Prepare Data
$amenities = is_array($venue['amenities']) ? $venue['amenities'] : [];
$images = is_array($venue['image_urls']) ? $venue['image_urls'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Venue - <?php echo htmlspecialchars($venue['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20 bg-brand-dark">

    <nav class="border-b border-white/5 bg-brand-card/80 backdrop-blur-md px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="text-brand-purple">EDIT:</span> <?php echo htmlspecialchars($venue['name']); ?>
        </h1>
        <a href="manage_venues.php" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Cancel</a>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <form action="../actions/update_venue_action.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="venue_id" value="<?php echo $venue_id; ?>">
            
            <!-- Basic Info -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center gap-2">
                    <i data-lucide="info" size="18" class="text-brand-purple"></i> General Details
                </h3>
                <div class="grid gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Venue Name</label>
                        <input type="text" name="venue_name" value="<?php echo htmlspecialchars($venue['name']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rate (GHS/hr)</label>
                            <input type="number" name="cost_per_hour" value="<?php echo $venue['cost_per_hour']; ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Capacity</label>
                            <input type="number" name="capacity" value="<?php echo $venue['capacity']; ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none"><?php echo htmlspecialchars($venue['description']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center gap-2">
                    <i data-lucide="map-pin" size="18" class="text-brand-purple"></i> Location
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address</label>
                        <input type="text" name="venue_address" value="<?php echo htmlspecialchars($venue['address']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Latitude</label>
                            <input type="text" name="lat" value="<?php echo $venue['latitude']; ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm text-gray-400 focus:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Longitude</label>
                            <input type="text" name="lng" value="<?php echo $venue['longitude']; ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm text-gray-400 focus:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center gap-2">
                    <i data-lucide="layers" size="18" class="text-brand-purple"></i> Amenities
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <?php 
                    $options = ['Floodlights', 'Changing Rooms', 'Parking', 'Restrooms'];
                    foreach($options as $opt): 
                        $checked = in_array($opt, $amenities) ? 'checked' : '';
                    ?>
                    <label class="cursor-pointer select-none">
                        <input type="checkbox" name="amenities[]" value="<?php echo $opt; ?>" <?php echo $checked; ?> class="peer hidden">
                        <div class="px-3 py-2.5 rounded-xl bg-brand-dark border border-white/10 text-gray-400 text-sm peer-checked:bg-brand-purple peer-checked:text-white peer-checked:font-bold text-center transition-all">
                            <?php echo $opt; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <!-- Custom Amenities -->
                <?php 
                    $custom = array_diff($amenities, $options); 
                    $custom_str = implode(', ', $custom);
                ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Other Amenities</label>
                    <input type="text" name="custom_amenities" value="<?php echo htmlspecialchars($custom_str); ?>" placeholder="e.g. Wi-Fi, Shop" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm text-white focus:border-brand-purple focus:outline-none">
                </div>
            </div>

            <!-- Images -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center gap-2">
                    <i data-lucide="image" size="18" class="text-brand-purple"></i> Gallery
                </h3>
                
                <!-- Existing Images -->
                <?php if(!empty($images)): ?>
                    <p class="text-xs text-gray-500 mb-3">Current Photos (Uncheck to delete):</p>
                    <div class="grid grid-cols-4 gap-3 mb-6">
                        <?php foreach($images as $img): ?>
                            <label class="relative group cursor-pointer block">
                                <input type="checkbox" name="existing_images[]" value="<?php echo htmlspecialchars($img); ?>" checked class="peer hidden">
                                
                                <div class="relative rounded-xl overflow-hidden border-2 border-transparent peer-checked:border-brand-accent transition-all h-24">
                                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover peer-checked:opacity-100 opacity-40 grayscale peer-checked:grayscale-0 transition-all">
                                    
                                    <!-- Checked State (Keep) -->
                                    <div class="absolute top-1 right-1 bg-brand-accent text-black rounded-full p-0.5 hidden peer-checked:block shadow-md">
                                        <i data-lucide="check" size="12"></i>
                                    </div>
                                    
                                    <!-- Unchecked State (Delete) -->
                                    <div class="absolute inset-0 bg-red-500/20 flex items-center justify-center hidden peer-[:not(:checked)]:flex">
                                        <i data-lucide="trash-2" class="text-red-500"></i>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Add New (Drag & Drop) -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Add New Photos</label>
                    <div id="drop-zone" class="border-2 border-dashed border-white/10 hover:border-brand-purple hover:bg-brand-purple/5 rounded-xl p-6 transition-all text-center relative group min-h-[120px] flex flex-col items-center justify-center">
                        <input type="file" name="venue_images[]" id="imageInput" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <!-- Placeholder -->
                        <div id="upload-placeholder" class="pointer-events-none">
                            <div class="flex flex-col items-center justify-center gap-2 text-gray-400 group-hover:text-brand-purple transition-colors">
                                <i data-lucide="upload-cloud" size="24"></i>
                                <span class="text-sm font-bold">Drag & Drop or Click to Upload</span>
                            </div>
                        </div>

                        <!-- Preview Grid for NEW images -->
                        <div id="preview-container" class="hidden grid-cols-2 sm:grid-cols-4 gap-3 w-full mt-2 relative z-20"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2 text-right" id="file-counter">0 photos selected</p>
                </div>
            </div>

            <!-- Contact -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center gap-2">
                    <i data-lucide="phone" size="18" class="text-brand-purple"></i> Contact Info
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($venue['phone']); ?>" placeholder="Phone" class="bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($venue['email']); ?>" placeholder="Email" class="bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-purple focus:outline-none">
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" name="update_venue" class="flex-1 py-4 bg-brand-purple hover:bg-purple-600 text-white font-bold rounded-xl shadow-lg shadow-brand-purple/20 transition-transform hover:scale-[1.01]">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    
    <script>
        lucide.createIcons();

        // --- IMAGE PREVIEW LOGIC ---
        (function() {
            let allFiles = [];
            const maxFiles = 4; // Max new files
            const fileInput = document.getElementById('imageInput');
            const dropZone = document.getElementById('drop-zone');
            const previewContainer = document.getElementById('preview-container');
            const placeholder = document.getElementById('upload-placeholder');
            const counter = document.getElementById('file-counter');

            function handleFiles(newFiles) {
                const remainingSlots = maxFiles - allFiles.length;
                if (remainingSlots <= 0) {
                    alert("Maximum 4 new images allowed.");
                    return;
                }
                const filesToAdd = Array.from(newFiles).slice(0, remainingSlots);
                allFiles = [...allFiles, ...filesToAdd];
                updateUI();
            }

            function updateUI() {
                // Sync to input (for form submission)
                const dataTransfer = new DataTransfer();
                allFiles.forEach(file => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;
                counter.textContent = `${allFiles.length} new photos selected`;

                // Render Previews
                previewContainer.innerHTML = '';
                if (allFiles.length > 0) {
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('grid');

                    allFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = "relative h-20 rounded-lg overflow-hidden border border-white/20 group/item bg-black/50";
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <button type="button" onclick="removeNewFile(${index})" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-md cursor-pointer z-30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    placeholder.classList.remove('hidden');
                    previewContainer.classList.add('hidden');
                    previewContainer.classList.remove('grid');
                }
            }

            window.removeNewFile = function(index) {
                allFiles.splice(index, 1);
                updateUI();
                event.preventDefault(); // Stop click from opening file dialog
                event.stopPropagation();
            }

            fileInput.addEventListener('change', (e) => {
                if(e.target.files.length > 0) handleFiles(e.target.files);
            });

            // Drag & Drop
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