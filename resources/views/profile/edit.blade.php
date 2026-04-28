@extends('layouts.crud')

@section('title', 'Mi perfil')

@section('content')
    <x-crud.page-header title="Mi perfil" subtitle="Gestiona la informacion de tu cuenta." />

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">Datos del usuario autenticado</h5>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="border rounded-3 p-3 bg-light">
                        <small class="text-muted d-block">Nombre</small>
                        <strong>{{ $user->name }}</strong>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="border rounded-3 p-3 bg-light">
                        <small class="text-muted d-block">Correo</small>
                        <strong>{{ $user->email }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">Actualizar informacion</h5>
            <form method="post" action="{{ route('profile.update') }}" class="row g-3">
                @csrf
                @method('patch')

                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-semibold">Nombre</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label fw-semibold">Correo</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-app-primary">Guardar cambios</button>
                    @if (session('status') === 'profile-updated')
                        <span class="text-success fw-semibold">Perfil actualizado.</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">Actualizar contrasena</h5>
            <form method="post" action="{{ route('password.update') }}" class="row g-3">
                @csrf
                @method('put')

                <div class="col-12 col-md-4">
                    <label for="current_password" class="form-label fw-semibold">Contrasena actual</label>
                    <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="password" class="form-label fw-semibold">Nueva contrasena</label>
                    <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirmar contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-app-primary">Guardar contrasena</button>
                    @if (session('status') === 'password-updated')
                        <span class="text-success fw-semibold">Contrasena actualizada.</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated border-danger">
        <div class="card-body">
            <h5 class="text-danger fw-bold mb-2">Eliminar cuenta</h5>
            <p class="text-muted mb-3">Esta accion es permanente. Ingresa tu contrasena para confirmar la eliminacion de tu cuenta.</p>

            <form method="post" action="{{ route('profile.destroy') }}" class="row g-3">
                @csrf
                @method('delete')

                <div class="col-12 col-md-6">
                    <label for="delete_password" class="form-label fw-semibold">Contrasena</label>
                    <input id="delete_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Contrasena actual">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-danger">Eliminar mi cuenta</button>
                </div>
            </form>
        </div>
    </div>
@endsection
