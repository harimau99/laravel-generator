@section('content')
    <div class="row">
        <div class="{{ !empty($col_class) ? $col_class : 'col-md-8 col-md-offset-2' }}">
            @if (session('status'))
                <div class="alert alert-{{ session('status-type') ? : 'success' }}">
                    {{ session('status') }}
                </div>
            @endif
            @component(config('generator.view_component').'components.panel')
                @slot('title')
                    {{ __('List') }} {{ !empty($panel_title) ? $panel_title : ucwords(__($resource_route.'.plural')) }}
                @endslot
                @slot('tools')
                    @if (Route::has($resource_route.'.create'))
                        @auth
                            @can('create', isset($model_class) ? $model_class : 'App\Model')
                                <a href="{{ route($resource_route.'.create', [ 'redirect' => request()->fullUrlWithQuery([ 'search' => null ]) ]) }}"
                                   class="btn btn-default btn-xs">{{ __('Create') }}</a>
                            @endcan
                        @endauth
                        @guest
                            <a href="{{ route($resource_route.'.create', [ 'redirect' => request()->fullUrlWithQuery([ 'search' => null ]) ]) }}"
                               class="btn btn-default btn-xs">{{ __('Create') }}</a>
                        @endguest
                    @endif
                @endslot

                <form class="form" method="GET">
                    <div class="row" style="margin-bottom:15px">
                        <div class="col-xs-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                                <input type="text" name="search" placeholder="{{ __('Search') }}"
                                       class="input-sm form-control" value="{{ request()->search }}" autofocus>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-8">
                            <div class="pull-right">
                                <select class="form-control input-sm" name="per_page" id="per_page" onchange="this.form.submit()" title="per page">
                                    @foreach ([ 15, 50, 100, 250 ] as $value)
                                        <option value="{{ $value }}" {{ $value == $models->perPage() ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">
                        <thead class="text-nowrap">
                        <tr>
                            @foreach ($visibles[$model_variable] as $key => $column)
                                @if (!empty($column['column']))
                                    <th class="text-center">
                                        {{ !empty($column['label']) ? $column['label'] : ucwords(str_replace('_', ' ', snake_case($column['name']))) }}
                                        @if (array_search($column['name'].'.'.(isset($column['sort']) ? $column['sort'] : $column['column']).',desc', explode('|', request()->sort)) === false)
                                            <a href="{{ route($resource_route.'.index', array_merge(request()->query(), [ 'sort' => $column['name'].'.'.(isset($column['sort']) ? $column['sort'] : $column['column']).',desc' ])) }}">
                                                <i class="fa fa-sort text-muted"></i></a>
                                        @else
                                            <a href="{{ route($resource_route.'.index', array_merge(request()->query(), [ 'sort' => $column['name'].'.'.(isset($column['sort']) ? $column['sort'] : $column['column']).',asc' ])) }}">
                                                <i class="fa fa-sort text-muted"></i></a>
                                        @endif
                                    </th>
                                @else
                                    <th class="text-center">
                                        {{ !empty($column['label']) ? $column['label'] : ucwords(str_replace('_', ' ', snake_case($column['name']))) }}
                                        @if (array_search((isset($column['sort']) ? $column['sort'] : $column['name']).',desc', explode('|', request()->sort)) === false)
                                            <a href="{{ route($resource_route.'.index', array_merge(request()->query(), [ 'sort' => (isset($column['sort']) ? $column['sort'] : $column['name']).',desc' ])) }}">
                                                <i class="fa fa-sort text-muted"></i></a>
                                        @else
                                            <a href="{{ route($resource_route.'.index', array_merge(request()->query(), [ 'sort' => (isset($column['sort']) ? $column['sort'] : $column['name']).',asc' ])) }}">
                                                <i class="fa fa-sort text-muted"></i></a>
                                        @endif
                                    </th>
                                @endif
                            @endforeach
                            <th class="text-center action" width="1px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @stack('tbody-prepend')
                        @forelse ($models as $key => $model)
                            <tr>
                                @foreach ($visibles[$model_variable] as $key => $column)
                                    @if (!empty($column['column']))
                                        <td class="{{ !empty($column['class']) ? $column['class'] : '' }}">
                                            @if ($model->{$column['name']})
                                                <a href="{{ Route::has(str_plural($column['name']).'.show') && (!auth()->check() || auth()->user()->can('view', $model->{$column['name']})) ? route(str_plural($column['name']).'.show', [ $model->{$column['name']}->getKey(), 'redirect' => request()->fullUrl() ]) : '#' }}">
                                                    @if ($model->{$column['name']}->{$column['column']} instanceof \Illuminate\Support\HtmlString)
                                                        {!! $model->{$column['name']}->{$column['column']} !!}
                                                    @else
                                                        {{ $model->{$column['name']}->{$column['column']} }}
                                                    @endif
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @else
                                        <td class="{{ !empty($column['class']) ? $column['class'] : '' }}">
                                            @if ($model->{$column['name']} instanceof \Illuminate\Support\HtmlString)
                                                {!! $model->{$column['name']} !!}
                                            @else
                                                {{ $model->{$column['name']} }}
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="action text-nowrap text-right">
                                    @if (Route::has($resource_route.'.show'))
                                        @auth
                                            @can('view', $model)
                                                <a href="{{ route($resource_route.'.show', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                                   class="btn btn-primary btn-xs">{{ __('Show') }}</a>
                                            @endcan
                                        @endauth
                                        @guest
                                            <a href="{{ route($resource_route.'.show', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                               class="btn btn-primary btn-xs">{{ __('Show') }}</a>
                                        @endguest
                                    @endif
                                    @if (Route::has($resource_route.'.edit'))
                                        @auth
                                            @can('update', $model)
                                                <a href="{{ route($resource_route.'.edit', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                                   class="btn btn-success btn-xs">{{ __('Edit') }}</a>
                                            @endcan
                                        @endauth
                                        @guest
                                            <a href="{{ route($resource_route.'.edit', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                               class="btn btn-success btn-xs">{{ __('Edit') }}</a>
                                        @endguest
                                    @endif
                                    @if (Route::has($resource_route.'.destroy'))
                                        @auth
                                            @can('delete', $model)
                                                <form style="display:inline"
                                                      action="{{ route($resource_route.'.destroy', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to :do?', [ 'do' => ucwords(__('Delete')) ]) }}');">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-danger btn-xs" name="_method"
                                                            value="DELETE">{{ __('Delete') }}</button>
                                                </form>
                                            @endcan
                                        @endauth
                                        @guest
                                            <form style="display:inline"
                                                  action="{{ route($resource_route.'.destroy', [ $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('{{ __('Are you sure you want to :do?', [ 'do' => ucwords(__('Delete')) ]) }}');">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-xs" name="_method"
                                                        value="DELETE">{{ __('Delete') }}</button>
                                            </form>
                                        @endguest
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center"
                                    colspan="{{ count($visibles[$model_variable]) + 1 }}">{{ __('Empty') }}</td>
                            </tr>
                        @endforelse
                        @stack('tbody-append')
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    {{ $models->links() }}
                </div>
            @endcomponent
        </div>
    </div>
@endsection
