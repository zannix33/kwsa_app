@push('custom-scripts')

<script>

    let employeeAreas=@json($areas);

    $(document).on(
        'change',
        '.area-select',

        function(){

            let row=$(this).closest('tr');
            let branch=row.find('.branch-select');

            branch.empty();
            branch.append(
                '<option value="">No Branch</option>'
            );

            let areaId=$(this).val();
            let area=employeeAreas.find(function(item){
                return item.id==areaId;
            });

            if(!area)
                return;
            area.branches.forEach(function(branchItem){
                branch.append(
                    `<option value="${branchItem.id}">
                    ${branchItem.name}
                </option>`
                );
            });
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Employee Changed
    |--------------------------------------------------------------------------
    |
    | Loads all assigned Areas and Branches of the selected employee.
    |
    */

    $('#user_id').on('change', function () {

        let employeeId = $(this).val();

        employeeAreas = [];

        if (!employeeId) {

            $('.area-select').html(
                '<option value="">Select Area</option>'
            );

            $('.branch-select').html(
                '<option value="">No Branch</option>'
            );
            return;
        }

        $.ajax({

            url: '/employees/' + employeeId + '/assignments',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                employeeAreas = response.areas;
                populateAreaSelects();
            }
        });

    });


    /*
    |--------------------------------------------------------------------------
    | Populate every Area Select
    |--------------------------------------------------------------------------
    */

    function populateAreaSelects()
    {

        $('.area-select').each(function () {

            let html = '<option value="">Select Area</option>';

            employeeAreas.forEach(function (area) {

                html += `
                <option value="${area.id}">
                    ${area.name}
                </option>
            `;

            });

            $(this).html(html);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Area Changed
    |--------------------------------------------------------------------------
    |
    | Load Branches only for the selected Area.
    |
    */

    $(document).on('change', '.area-select', function () {

        let areaId = $(this).val();

        let row = $(this).closest('tr');

        let branchSelect = row.find('.branch-select');

        branchSelect.html(
            '<option value="">No Branch</option>'
        );

        if (!areaId)
            return;

        let area = employeeAreas.find(function(item){

            return item.id == areaId;

        });

        if (!area)
            return;

        area.branches.forEach(function(branch){

            branchSelect.append(

                `<option value="${branch.id}">
                ${branch.name}
            </option>`

            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Apply Default Assignment
    |--------------------------------------------------------------------------
    */

    $('#btnApplyAssignment').on('click', function () {

        let area = $('#default_area').val();

        let branch = $('#default_branch').val();

        $('.area-select').each(function(){

            $(this).val(area).trigger('change');

        });

        setTimeout(function(){

            $('.branch-select').each(function(){

                $(this).val(branch);

            });

        },100);

    });



    /*
    |--------------------------------------------------------------------------
    | Default Area Changed
    |--------------------------------------------------------------------------
    |
    | Update the Branch dropdown at the top.
    |
    */

    $('#default_area').on('change', function(){

        let areaId = $(this).val();

        let html = '<option value="">No Branch</option>';

        let area = employeeAreas.find(function(item){

            return item.id == areaId;

        });

        if(area){

            area.branches.forEach(function(branch){

                html += `
                <option value="${branch.id}">
                    ${branch.name}
                </option>
            `;

            });

        }

        $('#default_branch').html(html);

    });

</script>
@endpush
