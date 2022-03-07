@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.users.create') }}"><i class="fas fa-plus"></i>
            {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
        </a>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.user.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.firstname') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.lastname') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.mobile') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.roles') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.is_team_leader') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.team') }}
                        </th>


                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr data-entry-id="{{ $user->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $user->id ?? '' }}
                        </td>

                        <td>
                            {{ $user->firstname ?? '' }}
                        </td>
                        <td>
                            {{ $user->lastname ?? '' }}
                        </td>
                        <td>
                            {{ $user->email ?? '' }}
                        </td>

                        <td>
                            {{ $user->mobile ?? '' }}
                        </td>

                        <td>

                            @foreach($user->roles as $key => $item)
                            <span class="badge badge-info">{{ $item->title }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{ App\Models\User::IS_TEAM_LEADER_SELECT[$user->is_team_leader] ?? '' }}
                        </td>
                        <td>
                            {{ $user->team->name ?? '' }}
                        </td>
                        <td>
                            @can('user_show')
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', $user->id) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan

                            @can('user_edit')
                            <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('user_delete')
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               
                                <button type="submit" class="btn btn-xs btn-danger">
                                   <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('user_delete')
            @if (auth()->user()->isAdmin())
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
            text: deleteButtonTrans,
                    url: "{{ route('admin.users.massDestroy') }}",
                    className: 'btn-danger',
                    action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id')
                    });
                    if (ids.length === 0) {
                    alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                    $.ajax({
                    headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                    }
            }
    dtButtons.push(deleteButton)
            @endif

            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
                    order: [[ 1, 'desc' ]],
                    pageLength: 100,
            });
    let table = $('.datatable-User:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
    $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
    })

</script>
@endsection