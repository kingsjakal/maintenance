{{-- Users Grid --}}
<section class="panel panel-default panel-grid">

    {{-- Grid: Header --}}
    <header class="panel-heading">

        <nav class="navbar navbar-default navbar-actions">

            <div class="container-fluid">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#users-actions">
                        <span class="fa fa-bars"></span>
                    </button>
                </div>

                {{-- Grid: Actions --}}
                <div class="collapse navbar-collapse" id="users-actions">

                    <ul class="nav navbar-nav navbar-left">

                        <li class="dropdown">
                            <a href="#" data-grid-exporter class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="Export">
                                <i class="fa fa-download"></i> <span class="visible-xs-inline">Export</span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#" data-download="pdf" data-toggle="tooltip" data-original-title="Export Results as PDF"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                <li><a href="#" data-download="csv" data-toggle="tooltip" data-original-title="Export Results as CSV"><i class="fa fa-file-excel-o"></i> CSV</a></li>
                                <li><a href="#" data-download="json" data-toggle="tooltip" data-original-title="Export Results as JSON"><i class="fa fa-file-code-o"></i> JSON</a></li>
                            </ul>
                        </li>

                        <li class="primary">
                            <a href="{{ route('maintenance.admin.users.create') }}" data-toggle="tooltip" data-original-title="Create">
                                <i class="fa fa-plus"></i> <span class="visible-xs-inline">Create</span>
                            </a>
                        </li>

                    </ul>

                    {{-- Grid: Filters --}}
                    <form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-search data-grid="users" role="form">

                        <div class="input-group">

                            <span class="input-group-btn">

                                <button class="btn btn-default" type="button" disabled>
                                    Filters
                                </button>

                                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <ul class="dropdown-menu" role="menu">

                                    <li>
                                        <a data-grid-calendar-preset="day">
                                            <i class="fa fa-calendar"></i> Today
                                        </a>
                                    </li>

                                    <li>
                                        <a data-grid-calendar-preset="week">
                                            <i class="fa fa-calendar"></i> This Week
                                        </a>
                                    </li>

                                    <li>
                                        <a data-grid-calendar-preset="month">
                                            <i class="fa fa-calendar"></i> This Month
                                        </a>
                                    </li>

                                </ul>

                                <button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="created_at">
                                    <i class="fa fa-calendar"></i>
                                </button>

                            </span>

                            <input class="form-control" name="filter" type="text" placeholder="Search">

                            <span class="input-group-btn">

                                <button data-toggle="tooltip" data-original-title="Search" class="btn btn-default" type="submit">
                                    <span class="fa fa-search"></span>
                                </button>

                                <button data-toggle="tooltip" data-original-title="Refresh" class="btn btn-default" data-grid="users" data-reset>
                                    <i class="fa fa-refresh fa-sm"></i>
                                </button>

                            </span>

                        </div>

                    </form>

                </div>

            </div>

        </nav>

    </header>

    {{-- Page header --}}
    <div class="panel-body">

        {{-- Grid: Applied Filters --}}
        <div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

            <div id="users-filters" class="btn-group" data-grid="users"></div>

        </div>

    </div>

    {{-- Grid: Table --}}
    <div class="table-responsive">

        <table id="users-results" class="table table-hover" data-source="{{ route('maintenance.api.v1.admin.users.grid') }}" data-grid="users">

            <thead>
                <tr>
                   <th class="sortable" data-sort="id">ID</th>
                   <th class="sortable" data-sort="email">Email / Username</th>
                   <th class="sortable" data-sort="first_name">First Name</th>
                   <th class="sortable" data-sort="last_name">Last Name</th>
                   <th class="sortable" data-sort="created_at">Created At</th>
               </tr>
           </thead>

            <tbody></tbody>

        </table>

    </div>

    <footer class="panel-footer clearfix text-center">

        {{-- Grid: Pagination --}}
        <div id="users-pagination" data-grid="users"></div>

    </footer>

    @include('admin.users.grid.templates.no-results')
    @include('admin.users.grid.templates.results')
    @include('layouts.partials.grid.templates.pagination', ['grid' => 'users'])
    @include('layouts.partials.grid.templates.filters', ['grid' => 'users'])

</section>

<script>

    $(function()
    {
        $.datagrid('users', '#users-results', '#users-pagination', '#users-filters');
    });

</script>