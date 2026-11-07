<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>

                    {{ $summary['total'] ?? 0 }}

                </h3>

                <p>

                    Total Jobs

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    {{ $summary['completed'] ?? 0 }}

                </h3>

                <p>

                    Completed

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ $summary['pending'] ?? 0 }}

                </h3>

                <p>

                    Pending

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>

                    ₱ {{ number_format($summary['cost'] ?? 0,2) }}

                </h3>

                <p>

                    Maintenance Cost

                </p>

            </div>

        </div>

    </div>

</div>
