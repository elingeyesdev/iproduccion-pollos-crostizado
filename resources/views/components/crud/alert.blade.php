@php
    $toasts = [];

    if (session('success')) {
        $toasts[] = [
            'type'  => 'success',
            'title' => 'Operación exitosa',
            'icon'  => 'fa-solid fa-circle-check text-success',
            'body'  => session('success'),
        ];
    }

    if (session('error')) {
        $toasts[] = [
            'type'  => 'danger',
            'title' => 'Error',
            'icon'  => 'fa-solid fa-circle-exclamation text-danger',
            'body'  => session('error'),
        ];
    }

    if (session('warning')) {
        $toasts[] = [
            'type'  => 'warning',
            'title' => 'Advertencia',
            'icon'  => 'fa-solid fa-triangle-exclamation text-warning',
            'body'  => session('warning'),
        ];
    }

    if (session('info')) {
        $toasts[] = [
            'type'  => 'info',
            'title' => 'Información',
            'icon'  => 'fa-solid fa-circle-info text-info',
            'body'  => session('info'),
        ];
    }

    if ($errors->any()) {
        $toasts[] = [
            'type'  => 'warning',
            'title' => 'Revisa los datos del formulario',
            'icon'  => 'fa-solid fa-triangle-exclamation text-warning',
            'body'  => implode(' | ', $errors->all()),
            'autohide' => false,
        ];
    }
@endphp

@if (!empty($toasts))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090; margin-top: 72px;">
        @foreach ($toasts as $index => $toast)
            @php
                $borderClass = match ($toast['type']) {
                    'success' => 'border-start border-4 border-success',
                    'danger'  => 'border-start border-4 border-danger',
                    'warning' => 'border-start border-4 border-success',
                    'info'    => 'border-start border-4 border-info',
                    default   => '',
                };
            @endphp
            <div id="crudToast{{ $index }}"
                 class="toast align-items-center shadow-lg {{ $borderClass }}"
                 role="alert" aria-live="assertive" aria-atomic="true"
                 data-bs-delay="4500"
                 @if(($toast['autohide'] ?? true) === true) data-bs-autohide="true" @else data-bs-autohide="false" @endif>
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-start gap-3">
                        <i class="{{ $toast['icon'] }} fs-5 mt-1 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">{{ $toast['title'] }}</div>
                            <div class="small text-muted">{{ $toast['body'] }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($toasts as $index => $toast)
                (function () {
                    var el = document.getElementById('crudToast{{ $index }}');
                    if (!el) return;
                    var toast = new bootstrap.Toast(el);
                    toast.show();
                })();
            @endforeach
        });
    </script>
    @endpush
@endif
