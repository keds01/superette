@extends('layouts.auth')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-500 via-blue-400 to-purple-400 animate-fade-in">
        <div class="w-full max-w-lg px-10 py-12 bg-white/60 backdrop-blur-md rounded-2xl shadow-2xl border border-white/30 mt-8">
            <div class="flex flex-col items-center mb-8">
                <img src="/images/LOGO_ELIFRANC_PRIX.png" alt="Logo Elifranc" class="h-28 w-28 rounded-full shadow-lg border-4 border-white/80 mb-4 animate-fade-in" />
                <h2 class="text-4xl font-extrabold text-gray-800 drop-shadow text-center tracking-tight">Connexion à la superette</h2>
                <p class="text-base text-gray-600 mt-2">Bienvenue ! Veuillez vous connecter pour accéder à votre espace.</p>
            </div>

            @if ($errors->has('login'))
                <div class="mb-4 rounded-lg bg-red-100 border border-red-400 text-red-700 px-4 py-3 animate-shake">
                    <ul class="list-disc pl-5">
                        <li>{{ $errors->first('login') }}</li>
                    </ul>
                </div>
            @endif
            <form class="space-y-8 animate-fade-in" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="login" class="block text-base font-medium text-gray-800">Nom d'utilisateur ou adresse e-mail</label>
                    <div class="mt-2 relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-400 text-lg"><i class="fas fa-user"></i></span>
                        <input id="login" name="login" type="text" autocomplete="username" required class="block w-full rounded-lg border-0 py-3 pl-12 pr-3 text-gray-900 shadow focus:ring-2 focus:ring-indigo-400 bg-white/80 placeholder:text-gray-400 sm:text-base" placeholder="Nom d'utilisateur ou email" value="{{ old('login') }}" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-base font-medium text-gray-800">Mot de passe</label>
                    <div class="mt-2 relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-400 text-lg"><i class="fas fa-lock"></i></span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border-0 py-3 pl-12 pr-3 text-gray-900 shadow focus:ring-2 focus:ring-indigo-400 bg-white/80 placeholder:text-gray-400 sm:text-base" placeholder="Votre mot de passe" />
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-gradient-to-r from-indigo-600 via-blue-500 to-purple-500 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:scale-105 hover:from-indigo-700 hover:to-purple-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">Se connecter <i class="fas fa-arrow-right ml-2"></i></button>
                </div>
            </form>
        </div>
        <div class="mt-8 text-white/80 text-sm text-center animate-fade-in-slow">
            &copy; {{ date('Y') }} Elifranc Superette. Tous droits réservés.
        </div>
    </div>
@endsection

@push('scripts')
<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: none; }
}
@keyframes fade-in-slow {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes shake {
  10%, 90% { transform: translateX(-1px); }
  20%, 80% { transform: translateX(2px); }
  30%, 50%, 70% { transform: translateX(-4px); }
  40%, 60% { transform: translateX(4px); }
}
.animate-fade-in { animation: fade-in 0.8s cubic-bezier(.4,0,.2,1) both; }
.animate-fade-in-slow { animation: fade-in-slow 2s 0.5s both; }
.animate-shake { animation: shake 0.4s; }
</style>
@endpush 