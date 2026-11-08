<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <strong style="font-size:1.1rem;">Areas</strong>

        <div>

            <button type="button" class="btn btn-info btn-sm" id="btnViewAreaGuards">
                <i class="fa fa-users"></i> View Guards
            </button>

            {{-- Create Area --}}
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#areaModal">

                <i class="fa fa-plus"></i>
                Create

            </button>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table id="client-areas-table" class="table table-hover table-bordered mb-0 w-100">
                <colgroup>
                    <col class="client-area-column">
                    <col class="client-region-column">
                    <col class="client-action-column">
                </colgroup>

                <thead class="thead-light">

                <tr>
                    <th>Area</th>
                    <th>Region</th>
                    <th class="text-center">Action</th>
                </tr>

                </thead>

                <tbody id="areas-table">

                @forelse($company->areas as $area)

                    <tr class="area-item"
                        data-id="{{ $area->id }}"
                        style="cursor:pointer;">

                        <td class="client-area-cell">
                            <strong class="client-area-name">{{ $area->name }}</strong>

                            @if($area->description)
                                <div class="text-muted client-area-description">{{ $area->description }}</div>
                            @endif

                        </td>

                        <td class="client-region-cell">
                            {{ optional($area->region)->code }}
                            @if($area->region) - {{ $area->region->name }} @else - @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-area" style="white-space:nowrap; padding-left:.4rem; padding-right:.4rem;"
                                    data-id="{{ $area->id }}"
                                    data-name="{{ e($area->name) }}"
                                    data-description="{{ e($area->description) }}"
                                    data-region-id="{{ $area->region_id }}">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3"
                            class="text-center text-muted">

                            No Areas Found

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>
/* Explicit column sizing prevents long area descriptions from crossing cells. */
#client-areas-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
}
#client-areas-table .client-area-column { width: 32%; }
#client-areas-table .client-region-column { width: 48%; }
#client-areas-table .client-action-column { width: 20%; }
#client-areas-table th,
#client-areas-table td {
    box-sizing: border-box;
    padding: .65rem .75rem;
    vertical-align: middle;
    white-space: normal !important;
    overflow: hidden;
}
#client-areas-table .client-area-cell,
#client-areas-table .client-region-cell {
    min-width: 0;
    white-space: normal !important;
    overflow-wrap: break-word;
    word-break: normal;
}
#client-areas-table .client-area-name,
#client-areas-table .client-area-description {
    display: block;
    max-width: 100%;
    white-space: normal !important;
    overflow-wrap: anywhere;
    word-break: break-word;
    line-height: 1.3;
}
#client-areas-table .client-area-name { font-size: 1rem; }
#client-areas-table .client-area-description { margin-top: .15rem; }
#client-areas-table .client-region-cell { line-height: 1.35; }
#client-areas-table .btn-edit-area { white-space: nowrap; }
@media (max-width: 576px) {
    #client-areas-table .client-area-column { width: 38%; }
    #client-areas-table .client-region-column { width: 42%; }
    #client-areas-table .client-action-column { width: 20%; }
    #client-areas-table th, #client-areas-table td { padding: .45rem .4rem; }
}
</style>
