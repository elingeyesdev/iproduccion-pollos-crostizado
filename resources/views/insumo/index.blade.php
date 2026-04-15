@extends('layouts.app')

@section('template_title')
    Insumos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Insumos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('insumos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Nombre</th>
									<th >Categoria Insumo Id</th>
									<th >Unidad Medida Id</th>
									<th >Preciounitario</th>
									<th >Stockactual</th>
									<th >Stockminimo</th>
									<th >Estado</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($insumos as $insumo)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $insumo->nombre }}</td>
										<td >{{ $insumo->categoria_insumo_id }}</td>
										<td >{{ $insumo->unidad_medida_id }}</td>
										<td >{{ $insumo->precioUnitario }}</td>
										<td >{{ $insumo->stockActual }}</td>
										<td >{{ $insumo->stockMinimo }}</td>
										<td >{{ $insumo->estado }}</td>

                                            <td>
                                                <form action="{{ route('insumos.destroy', $insumo->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('insumos.show', $insumo->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('insumos.edit', $insumo->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $insumos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
