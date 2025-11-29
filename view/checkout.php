<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Haaah Sports</title>
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

    <!-- Simple Header -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <h1 class="font-black text-xl tracking-tighter">HAAAH<span class="font-normal text-xs ml-1">CHECKOUT</span></h1>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <i data-lucide="lock" size="14"></i> Secure Transaction
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-8">Complete Your Booking</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT: Payment Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Payment Method Tabs -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5">
                    <h3 class="font-bold mb-6">Payment Method</h3>
                    
                    <div class="flex gap-4 mb-6">
                        <!-- MoMo Tab (Active) -->
                        <button class="flex-1 p-4 bg-brand-accent/10 border border-brand-accent rounded-xl flex flex-col items-center gap-2 text-brand-accent transition-all relative">
                            <i data-lucide="smartphone" size="24"></i>
                            <span class="font-bold text-sm">Mobile Money</span>
                            <div class="absolute top-2 right-2 w-3 h-3 bg-brand-accent rounded-full border-2 border-brand-card"></div>
                        </button>
                        <!-- Card Tab -->
                        <button class="flex-1 p-4 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 text-gray-400 hover:bg-white/10 transition-all">
                            <i data-lucide="credit-card" size="24"></i>
                            <span class="font-bold text-sm">Card</span>
                        </button>
                    </div>

                    <!-- MoMo Form -->
                    <div class="space-y-4 animate-fade-in">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Network</label>
                            <select class="w-full bg-brand-dark border border-white/10 rounded-lg p-3 text-sm focus:border-brand-accent focus:outline-none">
                                <option>MTN Mobile Money</option>
                                <option>Vodafone Cash</option>
                                <option>AirtelTigo Money</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone Number</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400 text-sm">+233</span>
                                <input type="tel" placeholder="XX XXX XXXX" class="w-full bg-brand-dark border border-white/10 rounded-lg p-3 pl-14 text-sm focus:border-brand-accent focus:outline-none">
                            </div>
                        </div>
                        <div class="p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg text-xs text-yellow-500 flex items-start gap-2">
                            <i data-lucide="alert-circle" size="14" class="mt-0.5 flex-shrink-0"></i>
                            You will receive a prompt on your phone to authorize the payment.
                        </div>
                    </div>

                </div>

                <!-- Billing Info -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5">
                    <h3 class="font-bold mb-6">Billing Contact</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" placeholder="First Name" class="bg-brand-dark border border-white/10 rounded-lg p-3 text-sm">
                        <input type="text" placeholder="Last Name" class="bg-brand-dark border border-white/10 rounded-lg p-3 text-sm">
                        <input type="email" placeholder="Email Address" class="col-span-2 bg-brand-dark border border-white/10 rounded-lg p-3 text-sm">
                    </div>
                </div>

            </div>

            <!-- RIGHT: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 sticky top-24">
                    <h3 class="font-bold mb-4">Order Summary</h3>
                    
                    <!-- Item 1 -->
                    <div class="flex gap-3 mb-4 pb-4 border-b border-white/5">
                        <div class="w-12 h-12 rounded-lg bg-white/5 flex items-center justify-center text-gray-500 flex-shrink-0">
                            <i data-lucide="trophy" size="20"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">Tuesday Night Ballers</h4>
                            <p class="text-xs text-gray-500">1 Player Slot</p>
                        </div>
                        <div class="text-sm font-bold">GHS 30.00</div>
                    </div>

                    <!-- Item 2 (Showing multiple games logic) -->
                    <div class="flex gap-3 mb-6 pb-4 border-b border-white/5">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/10 text-yellow-500 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="clock" size="20"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">Weekend League Match</h4>
                            <p class="text-xs text-gray-500">1 Player Slot (Pending)</p>
                        </div>
                        <div class="text-sm font-bold">GHS 30.00</div>
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Subtotal</span>
                            <span>GHS 60.00</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Service Fees</span>
                            <span>GHS 4.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-white pt-2 border-t border-white/10">
                            <span>Total</span>
                            <span>GHS 64.00</span>
                        </div>
                    </div>

                    <button class="w-full py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-xl transition-transform hover:scale-105 shadow-lg shadow-brand-accent/20 flex items-center justify-center gap-2">
                        Pay GHS 64.00 <i data-lucide="arrow-right" size="18"></i>
                    </button>

                    <div class="mt-4 flex justify-center gap-4 opacity-50 grayscale">
                        <!-- Payment Logos (Text placeholders) -->
                        <span class="font-black text-xs">MTN MoMo</span>
                        <span class="font-black text-xs">Vodafone</span>
                        <span class="font-black text-xs">VISA</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>