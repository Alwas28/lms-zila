<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="pageTitle">
        @auth
            Selamat Datang, {{ auth()->user()->name }}
        @endauth
    </x-slot>

    @auth
        @if(auth()->user()->role === 'admin')
            @include('dashboard.admin')
        @elseif(auth()->user()->role === 'dosen')
            @include('dashboard.dosen')
        @else
            @include('dashboard.mahasiswa')
        @endif
    @endauth
</x-app-layout>
