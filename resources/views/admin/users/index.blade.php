@extends('layouts.app')

@section('content')
{{-- Conteneur principal --}}
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">

    {{-- EN-TÊTE ET BOUTON AJOUTER --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-5xl tracking-tight font-serif font-extrabold text-[#F16522] uppercase">
             Gestion des Comptes
        </h1>
        
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center bg-[#82D2F5] text-[#346693] font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-[#F16522] hover:text-white transition duration-300 transform hover:scale-105">
            <span class="text-xl mr-2"></span> Ajouter un utilisateur
        </a>
    </div>

    {{-- MESSAGE DE SUCCÈS --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 shadow-md" role="alert">
            <p class="font-sans font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- BARRE DE RECHERCHE ET FILTRES --}}
    <div class="bg-white p-6 shadow-xl rounded-2xl mb-8 border-t-4 border-[#346693]">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            
            {{-- Champ de Recherche --}}
            <div class="relative flex-grow w-full sm:w-auto">
                <input type="text" id="searchInput" placeholder="🔍 Rechercher par Nom, Prénom, Email ou Téléphone..."
                    class="w-full rounded-full pl-5 pr-4 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base placeholder-[#346693]/70 font-sans">
            </div>

            {{-- Filtre de Rôle --}}
            <div class="w-full sm:w-52">
                <select name="roleFilter" id="roleFilter" 
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base appearance-none bg-white text-[#346693] font-sans font-semibold">
                    <option value="" selected>-- Tous les Rôles --</option>
                    <option value="admin">👑 Admin</option>
                    <option value="chauffeur">🚌 Chauffeur</option>
                    <option value="eleve">🎓 Élève</option>
                </select>
            </div>
            
        </div>
    </div>

    {{-- TABLEAU DES UTILISATEURS --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                {{-- En-tête de Tableau --}}
                <thead>
                    <tr class="bg-[#346693] text-white uppercase text-sm font-semibold tracking-wider">
                        <th class="py-4 px-6 text-left">Nom</th>
                        <th class="py-4 px-6 text-left">Prénom</th>
                        <th class="py-4 px-6 text-left">Email</th>
                        <th class="py-4 px-6 text-left">Téléphone</th>
                        <th class="py-4 px-6 text-center">Rôle</th>
                        <th class="py-4 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                {{-- Corps du Tableau --}}
                <tbody id="userTableBody" class="text-[#346693] text-base font-sans divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="py-4 px-6 font-semibold">{{ $user->nom }}</td>
                            <td class="py-4 px-6">{{ $user->prenom }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $user->email }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $user->telephone ?? '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-[#F16522]/20 text-[#F16522] uppercase">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200 mr-4">
                                    ✏️ Modifier
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer {{ $user->prenom }} {{ $user->nom }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-gray-400 font-bold hover:text-[#F16522] transition-colors duration-200 focus:outline-none">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 px-6 text-center italic text-[#346693]/70">
                                ✨ Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
           <div class="hide-pagination-info">
   {{ $users->links('pagination::tailwind') }}
</div>
<style>
.pagination-wrapper .text-sm.text-gray-700 {
    display: none;
}
</style>
        </div>
    </div>
</div>

{{-- SCRIPT AJAX pour la recherche --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const tableBody = document.getElementById('userTableBody');
    const pagination = document.querySelector('.pagination');
    const searchUrl = "{{ route('admin.users.search') }}"; 
    const csrfToken = '{{ csrf_token() }}';
    let debounceTimer;

    const debounce = (callback, delay) => {
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                callback.apply(this, args);
            }, delay);
        };
    };

    const performSearch = () => {
        const query = searchInput.value.trim();
        const role = roleFilter.value;
        if (pagination) {
    if (query || role) {
        pagination.style.display = 'none';
    } else {
        pagination.style.display = 'flex';
    }
}
        
        // La requête envoie maintenant `query` et `role_filter`
        fetch(`${searchUrl}?query=${encodeURIComponent(query)}&role_filter=${encodeURIComponent(role)}`)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-10 px-6 text-center italic text-[#346693]/70">
                                ✨ Aucun utilisateur trouvé pour cette recherche.
                            </td>
                        </tr>`;
                    return;
                }

                data.forEach(user => {
                    const userRole = user.role.charAt(0).toUpperCase() + user.role.slice(1);
                    const deleteConfirmText = "Êtes-vous sûr de vouloir supprimer " + user.prenom + " " + user.nom + " ?";
                    
                    // Utilisation des chemins corrects pour les URLs d'action
                    const editUrl = `/admin/users/${user.id}/edit`;
                    const deleteUrl = `/admin/users/${user.id}`;
                    
                    tableBody.innerHTML += `
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="py-4 px-6 font-semibold">${user.nom}</td>
                            <td class="py-4 px-6">${user.prenom}</td>
                            <td class="py-4 px-6 text-gray-600">${user.email}</td>
                            <td class="py-4 px-6 text-gray-600">${user.telephone ?? '-'}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-[#F16522]/20 text-[#F16522] uppercase">
                                    ${userRole}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <a href="${editUrl}" 
                                   class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200 mr-4">
                                    ✏️ Modifier
                                </a>
                                <form action="${deleteUrl}" method="POST" class="inline-block" onsubmit="return confirm('${deleteConfirmText}')">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" 
                                            class="text-gray-400 font-bold hover:text-[#F16522] transition-colors duration-200 focus:outline-none">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => console.error('Erreur AJAX:', err));
    };

    // Attache les écouteurs d'événements pour le champ de recherche et le filtre
    searchInput.addEventListener('input', debounce(performSearch, 300));
    roleFilter.addEventListener('change', performSearch);
});
</script>
@endsection