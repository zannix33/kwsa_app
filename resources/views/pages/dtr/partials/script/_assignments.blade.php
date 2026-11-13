@push('custom-scripts')
<script>
    let employeeAreas = @json($areas);

    function areaForId(areaId) {
        return employeeAreas.find(area => String(area.id) === String(areaId));
    }

    function fillCompanies(select, area, selectedId = '') {
        select.innerHTML = '<option value="">Select Company</option>';
        (area && area.assigned_companies ? area.assigned_companies : []).forEach(company => {
            const option = document.createElement('option');
            option.value = company.id;
            option.textContent = company.name;
            if (String(company.id) === String(selectedId)) option.selected = true;
            select.appendChild(option);
        });
        select.disabled = !area || !area.assigned_companies || area.assigned_companies.length === 0;
    }

    function fillBranches(select, area, selectedId = '') {
        select.innerHTML = '<option value="">No Branch</option>';
        (area && area.branches ? area.branches : []).forEach(branch => {
            const option = document.createElement('option');
            option.value = branch.id;
            option.textContent = branch.name;
            if (String(branch.id) === String(selectedId)) option.selected = true;
            select.appendChild(option);
        });
    }

    function applyAreaToRow(row, areaId, companyId = '', branchId = '') {
        const area = areaForId(areaId);
        const branch = row.querySelector('.branch-select');
        const company = row.querySelector('.company-select');
        if (branch) fillBranches(branch, area, branchId);
        if (company) fillCompanies(company, area, companyId);
        if (branch && branch.value) {
            if (company) { company.value = ''; company.disabled = true; }
        } else if (company) {
            company.disabled = !area || !area.assigned_companies || area.assigned_companies.length === 0;
        }
    }

    $(document).on('change', '.area-select', function () {
        const row = this.closest('tr');
        applyAreaToRow(row, this.value);
    });

    $(document).on('change', '.branch-select', function () {
        const company = this.closest('tr').querySelector('.company-select');
        if (!company) return;
        if (this.value) { company.value = ''; company.disabled = true; }
        else {
            const area = areaForId(this.closest('tr').querySelector('.area-select').value);
            fillCompanies(company, area);
        }
    });

    $('#user_id').on('change', function () {
        const employeeId = $(this).val();
        employeeAreas = [];
        if (!employeeId) {
            $('.area-select').html('<option value="">Select Area</option>');
            $('.branch-select').html('<option value="">No Branch</option>');
            $('.company-select').html('<option value="">Select Company</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: '/dtr/employees/' + employeeId + '/assignments',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                employeeAreas = response.areas || [];
                populateAreaSelects();
            }
        });
    });

    function populateAreaSelects() {
        $('.area-select').each(function () {
            const current = this.value;
            let html = '<option value="">Select Area</option>';
            employeeAreas.forEach(area => { html += `<option value="${area.id}">${area.name}</option>`; });
            $(this).html(html);
            if (employeeAreas.length === 1) {
                this.value = employeeAreas[0].id;
                applyAreaToRow(this.closest('tr'), this.value);
            } else if (current) {
                this.value = current;
                applyAreaToRow(this.closest('tr'), current);
            }
        });
        let defaultHtml = '<option value="">Select Area</option>';
        employeeAreas.forEach(area => { defaultHtml += `<option value="${area.id}">${area.name}</option>`; });
        $('#default_area').html(defaultHtml);
        if (employeeAreas.length === 1) $('#default_area').val(employeeAreas[0].id).trigger('change');
    }

    $('#default_area').on('change', function () {
        const area = areaForId(this.value);
        fillBranches(document.getElementById('default_branch'), area);
        fillCompanies(document.getElementById('default_company'), area);
        if (area && area.branches && area.branches.length === 1) $('#default_branch').val(area.branches[0].id);
        if (area && area.assigned_companies && area.assigned_companies.length === 1) $('#default_company').val(area.assigned_companies[0].id);
    });

    $('#btnApplyAssignment').on('click', function () {
        const areaId = $('#default_area').val();
        const branchId = $('#default_branch').val();
        const companyId = $('#default_company').val();
        $('.area-select').each(function () {
            this.value = areaId;
            applyAreaToRow(this.closest('tr'), areaId, companyId, branchId);
        });
    });

    $(function () {
        if (employeeAreas.length === 1) {
            const area = employeeAreas[0];
            $('#default_area').val(area.id).trigger('change');
            $('.area-select').each(function () {
                this.value = area.id;
                applyAreaToRow(this.closest('tr'), area.id);
            });
        }
    });
</script>
@endpush
