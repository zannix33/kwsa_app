<script>

    /*
    |--------------------------------------------------------------------------
    | Load Areas
    |--------------------------------------------------------------------------
    |
    | Loads all Areas belonging to the current Company.
    |
    */

    function loadAreas(selectedId = null)
    {

        $('#areas-table').html(
            loadingRow(2, 'Loading areas...')
        );

        $.ajax({

            url: "{{ route('companies.areas', $company->id) }}",

            type: "GET",

            dataType: "json",

            success:function(areas){

                let html = '';

                if(areas.length === 0){

                    $('#areas-table').html(
                        emptyRow(2,'No Areas Found.')
                    );

                    return;

                }

                $.each(areas,function(index,area){

                    let active = '';

                    if(selectedId == area.id){

                        active = 'table-primary';

                        selectedAreaId = area.id;

                    }

                    html += `

            <tr class="area-item ${active}"
                data-id="${area.id}"
                style="cursor:pointer;">

                <td>

                    <strong>

                        ${area.name}

                    </strong>

                    <br>

                    <small class="text-muted">

                        ${area.description ?? ''}

                    </small>

                </td>

                <td class="text-right">

                    ${area.payroll_rate
                        ? Number(area.payroll_rate).toFixed(2)
                        : '-'}

                </td>

            </tr>

        `;

                });

                $('#areas-table').html(html);

                /*
                |--------------------------------------------------------------------------
                | Automatically load selected Area
                |--------------------------------------------------------------------------
                */

                if(selectedAreaId){

                    loadBranches(selectedAreaId);

                    loadAreaGuards(selectedAreaId);

                }

            },

            error:function(){

                $('#areas-table').html(
                    errorRow(2, 'Unable to load Areas.')
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    $(function(){

        loadAreas();

    });

</script>
