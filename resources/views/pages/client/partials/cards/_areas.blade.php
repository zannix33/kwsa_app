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

            <table class="table table-hover table-bordered mb-0">

                <thead class="thead-light">

                <tr>
                    <th>Area</th>
                    <th width="120">Rate</th>
                </tr>

                </thead>

                <tbody id="areas-table">

                @forelse($company->areas as $area)

                    <tr class="area-item"
                        data-id="{{ $area->id }}"
                        style="cursor:pointer;">

                        <td>

                            <strong>{{ $area->name }}</strong>

                            @if($area->description)
                                <br>
                                <small class="text-muted">
                                    {{ $area->description }}
                                </small>
                            @endif

                        </td>

                        <td class="text-right">

                            @if($area->rate)

                                {{ number_format($area->payroll_rate,2) }}

                            @else

                                -

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="2"
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
