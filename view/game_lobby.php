<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Lobby - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.html" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Back to Games
        </a>
        <div class="flex items-center gap-4">
            <button class="p-2 text-gray-400 hover:text-white"><i data-lucide="share-2" size="20"></i></button>
            <a href="checkout.html" class="relative p-2 text-brand-accent hover:text-white">
                <i data-lucide="shopping-cart" size="20"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: Match Info & Roster -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Hero Card -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-32 bg-brand-accent/5 blur-[80px] rounded-full"></div>
                    
                    <div class="flex justify-between items-start relative z-10 mb-6">
                        <div>
                            <span class="px-3 py-1 bg-brand-purple/20 text-brand-purple text-xs font-bold uppercase tracking-wider rounded-full mb-2 inline-block">5-a-side</span>
                            <h1 class="text-3xl font-black mb-2">Tuesday Night Ballers</h1>
                            <div class="flex items-center gap-4 text-sm text-gray-400">
                                <span class="flex items-center gap-1"><i data-lucide="calendar" size="14"></i> Tue, Nov 28</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" size="14"></i> 19:00 - 20:30</span>
                                <span class="flex items-center gap-1"><i data-lucide="map-pin" size="14"></i> McDan Astro Turf</span>
                            </div>
                        </div>
                        <div class="text-center bg-black/30 p-3 rounded-xl border border-white/10">
                            <div class="text-xs text-gray-400 uppercase font-bold">Entry Fee</div>
                            <div class="text-xl font-black text-brand-accent">GHS 30</div>
                        </div>
                    </div>

                    <!-- Threshold Meter -->
                    <div class="bg-black/20 rounded-xl p-4 border border-white/5">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-bold text-yellow-500 flex items-center gap-2">
                                <i data-lucide="clock" size="14"></i> Pending Green Light
                            </span>
                            <span class="text-gray-400">8 / 10 Players Joined</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-3 mb-2 overflow-hidden">
                            <!-- Width = 80% -->
                            <div class="bg-gradient-to-r from-yellow-600 to-yellow-400 h-full rounded-full relative" style="width: 80%">
                                <div class="absolute right-0 top-0 bottom-0 w-1 bg-white/50 animate-pulse"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">2 more players needed to confirm venue booking.</p>
                    </div>
                </div>

                <!-- The Squad (Roster) -->
                <div>
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i data-lucide="users" class="text-brand-accent"></i> Squad List
                    </h3>
                    
                    <!-- Grid of Players -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Host -->
                        <div class="bg-brand-card p-4 rounded-xl border border-brand-accent/30 relative">
                            <span class="absolute top-2 right-2 text-[10px] bg-brand-accent text-black font-bold px-1.5 py-0.5 rounded">HOST</span>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center font-bold">JM</div>
                                <div>
                                    <div class="font-bold text-sm">John M.</div>
                                    <div class="text-xs text-gray-500">Midfielder</div>
                                </div>
                            </div>
                        </div>

                        <!-- Player 2 -->
                        <div class="bg-brand-card p-4 rounded-xl border border-white/5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold">KA</div>
                                <div>
                                    <div class="font-bold text-sm">Kwame A.</div>
                                    <div class="text-xs text-gray-500">Striker</div>
                                </div>
                            </div>
                        </div>

                         <!-- Player 3 -->
                         <div class="bg-brand-card p-4 rounded-xl border border-white/5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center font-bold">SD</div>
                                <div>
                                    <div class="font-bold text-sm">Sarah D.</div>
                                    <div class="text-xs text-gray-500">Defender</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ... Repeat for players ... -->
                        <div class="bg-brand-card p-4 rounded-xl border border-white/5 opacity-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-bold">?</div>
                                <div class="text-sm font-bold text-gray-400">Filled</div>
                            </div>
                        </div>

                        <!-- Empty Slot (CTA) -->
                        <button class="bg-brand-dark p-4 rounded-xl border-2 border-dashed border-white/10 hover:border-brand-accent/50 hover:bg-white/5 transition-all group flex flex-col items-center justify-center gap-2 h-full">
                            <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-accent group-hover:text-black transition-colors">
                                <i data-lucide="plus" size="16"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-400 group-hover:text-white">Open Slot</span>
                        </button>
                         <!-- Empty Slot -->
                         <button class="bg-brand-dark p-4 rounded-xl border-2 border-dashed border-white/10 hover:border-brand-accent/50 hover:bg-white/5 transition-all group flex flex-col items-center justify-center gap-2 h-full">
                            <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-accent group-hover:text-black transition-colors">
                                <i data-lucide="plus" size="16"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-400 group-hover:text-white">Open Slot</span>
                        </button>
                    </div>
                </div>

                <!-- Venue Map Placeholder -->
                <div class="rounded-2xl overflow-hidden border border-white/5 h-48 relative bg-[#2a2a35] flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="map" class="mx-auto mb-2 text-gray-500"></i>
                        <span class="text-sm text-gray-400">Map View (La Town Park)</span>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Chat & Actions -->
            <div class="space-y-6">
                
                <!-- Action Card -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 shadow-xl">
                    <h3 class="font-bold text-lg mb-4">Join this Game</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Spot Price</span>
                            <span>GHS 30.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Booking Fee</span>
                            <span>GHS 2.00</span>
                        </div>
                        <div class="h-px bg-white/10"></div>
                        <div class="flex justify-between font-bold text-brand-accent">
                            <span>Total</span>
                            <span>GHS 32.00</span>
                        </div>
                    </div>

                    <a href="checkout.html" class="block w-full py-3 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-center rounded-xl transition-transform hover:scale-105 mb-3">
                        Join Squad (GHS 32)
                    </a>
                    <button class="block w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-center rounded-xl transition-colors">
                        Add to Cart
                    </button>
                    <p class="text-[10px] text-center text-gray-500 mt-3">
                        <i data-lucide="shield-check" size="10" class="inline"></i> Refunded automatically if game is cancelled.
                    </p>
                </div>

                <!-- Live Chat -->
                <div class="bg-brand-card rounded-2xl border border-white/5 h-[400px] flex flex-col">
                    <div class="p-4 border-b border-white/5 font-bold flex items-center justify-between">
                        <span>Lobby Chat</span>
                        <span class="text-xs text-green-500 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> 5 Online</span>
                    </div>
                    
                    <!-- Chat Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Message 1 -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-600 flex-shrink-0 text-xs flex items-center justify-center font-bold">JM</div>
                            <div>
                                <div class="bg-white/5 p-2 rounded-r-xl rounded-bl-xl text-sm">
                                    <span class="text-xs font-bold text-purple-400 block mb-0.5">John M.</span>
                                    Is everyone bringing turf shoes?
                                </div>
                                <span class="text-[10px] text-gray-600 ml-1">10:42 AM</span>
                            </div>
                        </div>

                        <!-- Message 2 -->
                        <div class="flex items-start gap-3 flex-row-reverse">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 text-xs flex items-center justify-center font-bold">Me</div>
                            <div class="text-right">
                                <div class="bg-brand-accent/20 text-brand-accent p-2 rounded-l-xl rounded-br-xl text-sm text-left">
                                    Yeah the pitch is slippery today.
                                </div>
                            </div>
                        </div>

                        <!-- System Message -->
                        <div class="text-center my-2">
                            <span class="bg-white/5 text-gray-500 text-[10px] px-2 py-1 rounded-full">Kwame A. joined the lobby</span>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-3 border-t border-white/5 bg-black/20">
                        <div class="flex gap-2">
                            <input type="text" placeholder="Type a message..." class="flex-1 bg-brand-dark border border-white/10 rounded-lg px-3 py-2 text-sm focus:border-brand-accent focus:outline-none">
                            <button class="p-2 bg-white/5 hover:bg-brand-accent hover:text-black rounded-lg transition-colors">
                                <i data-lucide="send" size="16"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>