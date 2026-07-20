<script>

    /*
    |--------------------------------------------------------------------------
    | Format Time
    |--------------------------------------------------------------------------
    */

    function formatTime(time)
    {
        if (!time) {
            return '--:--';
        }

        return time.substring(0, 5);
    }


    /*
    |--------------------------------------------------------------------------
    | Loading Row
    |--------------------------------------------------------------------------
    */

    function loadingRow(columns, text = 'Loading...')
    {
        return `
        <tr>
            <td colspan="${columns}" class="text-center">
                <i class="fa fa-spinner fa-spin"></i>
                ${text}
            </td>
        </tr>
    `;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Row
    |--------------------------------------------------------------------------
    */

    function emptyRow(columns, text)
    {
        return `
        <tr>
            <td colspan="${columns}" class="text-center text-muted">
                ${text}
            </td>
        </tr>
    `;
    }


    /*
    |--------------------------------------------------------------------------
    | Error Row
    |--------------------------------------------------------------------------
    */

    function errorRow(columns, text)
    {
        return `
        <tr>
            <td colspan="${columns}" class="text-center text-danger">
                ${text}
            </td>
        </tr>
    `;
    }


    /*
    |--------------------------------------------------------------------------
    | Show Validation Errors
    |--------------------------------------------------------------------------
    */

    function showErrors(container, errors)
    {
        let html = '';

        $.each(errors, function (key, value) {

            html += `<div>${value[0]}</div>`;

        });

        $(container)
            .removeClass('d-none')
            .html(html);
    }


    /*
    |--------------------------------------------------------------------------
    | Clear Validation Errors
    |--------------------------------------------------------------------------
    */

    function clearErrors(container)
    {
        $(container)
            .addClass('d-none')
            .html('');
    }


    /*
    |--------------------------------------------------------------------------
    | Success Alert
    |--------------------------------------------------------------------------
    */

    function showSuccess(message)
    {
        alert(message);
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Dialog
    |--------------------------------------------------------------------------
    */

    function confirmAction(message)
    {
        return confirm(message);
    }

</script>
