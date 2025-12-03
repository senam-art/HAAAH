<?php
// view/create_event.php

// 1. Bootstrap & Security
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// 2. Load Logic
require_once PROJECT_ROOT . '/actions/get_profile_data.php';

// 3. Determine Profile Picture
$profile_pic_path = ".." . $profile_tags['profile_image'] ?? null;


check_login();

// Fetch user for avatar and EMAIL (Required for Paystack)
$user_id = get_user_id();
$userController = new UserController();
$current_user = $userController->get_user_by_id_ctr($user_id);
$initials = strtoupper(substr($current_user['user_name'], 0, 1));
$user_email = $current_user['email']; // Store email for JS
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haaah Sports</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Paystack Inline JS -->
    <script src="https://js.paystack.co/v1/inline.js"></script> 

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
        
        .input-field {
            @apply w-full bg-brand-dark border border-white/10 rounded-xl p-4 text-white placeholder-gray-600 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent focus:outline-none transition-all duration-200;
        }
        .label-text {
            @apply block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; } 
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        input[type="date"], input[type="time"] { color-scheme: dark; }
        ::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; opacity: 0.6; }
        ::-webkit-calendar-picker-indicator:hover { opacity: 1; }
    </style>
    
    <!-- Unified JS File -->
    <script src="../js/create-event.js"></script>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-32">

    <!-- Store User Email for JS to access -->
    <input type="hidden" id="user_email_hidden" value="<?php echo htmlspecialchars($user_email); ?>">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-dark/80 px-6 py-4 sticky top-0 z-50 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="homepage.php" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors group">
                <div class="p-2 rounded-full bg-white/5 group-hover:bg-white/10 transition-colors"><i data-lucide="arrow-left" size="18"></i></div>
                <span class="text-sm font-bold">Cancel</span>
            </a>
            <h1 class="font-black text-lg tracking-tight">Create Match</h1>
            <!-- Profile Picture Section -->
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-accent to-brand-purple p-[2px]">
                <div class="w-full h-full rounded-full bg-brand-dark flex items-center justify-center font-bold text-xs text-white overflow-hidden">
                    <?php if ($profile_pic_path): ?>
                    <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?php echo $initials; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-10">
        
        <form id="eventForm" action="../actions/create_event_action.php" method="POST">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- LEFT COLUMN: Venue & Details (Span 8) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- STEP 1: Venue Picker -->
                    <div class="bg-brand-card p-1 rounded-3xl border border-white/5 shadow-2xl">
                        <div class="bg-brand-card rounded-[22px] p-6 sm:p-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-10 h-10 rounded-full bg-brand-accent text-black flex items-center justify-center font-black text-lg shadow-lg shadow-brand-accent/20">1</div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Select Venue</h3>
                                    <p class="text-sm text-gray-400">Where is the action happening?</p>
                                </div>
                            </div>

                            <div id="venue-map" class="w-full h-72 rounded-2xl bg-gray-800 mb-6 border border-white/10 grayscale hover:grayscale-0 transition-all duration-500"></div>
                            
                            <div id="venue-card-container" class="bg-black/20 p-1 rounded-2xl border border-white/5 min-h-[200px] flex items-center justify-center">
                                <span class="text-gray-500 text-sm flex items-center gap-2">
                                    <i data-lucide="loader-2" class="animate-spin"></i> Loading nearby venues...
                                </span>
                            </div>

                            <input type="hidden" name="selected_venue_id" id="selected_venue_id" required>
                            <input type="hidden" name="selected_venue_name" id="selected_venue_name">
                            <input type="hidden" id="selected_venue_cost"> 
                        </div>
                    </div>

                    <!-- STEP 2: Basic Details -->
                    <div class="bg-brand-card p-1 rounded-3xl border border-white/5 shadow-2xl">
                        <div class="bg-brand-card rounded-[22px] p-6 sm:p-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-10 h-10 rounded-full bg-brand-purple text-white flex items-center justify-center font-black text-lg shadow-lg shadow-brand-purple/20">2</div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Match Details</h3>
                                    <p class="text-sm text-gray-400">Set the time and format.</p>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="label-text">Event Title</label>
                                    <div class="relative">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                            <i data-lucide="type" size="20"></i>
                                        </div>
                                        <input type="text" name="title" placeholder="e.g. Friday Night Lights ⚡️" class="w-full bg-brand-dark border border-white/10 rounded-xl p-4 pl-12 text-white placeholder-gray-600 focus:border-brand-purple focus:ring-1 focus:ring-brand-purple focus:outline-none transition-all font-bold text-lg" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="label-text">Format</label>
                                    <div class="relative">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                            <i data-lucide="users-round" size="20"></i>
                                        </div>
                                        <select name="format" class="w-full bg-brand-dark border border-white/10 rounded-xl p-4 pl-12 text-white appearance-none focus:border-brand-purple focus:outline-none cursor-pointer">
                                            <option value="5-a-side">5-a-side</option>
                                            <option value="7-a-side">7-a-side</option>
                                            <option value="11-a-side">11-a-side</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="absolute right-4 top-4.5 text-gray-500 pointer-events-none" size="16"></i>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                    <div>
                                        <label class="label-text">Date</label>
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                                <i data-lucide="calendar" size="20"></i>
                                            </div>
                                            <input type="date" id="event_date" name="event_date" min="<?php echo date('Y-m-d'); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-4 pl-12 text-white focus:border-brand-purple focus:outline-none cursor-pointer" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="label-text">Start Time</label>
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                                <i data-lucide="clock" size="20"></i>
                                            </div>
                                            <select id="event_time" name="event_time" class="w-full bg-brand-dark border border-white/10 rounded-xl p-4 pl-12 text-white appearance-none focus:border-brand-purple focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer" required disabled>
                                                <option value="">Select Venue First</option>
                                            </select>
                                            <i data-lucide="chevron-down" class="absolute right-4 top-4.5 text-gray-500 pointer-events-none" size="16"></i>
                                        </div>
                                        <div id="time-loading" class="hidden text-[10px] text-brand-accent mt-2 flex items-center gap-1"><i data-lucide="loader" size="10" class="animate-spin"></i> Checking availability...</div>
                                    </div>
                                    <div>
                                        <label class="label-text">Duration</label>
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                                <i data-lucide="hourglass" size="20"></i>
                                            </div>
                                            <select id="duration" name="duration" class="w-full bg-brand-dark border border-white/10 rounded-xl p-4 pl-12 text-white appearance-none focus:border-brand-purple focus:outline-none cursor-pointer">
                                                <option value="1">1 Hour</option>
                                                <option value="2">2 Hours</option>
                                                <option value="3">3 Hours</option>
                                            </select>
                                            <i data-lucide="chevron-down" class="absolute right-4 top-4.5 text-gray-500 pointer-events-none" size="16"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Calculator (Span 4) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- STEP 3: Financials -->
                    <div class="bg-brand-card rounded-3xl border border-white/5 shadow-2xl sticky top-28 overflow-hidden">
                        
                        <div class="p-6 border-b border-white/5 bg-white/[0.02]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">3</div>
                                <h3 class="text-lg font-bold text-white">Financials</h3>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            
                            <div id="selected-venue-display" class="hidden p-4 bg-brand-accent/5 border border-brand-accent/20 rounded-xl animate-fade-in"></div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="label-text text-[10px]">Fee / Player</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">GHS</span>
                                        <input type="number" id="cost_per_player" name="cost_per_player" min="0" step="0.50" value="30.00" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-14 text-white font-mono font-bold focus:border-brand-accent focus:outline-none" required>
                                    </div>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="label-text text-[10px]">Min Players</label>
                                    <div class="relative">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                            <i data-lucide="users" size="16"></i>
                                        </div>
                                        <input type="number" id="min_players" name="min_players" min="2" max="50" value="10" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-12 text-white font-mono font-bold focus:border-brand-accent focus:outline-none" required>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-black/20 rounded-xl p-4 space-y-3 border border-white/5">
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Venue Cost (<span id="calc_duration_label">1h</span>)</span>
                                    <span id="display_venue_cost" class="font-mono text-white">--</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Platform Fee (10%)</span>
                                    <span id="display_platform_fee" class="font-mono text-white">--</span>
                                </div>
                                <div class="h-px bg-white/10 my-1"></div>
                                <div class="flex justify-between text-sm font-bold text-white">
                                    <span>Total Needed</span>
                                    <span id="display_total_needed" class="font-mono">--</span>
                                </div>
                            </div>

                            <div id="commission_display" class="text-xs text-center py-2 text-gray-500">
                                Select a venue to see profit potential
                            </div>

                            <div class="bg-gradient-to-br from-brand-purple/20 to-brand-card border border-brand-purple/30 rounded-xl p-5 text-center relative overflow-hidden">
                                <div class="relative z-10">
                                    <p class="text-brand-purple font-bold text-xs uppercase tracking-widest mb-1">Commitment Fee</p>
                                    <h2 id="display_commitment_fee" class="text-3xl font-black text-white mb-2">--</h2>
                                    <p class="text-[10px] text-gray-400">Pay 20% now to reserve. 100% Refundable.</p>
                                </div>
                                <div class="absolute -right-4 -top-4 w-20 h-20 bg-brand-purple/20 blur-2xl rounded-full"></div>
                            </div>
                            
                            <input type="hidden" name="hidden_commitment_fee" id="hidden_commitment_fee" value="0">

                            <button type="submit" id="submitBtn" class="w-full py-4 bg-gray-700 text-gray-400 font-bold rounded-xl cursor-not-allowed transition-all flex items-center justify-center gap-2" disabled>
                                Select Venue First
                            </button>

                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- =======================
         PAYMENT CONFIRMATION MODAL 
         ======================= -->
    <div id="payment-modal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
        
        <!-- Modal Content -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm transition-all scale-95 opacity-0" id="modal-content">
            <div class="bg-brand-card border border-white/10 rounded-2xl shadow-2xl p-6 relative overflow-hidden">
                
                <!-- Glow Effect -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-accent to-brand-purple"></div>

                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-brand-purple/20 text-brand-purple rounded-full flex items-center justify-center mx-auto mb-4 border border-brand-purple/30">
                        <i data-lucide="credit-card" size="24"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-1">Confirm Payment</h3>
                    <p class="text-sm text-gray-400">Secure commitment fee to publish.</p>
                </div>

                <div class="bg-black/40 rounded-xl p-4 mb-6 border border-white/5 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Event</span>
                        <span class="text-white font-bold text-right truncate w-32" id="modal_event_title">--</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Due</span>
                        <span class="text-brand-accent font-black text-lg" id="modal_amount_display">--</span>
                    </div>
                </div>

                <button type="button" id="paystack-btn" class="w-full py-3.5 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-all flex items-center justify-center gap-2 shadow-lg shadow-brand-accent/20">
                    Pay Now <i data-lucide="arrow-right" size="18"></i>
                </button>
                
                <button type="button" onclick="closePaymentModal()" class="w-full mt-3 py-3 text-gray-500 font-bold hover:text-white transition-colors">
                    Cancel
                </button>

            </div>
        </div>
    </div>

</body>
</html>