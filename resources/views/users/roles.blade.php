@extends('layouts.crud')

@section('title', 'Asignar Roles')

@section('content')
    <x-crud.page-header title="Asignar roles" :subtitle="'Gestion de roles para ' . $user->name">
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('users.roles.update', $user) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="roles" class="form-label fw-semibold">Roles</label>
                    <select id="roles" name="roles[]" class="form-select @error('roles') is-invalid @enderror @error('roles.*') is-invalid @enderror" multiple required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(in_array($role->name, old('roles', $selectedRoles), true))>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantenga presionada la tecla Ctrl para seleccionar varios roles.</small>
                    @error('roles')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('roles.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar roles</button>
                </div>
            </form>
        </div>
    </div>
@endsection
