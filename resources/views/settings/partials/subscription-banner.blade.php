{{-- Bandeau abonnement du tenant (affiché dans les paramètres) --}}
<div class="mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-5 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-credit-card text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold">
                        Abonnement {{ $plan ? $plan->name : ucfirst($tenant->subscription_plan ?? '-') }}
                    </h2>
                    <p class="text-sm text-indigo-100 mt-1">
                        @if($plan)
                            {{ number_format($plan->price_monthly, 2) }} €/mois
                            · Enfants illimités
                        @else
                            Plan : {{ ucfirst($tenant->subscription_plan ?? '-') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-6 text-sm">
                @if($tenant->subscription_starts_at)
                <div class="text-center">
                    <p class="text-indigo-200 text-xs uppercase tracking-wide">Début</p>
                    <p class="font-semibold mt-1">{{ $tenant->subscription_starts_at->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($tenant->subscription_expires_at)
                <div class="text-center">
                    <p class="text-indigo-200 text-xs uppercase tracking-wide">Expiration</p>
                    <p class="font-semibold mt-1">{{ $tenant->subscription_expires_at->format('d/m/Y') }}</p>
                    @if($tenant->subscription_expires_at->isPast())
                        <span class="text-red-200 text-xs font-medium">Expiré</span>
                    @elseif($tenant->subscription_expires_at->diffInDays(now()) <= 30)
                        <span class="text-yellow-200 text-xs font-medium">{{ ceil($tenant->subscription_expires_at->diffInDays(now())) }}j restants</span>
                    @endif
                </div>
                @endif
                @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                <div class="text-center">
                    <p class="text-indigo-200 text-xs uppercase tracking-wide">Essai</p>
                    <p class="font-semibold mt-1">{{ $tenant->trial_ends_at->format('d/m/Y') }}</p>
                    <span class="text-yellow-200 text-xs font-medium">{{ ceil($tenant->trial_ends_at->diffInDays(now())) }}j restants</span>
                </div>
                @endif
            </div>
        </div>
        @if($tenant->status === 'suspended')
        <div class="mt-3 bg-red-500 bg-opacity-30 rounded-lg px-4 py-2 text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> Compte suspendu — contactez votre administrateur
        </div>
        @endif
    </div>
</div>
