@extends('layouts.crud')

@section('title', 'Editar Merma')

@section('content')
    <x-crud.page-header title="Editar merma" subtitle="Modifica los datos del registro de merma.">
        <x-slot:actions>
            <a href="{{ route('mermas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('mermas.update', $merma) }}" novalidate>
                @csrf
                @method('PUT')
                @include('merma.form', ['submitLabel' => 'Actualizar merma'])
            </form>
        </div>
    </div>
@endsection
