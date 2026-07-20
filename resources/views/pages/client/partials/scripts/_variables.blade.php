<script>

    /*
    |--------------------------------------------------------------------------
    | Selected Records
    |--------------------------------------------------------------------------
    */

    let selectedAreaId = null;

    let selectedBranchId = null;


    /*
    |--------------------------------------------------------------------------
    | AJAX URLs
    |--------------------------------------------------------------------------
    */

    const urls = {

        loadAreas: "{{ route('companies.areas', $company) }}",

        loadBranches: "{{ url('/areas') }}",

        loadAreaGuards: "{{ url('/areas') }}",

        loadBranchGuards: "{{ url('/branches') }}",

        createArea: "{{ route('areas.store') }}",

        createBranch: "{{ route('branches.store') }}",

        assignAreaGuard: "{{ route('areas.assign.guard') }}",

        assignBranchGuard: "{{ route('branches.assign.guard') }}",

        removeAreaGuard: "{{ route('areas.remove.guard') }}",

        removeBranchGuard: "{{ route('branches.remove.guard') }}"

    };

</script>
