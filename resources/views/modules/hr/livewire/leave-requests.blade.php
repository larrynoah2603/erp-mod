<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Mes demandes de congés</h1>
        <div class="flex space-x-2">
            <button wire:click="$set('view', 'calendar')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Calendrier
            </button>
            <button wire:click="$set('view', 'requests')" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                Mes demandes
            </button>
            @if(!auth()->user()->isAdmin())
            <button wire:click="$set('showForm', true)" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                + Nouvelle demande
            </button>
            @endif
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex space-x-4">
            <select wire:model="status" class="rounded-md border-gray-300 shadow-sm">
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvés</option>
                <option value="rejected">Rejetés</option>
                <option value="cancelled">Annulés</option>
            </select>
            
            <input type="month" wire:model="month" class="rounded-md border-gray-300 shadow-sm">
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $request)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->type === 'annual')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Congés annuels</span>
                        @elseif($request->type === 'sick')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Maladie</span>
                        @elseif($request->type === 'personal')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Personnel</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ ucfirst($request->type) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        du {{ $request->start_date->format('d/m/Y') }}<br>
                        au {{ $request->end_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">
                        {{ $request->days_count }} jours
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">En attente</span>
                        @elseif($request->status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approuvé</span>
                        @elseif($request->status === 'rejected')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rejeté</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Annulé</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900 truncate max-w-xs">{{ $request->reason }}</p>
                        @if($request->rejection_reason)
                            <p class="text-xs text-red-600">Motif rejet: {{ $request->rejection_reason }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($request->status === 'pending' && $request->user_id === auth()->id())
                            <button wire:click="cancel({{ $request->id }})" 
                                    onclick="return confirm('Annuler cette demande ?')"
                                    class="text-red-600 hover:text-red-900">
                                Annuler
                            </button>
                        @endif
                        
                        @if(auth()->user()->isAdmin() && $request->status === 'pending')
                            <div class="flex space-x-2">
                                <button wire:click="approve({{ $request->id }})" 
                                        class="text-green-600 hover:text-green-900">
                                    Approuver
                                </button>
                                <button wire:click="reject({{ $request->id }}, prompt('Motif du rejet'))" 
                                        class="text-red-600 hover:text-red-900">
                                    Rejeter
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucune demande trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>
</div>