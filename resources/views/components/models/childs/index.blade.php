@section('parent-content')
    <div class="row" style="margin-bottom:15px">
        <div class="col-sm-6 col-md-4" style="margin-bottom: 10px">
            <form class="form" method="GET">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                    <input type="text" name="search" placeholder="{{ __('Search') }}" class="input-sm form-control"
                           value="{{ request()->search }}" autofocus>
                </div>
            </form>
        </div>
        <div class="col-sm-6 col-md-3 col-md-offset-5">
            @if (Route::has($resource_route.'.create'))
                @auth
                    @can('create', isset($model_class) ? $model_class : 'App\Model')
                        <a href="{{ route($resource_route.'.create', [ $parent->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                           class="btn btn-default btn-block btn-sm">{{ __('Create') }}</a>
                    @endcan
                @endauth
                @guest
                    <a href="{{ route($resource_route.'.create', [ $parent->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                       class="btn btn-default btn-block btn-sm">{{ __('Create') }}</a>
                @endguest
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table id="dummy_model_plural_variable" class="table table-striped table-hover">
            <thead class="text-nowrap">
            <tr>
                @foreach ($visibles[$model_variable] as $key => $column)
                    @if (!empty($column['column']))
                        <th class="text-center">
                            {{ !empty($column['label']) ? $column['label'] : ucwords(str_replace('_', ' ', snake_case($column['name']))) }}
                            @if (array_search($column['name'].'.'.$column['column'].',desc', explode('|', request()->sort)) === false)
                                <a href="{{ route($resource_route.'.index', array_merge([ $parent->getKey() ], request()->query(), [ 'sort' => $column['name'].'.'.$column['column'].',desc' ])) }}">
                                    <i class="fa fa-sort text-muted"></i></a>
                            @else
                                <a href="{{ route($resource_route.'.index', array_merge([ $parent->getKey() ], request()->query(), [ 'sort' => $column['name'].'.'.$column['column'].',asc' ])) }}">
                                    <i class="fa fa-sort text-muted"></i></a>
                            @endif
                        </th>
                    @else
                        <th class="text-center">
                            {{ !empty($column['label']) ? $column['label'] : ucwords(str_replace('_', ' ', snake_case($column['name']))) }}
                            @if (array_search($column['name'].',desc', explode('|', request()->sort)) === false)
                                <a href="{{ route($resource_route.'.index', array_merge([ $parent->getKey() ], request()->query(), [ 'sort' => $column['name'].',desc' ])) }}">
                                    <i class="fa fa-sort text-muted"></i></a>
                            @else
                                <a href="{{ route($resource_route.'.index', array_merge([ $parent->getKey() ], request()->query(), [ 'sort' => $column['name'].',asc' ])) }}">
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
                    <td class="action text-nowrap">
                        @if (Route::has($resource_route.'.show'))
                            @auth
                                @can('view', $model)
                                    <a href="{{ route($resource_route.'.show', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                                       class="btn btn-primary btn-xs">{{ __('Show') }}</a>
                                @endcan
                            @endauth
                            @guest
                                <a href="{{ route($resource_route.'.show', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                                   class="btn btn-primary btn-xs">{{ __('Show') }}</a>
                            @endguest
                        @endif
                        @if (Route::has($resource_route.'.edit'))
                            @auth
                                @can('update', $model)
                                    <a href="{{ route($resource_route.'.edit', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                                       class="btn btn-success btn-xs">{{ __('Edit') }}</a>
                                @endcan
                            @endauth
                            @guest
                                <a href="{{ route($resource_route.'.edit', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->filled('redirect') ? request()->redirect : null ]) }}"
                                   class="btn btn-success btn-xs">{{ __('Edit') }}</a>
                            @endguest
                        @endif
                        @if (Route::has($resource_route.'.destroy'))
                            @auth
                                @can('delete', $model)
                                    <form style="display:inline"
                                          action="{{ route($resource_route.'.destroy', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
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
                                      action="{{ route($resource_route.'.destroy', [ $parent->getKey(), $model->getKey(), 'redirect' => request()->fullUrl() ]) }}"
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
                    <td class="text-center" colspan="{{ count($visibles[$model_variable]) + 1 }}">{{ __('Empty') }}</td>
                </tr>
            @endforelse
            @stack('tbody-append')
            </tbody>
        </table>
    </div>
    <div class="text-center">
        {{ $models->links() }}
    </div>
@endsection
