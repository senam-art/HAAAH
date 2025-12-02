<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/actions/get_analytics_data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders & Bookings - Haaah</title>
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

    <nav class="border-b border-white/5 bg-brand-dark px-6 py-4 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex items-center gap-4">
            <a href="homepage.php" class="text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <h1 class="font-bold text-lg">Orders & Bookings</h1>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-8">

        <!-- SECTION 1: ORGANIZER STATS -->
        <section class="mb-12">
            <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                <i data-lucide="briefcase" class="text-brand-purple"></i> Organizer Performance
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5 relative overflow-hidden">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Events Hosted</p>
                    <h3 class="text-4xl font-black"><?php echo number_format($organizer_stats['events_hosted']); ?></h3>
                </div>
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5 relative overflow-hidden">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Attendees</p>
                    <h3 class="text-4xl font-black text-brand-accent"><?php echo number_format($organizer_stats['players_hosted']); ?></h3>
                </div>
                <div class="bg-brand-card p-6 rounded-2xl border border-white/5 relative overflow-hidden">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Revenue</p>
                    <h3 class="text-4xl font-black text-blue-400">GHS <?php echo number_format($organizer_stats['revenue_generated'], 2); ?></h3>
                </div>
            </div>
        </section>

        <!-- SECTION 2: PAYMENT HISTORY -->
        <section>
            <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                <i data-lucide="receipt" class="text-brand-accent"></i> My Bookings & Payments
            </h2>

            <div class="bg-brand-card rounded-2xl border border-white/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-white/5 text-xs uppercase font-bold text-gray-300">
                            <tr>
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Event Details</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Payment Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if (!empty($payment_history)): ?>
                                <?php foreach ($payment_history as $payment): ?>
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-mono text-white">
                                            #INV-<?php echo str_pad($payment['payment_id'], 5, '0', STR_PAD_LEFT); ?>
                                            <div class="text-[10px] text-gray-600 mt-0.5">Ref: <?php echo substr($payment['reference'], 0, 8); ?>...</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-white"><?php echo htmlspecialchars($payment['event_title']); ?></div>
                                            <div class="text-xs"><?php echo date('D, M j', strtotime($payment['event_date'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-white">
                                            <!-- Dynamic Currency Display -->
                                            <?php echo htmlspecialchars($payment['currency']); ?> 
                                            <?php echo number_format($payment['amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($payment['status'] === 'success' || $payment['status'] === 'confirmed'): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-500/10 text-green-400 text-xs font-bold border border-green-500/20">
                                                    <i data-lucide="check" size="10"></i> Paid
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-yellow-500/10 text-yellow-400 text-xs font-bold border border-yellow-500/20">
                                                    <?php echo ucfirst($payment['status']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs">
                                            <!-- Handle NULL paid_at for pending transactions -->
                                            <?php 
                                                echo !empty($payment['paid_at']) 
                                                    ? date('M j, Y h:i A', strtotime($payment['paid_at'])) 
                                                    : '<span class="text-yellow-500">Pending</span>'; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                        No bookings found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>