<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <strong>Areas</strong>

        <div>

            {{-- Assign Guard --}}
            <button type="button"
                    class="btn btn-success btn-sm"
                    id="btnAssignAreaGuard">

                <i class="fa fa-user-plus"></i>
                Assign Guard

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

            <table class="table table-hover table-bordered mb-0 w-100" style="table-layout:fixed;">

                <thead class="thead-light">

                <tr>
                    <th style="width:22%;">Area</th>
                    <th style="width:58%;">Region</th>
                    <th style="width:20%;" class="text-center">Action</th>
                </tr>

                </thead>

                <tbody id="areas-table">

                @forelse($company->areas as $area)

                    <tr class="area-item"
                        data-id="{{ $area->id }}"
                        style="cursor:pointer;">

                        <td style="overflow-wrap:anywhere;">
                            <strong>{{ $area->name }}</strong>

                            @if($area->description)
                                <br>
                                <small class="text-muted">
                                    {{ $area->description }}
                                </small>
                            @endif

                        </td>

                        <td style="overflow-wrap:anywhere;">
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
