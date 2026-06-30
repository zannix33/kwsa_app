<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>{{ $summary['total'] ?? 0 }}</h3>

                <p>Total Inspections</p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>{{ $summary['passed'] ?? 0 }}</h3>

                <p>Passed</p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>{{ $summary['failed'] ?? 0 }}</h3>

                <p>Failed</p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ $summary['due'] ?? 0 }}</h3>

                <p>Due Inspection</p>

            </div>

        </div>

    </div>

</div>
